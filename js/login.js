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
    

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const username = $('#username').val().trim();
        const password = $('#password').val();
        
        // Clear previous alerts
        $('#alertMessage').html('');
        
        // Validation
        if (!username || !password) {
            showAlert('Please fill in all fields!', 'danger');
            return;
        }
        
        // Disable button
        $('#loginBtn').prop('disabled', true).text('Logging in...');
        
        // AJAX request
        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                password: password
            }),
            success: function(response) {
                if (response.success) {
                    // Store token in localStorage
                    localStorage.setItem('userToken', response.usertoken);
                    
                    showAlert('Login successful! Redirecting...', 'success');
                    
                    // Redirect to profile page
                    setTimeout(function() {
                        window.location.href = 'profile.html';
                    }, 1500);
                } else {
                    showAlert(response.message, 'danger');
                    $('#loginBtn').prop('disabled', false).text('Login');
                }
            },
            error: function(xhr, status, error) {
                showAlert('An error occurred. Please try again.', 'danger');
                $('#loginBtn').prop('disabled', false).text('Login');
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