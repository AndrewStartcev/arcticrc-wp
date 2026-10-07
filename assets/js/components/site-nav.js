(function () {
  "use strict";

  const initialized = new WeakSet();

  function getSummary(details) {
    return details.querySelector(":scope > summary") || details.querySelector("summary");
  }

  function closeSiteNav(details, { restoreFocus = false } = {}) {
    if (!details || !details.open) {
      return;
    }

    details.open = false;

    if (restoreFocus) {
      const summary = getSummary(details);
      if (summary) {
        summary.focus({ preventScroll: true });
      }
    }
  }

  function initSiteNav(details) {
    if (!details || initialized.has(details)) {
      return;
    }

    const summary = getSummary(details);
    if (!summary) {
      return;
    }

    initialized.add(details);
    details.dataset.navEnhanced = "true";

    // CSS owns the presentation. Once its disclosure control disappears,
    // discard the open state instead of reviving an old overlay on return.
    window.addEventListener("resize", () => {
      if (details.open && getComputedStyle(summary).display === "none") {
        closeSiteNav(details);
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && details.open) {
        event.preventDefault();
        closeSiteNav(details, { restoreFocus: true });
      }
    });

    details.addEventListener("click", (event) => {
      const link = event.target.closest("a[href]");
      if (link && details.contains(link)) {
        closeSiteNav(details);
      }
    });

    details.addEventListener("focusout", (event) => {
      const nextTarget = event.relatedTarget;
      if (details.open && nextTarget && !details.contains(nextTarget)) {
        closeSiteNav(details);

        if (typeof nextTarget.scrollIntoView === "function") {
          nextTarget.scrollIntoView({ block: "nearest", inline: "nearest", behavior: "instant" });
        }
      }
    });
  }

  function initAllSiteNavs() {
    document.querySelectorAll("details[data-site-nav]").forEach(initSiteNav);
  }

  window.ArcticBehaviors = window.ArcticBehaviors || {};
  window.ArcticBehaviors.initSiteNav = initSiteNav;
  window.ArcticBehaviors.closeSiteNav = closeSiteNav;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllSiteNavs, { once: true });
  } else {
    initAllSiteNavs();
  }
}());
