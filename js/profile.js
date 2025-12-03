$(document).ready(function() {
    const usertoken = localStorage.getItem('userToken');
    
    // Check if user is logged in
    if (!usertoken) {
        window.location.href = 'login.html';
        // return;
    }
    
    // Load profile data
    loadProfile();
    
    // Logout functionality
    $('#logoutBtn').on('click', function() {
        $.ajax({
            url: 'php/session_logout.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                usertoken: usertoken
            }),
            success: function(sessionResponse) {
                if (sessionResponse.success) {
                    localStorage.removeItem('userToken');
                    window.location.href = 'login.html';
                }
            }
        });
    });
    
    // Update profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });
    
    function loadProfile() {
        $.ajax({
            url: 'php/get_profile.php',
            type: 'POST',
            data: JSON.stringify({
                usertoken: usertoken
            }),
            success: function(response) {
                if (response.success) {
                    // Display account info
                    $('#displayUsername').text(response.username);
                    $('#username').text(response.username);
                    $('#email').text(response.email);
                    
                    // Fill profile form
                    $('#age').val(response.profile.age);
                    $('#dob').val(response.profile.dob);
                    $('#contact').val(response.profile.contact);
                    $('#address').val(response.profile.address);
                    $('#bio').val(response.profile.bio);
                }
            },
            error: function(xhr, status, error) {
                showAlert('Failed to load profile data.', 'danger');
            }
        });
    }
    
    function updateProfile() {
        const profileData = {
            usertoken: usertoken,
            age: $('#age').val(),
            dob: $('#dob').val(),
            contact: $('#contact').val(),
            address: $('#address').val(),
            bio: $('#bio').val()
        };
        
        // Disable button
        $('#updateBtn').prop('disabled', true).text('Updating...');
        
        $.ajax({
            url: 'php/update_profile.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(profileData),
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                } else {
                    if (response.message.includes('session')) {
                        showAlert('Session expired. Please login again.', 'danger');
                        setTimeout(function() {
                            localStorage.clear();
                            window.location.href = 'login.html';
                        }, 2000);
                    } else {
                        showAlert(response.message, 'danger');
                    }
                }
                $('#updateBtn').prop('disabled', false).text('Update Profile');
            },
            error: function(xhr, status, error) {
                showAlert('Failed to update profile.', 'danger');
                $('#updateBtn').prop('disabled', false).text('Update Profile');
            }
        });
    }
    
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertMessage').html(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('#alertMessage').html('');
        }, 5000);
    }
});