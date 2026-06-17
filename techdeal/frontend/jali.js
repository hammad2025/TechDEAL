// ===============================
// ▶️ Gestion de la bannière cookies
// ===============================
function gererBanniereCookies() {
    const banner = document.getElementById("cookieBanner");
    const accept = document.getElementById("acceptCookies");
    const skip = document.getElementById("skipCookies");
  
    if (!banner || !accept || !skip) {
        console.error("Un ou plusieurs éléments de la bannière de cookies sont introuvables.");
        return;
    }
  
    const consent = localStorage.getItem("cookieConsent");
  
    if (!consent) {
        banner.style.display = "block";
    }
  
    accept.addEventListener("click", () => {
        localStorage.setItem("cookieConsent", "accepted");
        banner.style.display = "none";
    });
  
    skip.addEventListener("click", (e) => {
        e.preventDefault();
        localStorage.setItem("cookieConsent", "refused");
        banner.style.display = "none";
    });
  }
// 📜 Gestion du menu utilisateur
document.addEventListener("DOMContentLoaded", () => {
    const userMenu = document.getElementById("userMenu");
    const userDropdown = document.getElementById("userDropdown");

    if (userMenu && userDropdown) {
        userMenu.addEventListener("click", () => {
            userDropdown.classList.toggle("hidden");
        });

        // Optionnel : Fermer le menu si on clique en dehors
        document.addEventListener("click", (e) => {
            if (!userMenu.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add("hidden");
            }
        });
    }
});

// 📜 Gestion du centre d'aide
document.addEventListener("DOMContentLoaded", () => {
    const helpBtn = document.getElementById("helpOption");
    const helpPopup = document.getElementById("helpPopup");
    const closeHelp = document.getElementById("closeHelp");

    if (helpBtn && helpPopup && closeHelp) {
        // Afficher le centre d'aide lorsque le bouton est cliqué
        helpBtn.addEventListener("click", () => {
            helpPopup.style.display = "block";
        });

        // Masquer le centre d'aide lorsque le bouton de fermeture est cliqué
        closeHelp.addEventListener("click", () => {
            helpPopup.style.display = "none";
        });

        // Optionnel : Fermer le centre d'aide si on clique en dehors
        document.addEventListener("click", (e) => {
            if (!helpPopup.contains(e.target) && e.target !== helpBtn) {
                helpPopup.style.display = "none";
            }
        });
    }
});
  // 📜 Gestion du menu hamburger
document.addEventListener("DOMContentLoaded", () => {
    const menuBtn = document.getElementById("menuBtn");
    const dropdown = document.getElementById("dropdownMenu");

    if (menuBtn && dropdown) {
        menuBtn.addEventListener("click", () => {
            dropdown.classList.toggle("hidden");
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const menuBtn = document.getElementById("menuBtn");
    const dropdown = document.getElementById("dropdownMenu");

    if (menuBtn && dropdown) {
        console.log("Menu button and dropdown found!");

        menuBtn.addEventListener("click", () => {
            console.log("Menu button clicked!");
            dropdown.classList.toggle("hidden");
        });
    } else {
        console.error("Menu button or dropdown not found!");
    }
});

// 📜 Gestion du clic sur le bouton "Recherche" dans le footer
document.addEventListener("DOMContentLoaded", () => {
    const footerSearchBtn = document.getElementById("footerSearchBtn");
    const searchInput = document.getElementById("searchInput");

    if (footerSearchBtn && searchInput) {
        footerSearchBtn.addEventListener("click", (e) => {
            e.preventDefault(); // Empêche le comportement par défaut du lien
            searchInput.focus(); // Met le focus sur la barre de recherche
            window.scrollTo({ top: 0, behavior: "smooth" }); // Remonte vers l'en-tête
        });
    }
});

// 📜 Gestion du clic sur le bouton "Recherche" dans le footer
document.addEventListener("DOMContentLoaded", () => {
    const footerSearchBtn = document.getElementById("footerSearchBtn");
    const searchInput = document.getElementById("searchInput");

    if (footerSearchBtn && searchInput) {
        footerSearchBtn.addEventListener("click", (e) => {
            e.preventDefault(); // Empêche le comportement par défaut du lien
            searchInput.focus(); // Met le focus sur la barre de recherche
            window.scrollTo({ top: 0, behavior: "smooth" }); // Remonte vers l'en-tête
        });
    }
});