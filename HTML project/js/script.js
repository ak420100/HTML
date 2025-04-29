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

    if (friendForm && friendsList) {
        // Function to attach event listeners to remove buttons
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

        // Submit event for adding a friend
        friendForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(friendForm);

            fetch('friendlist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(() => {
                fetchFriends();
                friendForm.reset();
            });
        });

        // Initial fetch
        function fetchFriends() {
            fetch('friendlist.php')
            .then(res => res.json())
            .then(data => {
                // Update DOM with data (implement this as needed)
                friendsList.innerHTML = ''; // Clear list
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

    // ✅ Dark Mode Toggle (optional)
    fetch('darkmode.php')
    .then(res => res.json())
    .then(data => {
        if (data.dark === true) {
            document.body.classList.add('dark-mode');
        }
    });

    // ✅ Optional: navigation helper
    window.goToPage = function (page) {
        window.location.href = page;
    };
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
