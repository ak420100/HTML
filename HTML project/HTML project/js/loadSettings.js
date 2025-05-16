fetch('settings.php')
  .then(res => res.json())
  .then(data => {
    if (data.error) {
      console.error(data.error);
      return;
    }

    if (data.dark_mode == 1) {
      document.body.classList.add("dark-mode");
    }
  })
  .catch(error => console.error('Error loading settings:', error));
