(function () {
  "use strict";

  const initialized = new WeakSet();

  function getMenuSummary(opener) {
    const nav = opener.closest("details[data-site-nav]");
    return nav ? (nav.querySelector(":scope > summary") || nav.querySelector("summary")) : null;
  }

  function setDialogContext(dialog, opener) {
    const form = dialog.querySelector("form[data-enquiry-form], form.wpcf7-form.enquiry-form");
    if (!form) {
      return;
    }

    const purpose = form.querySelector('[name="purpose"]');
    const recordId = form.querySelector('[name="record_id"]');

    if (purpose) {
      purpose.value = opener.dataset.purpose || "";
    }

    if (recordId) {
      recordId.value = opener.dataset.recordId || "";
    }
  }

  function focusDialogStart(dialog) {
    const form = dialog.querySelector("form[data-enquiry-form], form.wpcf7-form.enquiry-form");
    const name = form && form.querySelector('[name="name"]');
    const heading = dialog.querySelector("[data-dialog-heading]");
    const target = name || heading;

    if (target) {
      target.focus();
    }
  }

  function closeDialog(dialog) {
    if (dialog.open) {
      dialog.close();
    }
  }

  function initEnquiryDialog(dialog) {
    if (!dialog || initialized.has(dialog) || typeof dialog.showModal !== "function") {
      return;
    }

    initialized.add(dialog);
    let returnFocus = null;
    let beganOnBackdrop = false;

    document.addEventListener("click", (event) => {
      const opener = event.target.closest('a[href="#request"][data-dialog-open]');
      if (!opener) {
        return;
      }

      const nav = opener.closest("details[data-site-nav]");
      const openedFromMenu = Boolean(nav && nav.open);

      setDialogContext(dialog, opener);

      try {
        if (!dialog.open) {
          dialog.showModal();
        }
      } catch {
        return;
      }

      event.preventDefault();
      returnFocus = openedFromMenu ? (getMenuSummary(opener) || opener) : opener;

      if (nav && nav.open) {
        nav.open = false;
      }

      requestAnimationFrame(() => {
        focusDialogStart(dialog);
      });
    }, true);

    dialog.addEventListener("click", (event) => {
      const closeControl = event.target.closest("[data-dialog-close]");
      if (closeControl && dialog.contains(closeControl)) {
        event.preventDefault();
        closeDialog(dialog);
        return;
      }

      if (event.target === dialog && beganOnBackdrop) {
        closeDialog(dialog);
      }

      beganOnBackdrop = false;
    });

    dialog.addEventListener("pointerdown", (event) => {
      const panel = dialog.querySelector("[data-dialog-panel]");
      beganOnBackdrop = Boolean(panel) && event.target === dialog;
    });

    dialog.addEventListener("pointerup", (event) => {
      if (event.target !== dialog) {
        beganOnBackdrop = false;
      }
    });

    dialog.addEventListener("pointercancel", () => {
      beganOnBackdrop = false;
    });

    dialog.addEventListener("close", () => {
      const target = returnFocus;
      returnFocus = null;
      if (target && target.isConnected) {
        requestAnimationFrame(() => {
          target.focus();
        });
      }
    });
  }

  function initAllEnquiryDialogs() {
    const dialog = document.getElementById("enquiry-dialog");
    if (typeof HTMLDialogElement !== "undefined" && dialog instanceof HTMLDialogElement) {
      initEnquiryDialog(dialog);
    }
  }

  window.ArcticBehaviors = window.ArcticBehaviors || {};
  window.ArcticBehaviors.initEnquiryDialog = initEnquiryDialog;
  window.ArcticBehaviors.closeEnquiryDialog = closeDialog;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllEnquiryDialogs, { once: true });
  } else {
    initAllEnquiryDialogs();
  }
}());
