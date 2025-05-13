document.addEventListener("DOMContentLoaded", () => {
  fetch('get_theme.php')
    .then(res => res.json())
    .then(data => {
      if (data.theme) {
        const body = document.body;
        body.classList.remove(
          "theme-pink", "theme-blue", "theme-green", 
          "theme-purple", "theme-orange", "theme-gray"
        );
        body.classList.add("theme-" + data.theme);
      }
    });
});

document.addEventListener("DOMContentLoaded", () => {
  fetch('get_theme.php')
    .then(res => res.json())
    .then(data => {
      if (data.theme) {
        applyTheme(data.theme);
      }
    });
});

function applyTheme(theme) {
  const body = document.body;

  // 🔄 Remove ALL theme-related classes first
  body.classList.remove(
    "theme-pink", "theme-blue", "theme-green",
    "theme-purple", "theme-orange", "theme-gray",
    "blue-theme", "red-theme", "green-theme" // any extra legacy ones too
  );

  // ✅ Apply the selected one
  if (theme) {
    body.classList.add("theme-" + theme);
  }

  console.log("🎨 Applied theme:", "theme-" + theme);
}

