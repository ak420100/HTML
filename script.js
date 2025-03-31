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
document.getElementById("signupForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const username = document.getElementById("username").value;
    const email    = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    fetch("signup.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            username: username,
            email: email,
            password: password
        })
    })
    .then(res => res.json())
    .then(data => {
        const responseMessage = document.getElementById("responseMessage");
        if (data.error) {
            responseMessage.style.color = "red";
            responseMessage.textContent = data.error;
        } else {
            responseMessage.style.color = "green";
            responseMessage.textContent = data.success;

            // Redirect after a delay
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
