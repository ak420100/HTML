document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("signupForm").addEventListener("submit", async function (event) {
        event.preventDefault();

        const formData = {
            username: document.getElementById("username").value,
            email: document.getElementById("email").value,
            password: document.getElementById("password").value
        };

        try {
            const response = await fetch("signup.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            const responseMessage = document.getElementById("responseMessage");

            if (result.error) {
                responseMessage.style.color = "red";
                responseMessage.textContent = result.error;
            } else if (result.success) {
                responseMessage.style.color = "green";
                responseMessage.textContent = result.success;

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "friend.html";
                }, 2000);
            }
        } catch (error) {
            console.error("Fetch Error:", error);
        }
    });
});


// Function to navigate to another page
function goToPage(page) {
    window.location.href = page;
}
