$(document).ready(function() {
    const usertoken = localStorage.getItem('userToken');

    if (usertoken) {
        
        $.ajax({
            url: 'php/session_login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                usertoken: usertoken
            }),
            success: function(sessionResponse) {
                if (sessionResponse.success) {
                    window.location.href = 'profile.html';
                }
            }
        });
    }

    $('#signupForm').on('submit', function(e) {
        e.preventDefault();
        
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        
        // Clear previous alerts
        $('#alertMessage').html('');
        
        // Validation
        if (password !== confirmPassword) {
            showAlert('Passwords do not match!', 'danger');
            return;
        }
        
        if (password.length < 6) {
            showAlert('Password must be at least 6 characters long!', 'danger');
            return;
        }
        
        // Disable button
        $('#signupBtn').prop('disabled', true).text('Creating Account...');
        
        // AJAX request
        $.ajax({
            url: 'php/signup.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                name: name,
                email: email,
                password: password
            }),
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#signupForm')[0].reset();
                    
                    // Redirect to login after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                    $('#signupBtn').prop('disabled', false).text('Sign Up');
                }
            },
            error: function(xhr, status, error) {
                showAlert('An error occurred. Please try again.', 'danger');
                $('#signupBtn').prop('disabled', false).text('Sign Up');
            }
        });
    });
    
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertMessage').html(alertHtml);
    }
});