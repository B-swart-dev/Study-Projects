// Declaring xhr variable i am using AJAX due to my incorrect user not function correctly
//https://www.youtube.com/watch?v=Ju5FGcyifEA
const xhr = new XMLHttpRequest();

// Set up event listener for form submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorElement = document.getElementById('error');

    // Clear any previous error message
    errorElement.textContent = '';
    errorElement.style.textDecoration = 'none';

    // Check for empty username and password
    if (username === "" && password === "") {
        errorElement.textContent = 'Username and Password are required';
        errorElement.style.textDecoration = 'underline';
        return;
    } else if (username === "") {
        errorElement.textContent = 'Username is required';
        errorElement.style.textDecoration = 'underline';
        return;
    } else if (password === "") {
        errorElement.textContent = 'Password is required';
        errorElement.style.textDecoration = 'underline';
        return;
    }

    // Set up XMLHttpRequest
    xhr.open('POST', 'login_olms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Handle response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Response:', xhr.responseText); 
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log('Redirecting to main_page.php...'); 
                        window.location.href = 'main_page.php?r=' + encodeURIComponent(response.random_code);
                    } else {
                        errorElement.textContent = response.message;
                        errorElement.style.textDecoration = 'underline';
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    errorElement.textContent = 'Invalid response from server. Please try again later.';
                    errorElement.style.textDecoration = 'underline';
                }
            } else {
                console.error('Error occurred during login:', xhr.status); 
                errorElement.textContent = 'An error occurred. Please try again.';
                errorElement.style.textDecoration = 'underline';
            }
        }
    };

    // Send request
    const params = `user=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
    xhr.send(params);
});
