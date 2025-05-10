// ✅ Global navigation function (must be outside DOMContentLoaded if using inline onclick)
function goToPage(page) {
    window.location.href = page;
}

document.addEventListener("DOMContentLoaded", function () {
    // ✅ Login Form
    const loginForm = document.getElementById("LoginPage");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            fetch("login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({ email, password })
            })
            .then(res => res.json())
            .then(data => {
                const responseMessage = document.getElementById("responseMessage");

                if (data.error) {
                    responseMessage.style.color = "red";
                    responseMessage.textContent = data.error;
                } else if (data.success) {
                    responseMessage.style.color = "green";
                    responseMessage.textContent = data.success;
                    setTimeout(() => {
                        window.location.href = "index.html";
                    }, 1500);
                }
            })
            .catch(err => {
                console.error("Login error:", err);
                document.getElementById("responseMessage").textContent = "Something went wrong.";
            });
        });
    }

    // ✅ Signup Form
    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        signupForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const username = document.getElementById("username").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            fetch("signup.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({ username, email, password })
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById("responseMessage");

                if (data.error) {
                    msg.style.color = "red";
                    msg.textContent = data.error;
                } else {
                    msg.style.color = "green";
                    msg.textContent = data.success;
                    setTimeout(() => {
                        window.location.href = "friend.html";
                    }, 1500);
                }
            })
            .catch(err => {
                console.error("Signup error:", err);
                document.getElementById("responseMessage").textContent = "Something went wrong.";
            });
        });
    }

    // ✅ Friends List Page
    const friendForm = document.getElementById("friendForm");
    const friendsList = document.getElementById("friendsList");
    const emailError = document.getElementById("emailError");

    if (friendForm && friendsList) {
        function attachRemoveEventListeners() {
            document.querySelectorAll('.removeFriend').forEach(button => {
                button.addEventListener('click', function () {
                    const friendId = this.getAttribute('data-id');
                    fetch(`friendlist.php?id=${friendId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(() => {
                        fetchFriends();
                    });
                });
            });
        }

        friendForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(friendForm);

            if (emailError) emailError.style.display = 'none';

            fetch('friendlist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error && emailError) {
                    emailError.textContent = data.error;
                    emailError.style.display = 'block';
                } else {
                    fetchFriends();
                    friendForm.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        function fetchFriends() {
            fetch('friendlist.php')
            .then(res => res.json())
            .then(data => {
                friendsList.innerHTML = '';
                data.forEach(friend => {
                    const li = document.createElement('li');
                    li.textContent = friend.name;
                    friendsList.appendChild(li);
                });
                attachRemoveEventListeners();
            });
        }

        fetchFriends();
    }

    // ✅ Dark Mode Toggle
    fetch('darkmode.php')
    .then(res => res.json())
    .then(data => {
        if (data.dark === true) {
            document.body.classList.add('dark-mode');
        }
    });
});


const habitForm = document.getElementById("habitForm"); // make sure your form has this ID
if (habitForm) {
    habitForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(habitForm);

        fetch("createhab.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById("habitMessage"); // optional message div
            if (data.success) {
                msg.textContent = "Habit added successfully!";
                msg.style.color = "green";
                habitForm.reset();
            } else {
                msg.textContent = data.error || "Something went wrong.";
                msg.style.color = "red";
            }
        })
        .catch(err => {
            console.error("Habit creation error:", err);
        });
    });
}

// ✅ rewards page
document.addEventListener('DOMContentLoaded', () => {
    const coinBalanceElement = document.getElementById('coin-balance');
    const showThemesButton = document.getElementById('show-themes-button');
    const colorThemesDiv = document.getElementById('color-themes');
    const themeButtons = document.querySelectorAll('.theme-button');

    let coinBalance = parseInt(localStorage.getItem('coinBalance')) || 1000;
    coinBalanceElement.textContent = coinBalance;

    // Show/Hide Themes
    showThemesButton.addEventListener('click', () => {
        colorThemesDiv.style.display = colorThemesDiv.style.display === 'none' ? 'block' : 'none';
    });

    // Theme Selection
    themeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const theme = button.dataset.theme;
            const cost = parseInt(button.dataset.cost);

            if (coinBalance >= cost) {
                coinBalance -= cost;
                coinBalanceElement.textContent = coinBalance;

                applyTheme(theme);

                localStorage.setItem('coinBalance', coinBalance);
                localStorage.setItem('selectedTheme', theme);

                alert(`Theme ${theme} applied!`);
            } else {
                alert("Not enough coins!");
            }
        });
    });

    // Apply Previously Selected Theme
    const selectedTheme = localStorage.getItem('selectedTheme');
    if (selectedTheme) {
        applyTheme(selectedTheme);
    }

    // Function to Apply Theme
    function applyTheme(theme) {
        let backgroundColor, buttonColor;

        switch (theme) {
            case 'pink':
                backgroundColor = 'lightpink';
                buttonColor = 'deeppink';
                break;
            case 'blue':
                backgroundColor = 'lightblue';
                buttonColor = 'darkblue';
                break;
            case 'green':
                backgroundColor = 'lightgreen';
                buttonColor = 'darkgreen';
                break;
            case 'purple':
                backgroundColor = 'plum';
                buttonColor = 'purple';
                break;
            case 'orange':
                backgroundColor = 'orange';
                buttonColor = 'darkorange';
                break;
            case 'gray':
                backgroundColor = 'lightgray';
                buttonColor = 'gray';
                break;
            default:
                backgroundColor = '#4facfe';
                buttonColor = '#007bff';
        }

        document.body.style.background = `linear-gradient(to right, ${backgroundColor}, ${backgroundColor})`; // Set background gradient
        document.querySelectorAll('button').forEach(btn => {
            btn.style.backgroundColor = buttonColor;
        });
        document.querySelector('header').style.backgroundColor = buttonColor;
        document.querySelectorAll('.habit-block').forEach(block => {
            block.style.backgroundColor = buttonColor;
        });
    }
});

document.querySelector('form').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent the form from submitting normally

    const formData = new FormData(this);
    const response = await fetch(this.action, {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    // Clear any previous error messages
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    if (result.error) {
        const emailInput = document.querySelector('input[name="friendEmail"]');
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.style.color = 'red';
        errorMessage.style.fontSize = '0.9em';
        errorMessage.textContent = result.error;

        // Insert the error message after the email input
        emailInput.parentNode.insertBefore(errorMessage, emailInput.nextSibling);
    } else if (result.success) {
        alert('Friend request sent successfully!');
        location.reload(); // Reload the page or handle success as needed
    }
});


//rewards page 

document.addEventListener('DOMContentLoaded', () => {
    const coinBalanceElement = document.getElementById('coin-balance');
    const themeButtons = document.querySelectorAll('.theme-button');

    // Initialize coin balance (you can replace this with a server-side value if needed)
    let coinBalance = parseInt(localStorage.getItem('coinBalance')) || 1000;
    coinBalanceElement.textContent = coinBalance;

    // Theme Selection
    themeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const theme = button.dataset.theme;
            const cost = parseInt(button.dataset.cost);

            if (coinBalance >= cost) {
                // Deduct coins and update balance
                coinBalance -= cost;
                coinBalanceElement.textContent = coinBalance;

                // Apply the selected theme
                applyTheme(theme);

                // Save the updated balance and theme in localStorage
                localStorage.setItem('coinBalance', coinBalance);
                localStorage.setItem('selectedTheme', theme);

                alert(`Theme "${theme}" applied!`);
            } else {
                alert("Not enough coins!");
            }
        });
    });

    // Apply Previously Selected Theme
    const selectedTheme = localStorage.getItem('selectedTheme');
    if (selectedTheme) {
        applyTheme(selectedTheme);
    }

    // Function to Apply Theme
    function applyTheme(theme) {
        let backgroundColor, buttonColor;

        switch (theme) {
            case 'pink':
                backgroundColor = 'lightpink';
                buttonColor = 'deeppink';
                break;
            case 'blue':
                backgroundColor = 'lightblue';
                buttonColor = 'darkblue';
                break;
            case 'green':
                backgroundColor = 'lightgreen';
                buttonColor = 'darkgreen';
                break;
            case 'purple':
                backgroundColor = 'plum';
                buttonColor = 'purple';
                break;
            case 'orange':
                backgroundColor = 'orange';
                buttonColor = 'darkorange';
                break;
            case 'gray':
                backgroundColor = 'lightgray';
                buttonColor = 'gray';
                break;
            default:
                backgroundColor = '#ffffff';
                buttonColor = '#000000';
        }

        // Apply the theme to the website
        document.body.style.background = backgroundColor;
        document.querySelectorAll('button').forEach(btn => {
            btn.style.backgroundColor = buttonColor;
        });
        document.querySelector('header')?.style.backgroundColor = buttonColor;
    }
});