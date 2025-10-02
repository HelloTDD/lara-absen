function applyTheme(selectedIcon, selectedTheme) {
  const themeMode = document.getElementById("themeMode");
  themeMode.className = `mdi ${selectedIcon}`;

  const body = document.getElementById("body");
  const appCss = document.querySelector(
    'link[href*="app.min.css"], link[href*="app-dark.min.css"]'
  );

  if (selectedTheme === "light") {
    body.removeAttribute("class");
    body.setAttribute("data-bs-theme", "light");

    if (appCss) appCss.setAttribute("href", "/assets/css/app.min.css");
  } else if (selectedTheme === "dark") {
    body.removeAttribute("data-bs-theme");
    body.setAttribute("data-bs-theme", "dark");
    body.className = "menuitem-active";

    if (appCss) appCss.setAttribute("href", "/assets/css/app-dark.min.css");
  }

  localStorage.setItem(
    "themeMode",
    JSON.stringify({ icon: selectedIcon, theme: selectedTheme })
  );
}

document.querySelectorAll(".theme-Mode").forEach((item) => {
  item.addEventListener("click", function (e) {
    e.preventDefault();

    const selectedIcon = this.getAttribute("data-icon");
    const selectedTheme = this.getAttribute("data-theme");

    applyTheme(selectedIcon, selectedTheme);
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("themeMode");
  if (savedTheme) {
    const { icon, theme } = JSON.parse(savedTheme);
    applyTheme(icon, theme);
  }
});
