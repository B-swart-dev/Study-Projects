// Declaring xhr variable for AJAX
const xhr = new XMLHttpRequest();

// Set up event listener for form submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const username = form.user.value;
    const password = form.password.value;
    const errorElement = document.getElementById('error');

    // Clears any previous error message
    errorElement.textContent = '';
    errorElement.style.textDecoration = 'none';

    // Perform validation
    let fail = "";
    fail += validateUsername(username);
    fail += validatePassword(password);

    if (fail !== "") {
        alert(fail);
        return;
    }

    xhr.open('POST', 'login_MW.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Handle response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = 'login_success.php';
                    } else {
                        errorElement.textContent = response.message;
                        errorElement.style.textDecoration = 'underline';
                    }
                } catch (error) {
                    errorElement.textContent = 'Invalid response from server. Please try again later.';
                    errorElement.style.textDecoration = 'underline';
                }
            } else {
                errorElement.textContent = 'An error occurred. Please try again.';
                errorElement.style.textDecoration = 'underline';
            }
        }
    };

    // Send request
    const params = `user=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
    xhr.send(params);
});

// Validation functions
function validateUsername(username) {
    if (username === "") return "No username or email was entered.\n";
    return "";
}

function validatePassword(password) {
    if (password === "") return "No password was entered.\n";
    else if (password.length < 6) return "Passwords must be at least 6 characters.\n";
    return "";
}