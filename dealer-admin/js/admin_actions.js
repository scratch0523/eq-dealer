function logout() {

    $.ajax({
        url: 'admin_actions/admin_action.php',
        type: 'POST',
        data: {
            logout_request: 'logout_request'
        },
        success: function (response) {
            alert(response);
            if (response == 'logout-successfully') {
                window.location = 'login.html';
            }
        }
    });
}