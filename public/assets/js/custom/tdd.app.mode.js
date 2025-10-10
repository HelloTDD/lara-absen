function applyTheme(selectedIcon, selectedTheme) {
  const themeMode = document.getElementById("themeMode");
  themeMode.className = `mdi ${selectedIcon}`;

  const body = document.getElementById("body");
  const appCss = document.querySelector(
    'link[href*="app.min.css"], link[href*="app-dark.min.css"]'
  );
  const isMobileOrTablet = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)
    || window.innerWidth <= 1024;
  if (selectedTheme === "light") {
    body.removeAttribute("class");
    body.setAttribute("data-bs-theme", "light");
    const isMobileOrTablet =
      /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ||
      window.innerWidth <= 1024;

    if (isMobileOrTablet) {
      console.log("Mobile or tablet detected");
      // Tambahkan class tanpa menimpa yang sudah ada
      body.classList.add("enlarge-menu", "enlarge-menu-all");
    }
    if (appCss) appCss.setAttribute("href", "/assets/css/app.min.css");
  } else if (selectedTheme === "dark") {
    body.setAttribute("data-bs-theme", "dark");
    const isMobileOrTablet =
      /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ||
      window.innerWidth <= 1024;

    if (isMobileOrTablet) {
      console.log("Mobile or tablet detected");
      // Tambahkan class tanpa menimpa yang sudah ada
      body.classList.add("enlarge-menu", "enlarge-menu-all");
    }
    // Jangan overwrite class lain, cukup tambah
    body.classList.add("menuitem-active");

    if (appCss) appCss.setAttribute("href", "/assets/css/app-dark.min.css");
  }


  localStorage.setItem(
    "themeMode",
    JSON.stringify({ icon: selectedIcon, theme: selectedTheme })
  );

}
document.addEventListener("DOMContentLoaded", function () {
  const isMobileOrTablet =
    /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent) ||
    window.innerWidth <= 1024;

  if (isMobileOrTablet) {
    console.log("Mobile or tablet detected");
    // Tambahkan class tanpa menimpa yang sudah ada
    body.classList.add("enlarge-menu", "enlarge-menu-all");
  }
});
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
