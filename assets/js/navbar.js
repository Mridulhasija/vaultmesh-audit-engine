/**
 * Site navigation and persistent theme controls.
 */

// Immediate execution helper for theme syncing
function applyTheme(theme) {
  const isDark = theme === "dark";
  const root = document.documentElement;

  if (isDark) {
    root.classList.add("dark");
    if (document.body) document.body.classList.add("dark");
  } else {
    root.classList.remove("dark");
    if (document.body) document.body.classList.remove("dark");
  }

  // Sync all theme toggle buttons on the page (desktop and mobile)
  document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
    btn.setAttribute("aria-pressed", isDark ? "true" : "false");
    btn.setAttribute("title", isDark ? "Switch to light mode" : "Switch to dark mode");
    btn.setAttribute("aria-label", isDark ? "Switch to light mode" : "Switch to dark mode");

    const sunIcon = btn.querySelector(".theme-icon-sun");
    const moonIcon = btn.querySelector(".theme-icon-moon");
    const labelText = btn.querySelector(".theme-label-text");

    if (sunIcon && moonIcon) {
      if (isDark) {
        sunIcon.classList.remove("hidden");
        moonIcon.classList.add("hidden");
      } else {
        sunIcon.classList.add("hidden");
        moonIcon.classList.remove("hidden");
      }
    }

    if (labelText) {
      labelText.textContent = isDark ? "Light Mode" : "Dark Mode";
    }
  });
}

function getInitialTheme() {
  try {
    const saved = localStorage.getItem("theme");
    if (saved === "dark" || saved === "light") return saved;
  } catch (_) {}
  return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
}

// Register before DOM ready so the controls work even if this script is loaded late.
document.addEventListener("click", (event) => {
  const target = event.target instanceof Element ? event.target : null;
  const button = target && target.closest("[data-theme-toggle]");
  if (!button) return;
  event.preventDefault();
  event.stopPropagation();
  const nextTheme = document.documentElement.classList.contains("dark") ? "light" : "dark";
  try { localStorage.setItem("theme", nextTheme); } catch (_) {}
  applyTheme(nextTheme);
});

function initializeNavbar() {
  // Sync Theme State
  const initialTheme = getInitialTheme();
  applyTheme(initialTheme);

  // Authentication poster tabs are styled with Tailwind utility classes.
  document.querySelectorAll('[role="tablist"]').forEach((tabList) => {
    const tabs = Array.from(tabList.querySelectorAll('[data-auth-tab]'));
    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        tabs.forEach((item) => {
          const active = item === tab;
          item.setAttribute('aria-selected', active ? 'true' : 'false');
          item.classList.toggle('bg-[#173f35]', active);
          item.classList.toggle('text-white', active);
          item.classList.toggle('text-[#92a99a]', !active);
          const panel = document.querySelector(`[data-auth-panel="${item.dataset.authTab}"]`);
          if (panel) panel.classList.toggle('hidden', !active);
        });
      });
      tab.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
        event.preventDefault();
        const offset = event.key === 'ArrowRight' ? 1 : -1;
        tabs[(index + offset + tabs.length) % tabs.length].focus();
      });
    });
  });

  // Listen to OS Dark Mode Preference changes (if user hasn't explicitly set localStorage)
  if (window.matchMedia) {
    const colorPreference = window.matchMedia("(prefers-color-scheme: dark)");
    const syncPreference = (e) => {
      try {
        if (!localStorage.getItem("theme")) applyTheme(e.matches ? "dark" : "light");
      } catch (_) {}
    };
    if (colorPreference.addEventListener) colorPreference.addEventListener("change", syncPreference);
    else if (colorPreference.addListener) colorPreference.addListener(syncPreference);
  }

  // Mobile Menu Toggle
  const toggleButton = document.querySelector('[data-collapse-toggle="navbar-sticky"]');
  const mobileMenu = document.getElementById("navbar-sticky");

  if (toggleButton && mobileMenu) {
    toggleButton.addEventListener("click", function (event) {
      event.stopPropagation();
      const isHidden = mobileMenu.classList.toggle("hidden");
      this.setAttribute("aria-expanded", isHidden ? "false" : "true");
    });

    // Close mobile menu when clicking outside
    document.addEventListener("click", function (event) {
      if (!mobileMenu.contains(event.target) && !toggleButton.contains(event.target)) {
        mobileMenu.classList.add("hidden");
        toggleButton.setAttribute("aria-expanded", "false");
      }
    });
  }

  // Handle Dropdowns (Desktop & Mobile)
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach((dropdown) => {
    const dropdownLink = dropdown.querySelector(".dropdown-link");
    const dropdownToggle = dropdown.querySelector(".dropdown-toggle");
    const dropdownMenu = dropdown.querySelector(".dropdown-menu");
    const chevron = dropdown.querySelector("svg");

    if (!dropdownLink || !dropdownMenu) return;

    let hoverTimeout;

    // Open desktop
    const showDropdown = () => {
      clearTimeout(hoverTimeout);
      dropdownMenu.classList.remove("hidden");
      dropdown.classList.add("is-open");
      dropdownLink.setAttribute("aria-expanded", "true");
      if (chevron) chevron.style.transform = "rotate(180deg)";
    };

    // Close desktop with safe delay
    const hideDropdown = () => {
      hoverTimeout = setTimeout(() => {
        dropdownMenu.classList.add("hidden");
        dropdown.classList.remove("is-open");
        dropdownLink.setAttribute("aria-expanded", "false");
        if (chevron) chevron.style.transform = "rotate(0deg)";
      }, 160);
    };

    // Desktop hover & mouseleave
    dropdown.addEventListener("mouseenter", showDropdown);
    dropdown.addEventListener("mouseleave", hideDropdown);

    // Focus / keyboard accessibility
    dropdown.addEventListener("focusin", showDropdown);
    dropdown.addEventListener("focusout", (e) => {
      if (!dropdown.contains(e.relatedTarget)) {
        hideDropdown();
      }
    });

    // Mobile / Touch toggle
    if (dropdownToggle) {
      dropdownToggle.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const willBeHidden = !dropdownMenu.classList.contains("hidden");
        if (willBeHidden) {
          dropdownMenu.classList.add("hidden");
          dropdown.classList.remove("is-open");
          dropdownLink.setAttribute("aria-expanded", "false");
          if (chevron) chevron.style.transform = "rotate(0deg)";
        } else {
          dropdownMenu.classList.remove("hidden");
          dropdown.classList.add("is-open");
          dropdownLink.setAttribute("aria-expanded", "true");
          if (chevron) chevron.style.transform = "rotate(180deg)";
        }
      });
    }
  });

  // Global Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      dropdowns.forEach((d) => {
        const m = d.querySelector(".dropdown-menu");
        const l = d.querySelector(".dropdown-link");
        const c = d.querySelector("svg");
        if (m) m.classList.add("hidden");
        d.classList.remove("is-open");
        if (l) l.setAttribute("aria-expanded", "false");
        if (c) c.style.transform = "rotate(0deg)";
      });
      if (mobileMenu && !mobileMenu.classList.contains("hidden")) {
        mobileMenu.classList.add("hidden");
        if (toggleButton) toggleButton.setAttribute("aria-expanded", "false");
      }
    }
  });

  // Global click outside to close dropdowns
  document.addEventListener("click", (e) => {
    dropdowns.forEach((d) => {
      if (!d.contains(e.target)) {
        const m = d.querySelector(".dropdown-menu");
        const l = d.querySelector(".dropdown-link");
        const c = d.querySelector("svg");
        if (m) m.classList.add("hidden");
        d.classList.remove("is-open");
        if (l) l.setAttribute("aria-expanded", "false");
        if (c) c.style.transform = "rotate(0deg)";
      }
    });
  });

  // Automatically reset mobile menu on desktop resize
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 1280 && mobileMenu && !mobileMenu.classList.contains("hidden")) {
      mobileMenu.classList.add("hidden");
      if (toggleButton) toggleButton.setAttribute("aria-expanded", "false");
    }
  });

  // Active link highlighter (compatible with both Light & Dark modes)
  const currentPath = window.location.pathname.replace(/\/+$/, "") || "/";
  document.querySelectorAll(".nav-menu a").forEach((link) => {
    try {
      const linkPath = new URL(link.href).pathname.replace(/\/+$/, "") || "/";
      if (linkPath === currentPath) {
        link.classList.add("active");
      }
    } catch (_) {}
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializeNavbar, { once: true });
} else {
  initializeNavbar();
}
