document.addEventListener("DOMContentLoaded", () => {
    fetch('rewards.php')
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }
        document.getElementById('coin-balance').textContent = data.coins;
        // Apply saved theme (optional if user already has one)
        applyTheme(data.theme);
      });
  
    document.querySelectorAll('.theme-button').forEach(button => {
      button.addEventListener('click', () => {
        const theme = button.dataset.theme;
        const cost = parseInt(button.dataset.cost);
  
        fetch('rewards.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ theme, cost })
        })
          .then(res => res.json())
          .then(result => {
            if (result.success) {
              document.getElementById('coin-balance').textContent = result.new_balance;
              applyTheme(theme);
              alert(result.success);
            } else {
              alert(result.error);
            }
          });
      });
    });
  });
  
  function applyTheme(theme) {
    const body = document.body;
    body.classList.remove("theme-pink", "theme-blue", "theme-green", "theme-purple", "theme-orange", "theme-gray");
    if (theme) {
      body.classList.add("theme-" + theme);
    }
    console.log("Applying theme:", theme);
    console.log("New class added:", "theme-" + theme);

  }
  