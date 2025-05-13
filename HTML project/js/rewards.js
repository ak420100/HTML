document.addEventListener("DOMContentLoaded", () => {
    // Load coin balance and theme
    fetch('rewards.php')
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }
  
        document.getElementById('coin-balance').textContent = data.coins;
  
        const purchasedThemes = data.purchased_themes || [];
        document.querySelectorAll('.theme-button').forEach(button => {
          const theme = button.dataset.theme;
          if (purchasedThemes.includes(theme)) {
            button.dataset.cost = 0;
            button.textContent = `${theme.charAt(0).toUpperCase() + theme.slice(1)} (Unlocked)`;
          }
        });
  
        applyTheme(data.theme);
      });
  
    // Theme button click logic
    document.querySelectorAll('.theme-button').forEach(button => {
      button.addEventListener('click', () => {
        const theme = button.dataset.theme;
        const cost = parseInt(button.dataset.cost);
  
        // Always POST to update theme
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
  
              // Update button if it was a purchase
              if (cost > 0) {
                button.dataset.cost = 0;
                button.textContent = `${theme.charAt(0).toUpperCase() + theme.slice(1)} (Unlocked)`;
              }
  
              alert(result.success);
            } else {
              alert(result.error);
            }
          });
      });
    });
  
    // Claim coins button
    const claimBtn = document.getElementById('claim-coins-btn');
    const msg = document.getElementById('claim-message');
  
    if (claimBtn) {
      claimBtn.addEventListener('click', () => {
        fetch('claim_coins.php')
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              msg.textContent = data.success;
              document.getElementById('coin-balance').textContent = data.new_balance;
              claimBtn.disabled = true;
            } else {
              msg.textContent = data.error;
              claimBtn.disabled = true;
            }
          });
      });
    }
  });
  
  // Theme application function
  function applyTheme(theme) {
    const body = document.body;
    body.classList.remove(
      "theme-pink", "theme-blue", "theme-green",
      "theme-purple", "theme-orange", "theme-gray"
    );
    if (theme) {
      body.classList.add("theme-" + theme);
    }
    console.log("Applying theme:", theme);
  }
  