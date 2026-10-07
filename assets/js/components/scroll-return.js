(function () {
  "use strict";

  const controls = document.querySelector(".scroll-return");
  if (!controls) return;

  const button = controls.querySelector(".scroll-return__button");
  const arrow = controls.querySelector(".scroll-return__arrow");
  let savedPosition = null;
  let hasLeftSavedPosition = false;

  function updateState() {
    if (savedPosition) {
      const atSavedPosition = Math.abs(window.scrollY - savedPosition.top) < 1 &&
        Math.abs(window.scrollX - savedPosition.left) < 1;
      // Smooth scrolling starts after this handler; retain its origin until we move.
      if (!atSavedPosition) hasLeftSavedPosition = true;
      else if (hasLeftSavedPosition) savedPosition = null;
    }

    const returning = savedPosition !== null;
    controls.hidden = !returning && window.scrollY < document.documentElement.clientHeight * 2;
    const label = returning ? "Вернуться к сохранённому месту" : "Наверх к шапке";
    button.setAttribute("aria-label", label);
    button.title = label;
    button.setAttribute("aria-disabled", String(!returning && window.scrollY <= 0));
    arrow.classList.toggle("scroll-return__arrow--up", !returning);
    arrow.classList.toggle("scroll-return__arrow--down", returning);
  }

  button.addEventListener("click", () => {
    // Inherit the CSS smooth-scroll and reduced-motion policy.
    if (savedPosition) {
      window.scrollTo(savedPosition);
      savedPosition = null;
    } else {
      if (window.scrollY <= 0) return;
      savedPosition = { top: window.scrollY, left: window.scrollX };
      hasLeftSavedPosition = false;
      window.scrollTo({ top: 0, left: savedPosition.left });
    }
    updateState();
  });

  window.addEventListener("scroll", updateState, { passive: true });
  window.addEventListener("resize", updateState);
  window.addEventListener("pageshow", updateState);
  updateState();
}());
