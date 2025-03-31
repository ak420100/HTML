// ✅ Login submission
document.addEventListener("DOMContentLoaded", function () {
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
                body: new URLSearchParams({
                    email: email,
                    password: password
                })
            })
            .then(res => res.json())
            .then(data => {
                const responseMessage = document.getElementById("responseMessage");

                console.log("Login response:", data); // ✅ Debug

                if (data.error) {
                    responseMessage.style.color = "red";
                    responseMessage.textContent = data.error;
                } else if (data.success) {
                    responseMessage.style.color = "green";
                    responseMessage.textContent = data.success;

                    // ✅ Redirect after a short delay
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

    // ✅ Dark mode fetch
    fetch('darkmode.php')
      .then(res => res.json())
      .then(data => {
          if (data.dark === true) {
              document.body.classList.add('dark-mode');
          }
      });

    // ✅ Optional: helper for page navigation
    window.goToPage = function (page) {
        window.location.href = page;
    };
});
document.addEventListener("DOMContentLoaded", function () {
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
                body: new URLSearchParams({
                    username: username,
                    email: email,
                    password: password
                })
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
});


//friends list page back end

document.addEventListener("DOMContentLoaded", function() {
    const friendForm = document.getElementById("friendForm");
    const friendsList = document.getElementById("friendsList");

    // Function to fetch and display friends
    function fetchFriends() {
        fetch('friendlist.php')
            .then(response => response.json())
            .then(data => {
                friendsList.innerHTML = '';
                data.friends.forEach(friend => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${friend.name}</td>
                        <td>${friend.email}</td>
                        <td>${friend.habits}</td>
                        <td><button class="removeFriend" data-id="${friend.id}">X</button></td>
                    `;
                    friendsList.appendChild(row);
                });
                attachRemoveEventListeners();
            });
    }

    // Function to attach event listeners to remove buttons
    function attachRemoveEventListeners() {
        document.querySelectorAll('.removeFriend').forEach(button => {
            button.addEventListener('click', function() {
                const friendId = this.getAttribute('data-id');
                fetch(`friendlist.php?id=${friendId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    fetchFriends();
                });
            });
        });
    }

    // Submit event for the form
    friendForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(friendForm);
        fetch('friendlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fetchFriends();
            friendForm.reset();
        });
    });

    // Initial fetch to display friends
    fetchFriends();
});