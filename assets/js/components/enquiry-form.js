(function () {
  "use strict";

  const initialized = new WeakSet();
  const composing = new WeakSet();
  const phoneValidationShown = new WeakSet();
  const phoneFormat = "+7 999 999 99 99";
  const consentRequiredHint = "Подтвердите согласие, чтобы отправить заявку.";
  const consentReadyHint = "Согласие получено — заявку можно отправить.";

  function digits(value) {
    return value.replace(/\D/g, "");
  }

  function formatSubscriber(subscriber) {
    const groups = [subscriber.slice(0, 3), subscriber.slice(3, 6), subscriber.slice(6, 8), subscriber.slice(8, 10)].filter(Boolean);
    return `+7${groups.length ? ` ${groups.join(" ")}` : " "}`;
  }

  function parsePhone(value) {
    const raw = value.trim();
    const valueDigits = digits(raw);
    const allowedCharacters = /^[\d\s()+-]*$/.test(raw);
    const plusCount = (raw.match(/\+/g) || []).length;

    if (!raw) {
      return { kind: "empty", subscriber: "" };
    }

    if (!allowedCharacters || plusCount > 1 || (plusCount && !raw.startsWith("+"))) {
      return { kind: "unsupported", subscriber: "" };
    }

    if (raw.startsWith("+")) {
      if (!raw.startsWith("+7")) {
        return { kind: "unsupported", subscriber: "" };
      }
      if (valueDigits.length > 11 || valueDigits.slice(1).length > 10) {
        return { kind: "malformed", subscriber: "" };
      }
      return { kind: "domestic", subscriber: valueDigits.slice(1) };
    }

    if (valueDigits.length === 11 && /^[78]/.test(valueDigits)) {
      return { kind: "domestic", subscriber: valueDigits.slice(1) };
    }

    if (valueDigits.length === 1 && /^[78]/.test(valueDigits)) {
      return { kind: "domestic", subscriber: "" };
    }

    if (valueDigits.length <= 10) {
      return { kind: "domestic", subscriber: valueDigits };
    }

    return { kind: "malformed", subscriber: "" };
  }

  function subscriberDigitsBefore(value, position) {
    const before = digits(value.slice(0, position));
    return value.trim().startsWith("+7") ? Math.max(0, before.length - 1) : before.length;
  }

  function positionAfterSubscriberDigits(value, count) {
    if (count <= 0) {
      return value.startsWith("+7") ? 3 : 0;
    }

    let seen = 0;
    for (let index = 0; index < value.length; index += 1) {
      if (/\d/.test(value[index]) && !(index === 1 && value.startsWith("+7"))) {
        seen += 1;
        if (seen === count) {
          return index + 1;
        }
      }
    }
    return value.length;
  }

  function updatePhoneValidity(input, parsed, showInvalid) {
    const valid = parsed.kind === "domestic" && parsed.subscriber.length === 10;
    const needsMessage = Boolean(input.value.trim()) && !valid;
    input.setCustomValidity(needsMessage ? `Введите номер в формате ${phoneFormat}.` : "");
    input.setAttribute("aria-invalid", String(needsMessage && (showInvalid || phoneValidationShown.has(input))));
  }

  function syncPhoneState(input, preserveCaret, showInvalid) {
    const parsed = parsePhone(input.value);
    const position = input.selectionStart || 0;
    const before = subscriberDigitsBefore(input.value, position);

    if (parsed.kind === "domestic") {
      const formatted = formatSubscriber(parsed.subscriber);
      if (input.value !== formatted) {
        input.value = formatted;
      }
      if (preserveCaret && document.activeElement === input) {
        const nextPosition = positionAfterSubscriberDigits(formatted, before);
        input.setSelectionRange(nextPosition, nextPosition);
      }
    }

    updatePhoneValidity(input, parsed, showInvalid);
  }

  function resetPhoneState(input) {
    phoneValidationShown.delete(input);
    input.setCustomValidity("");
    input.setAttribute("aria-invalid", "false");
  }

  function initPhoneInput(input) {
    input.addEventListener("focus", () => {
      if (!input.value) {
        input.value = "+7 ";
        input.setSelectionRange(input.value.length, input.value.length);
      }
      syncPhoneState(input, true);
    });

    input.addEventListener("compositionstart", () => {
      composing.add(input);
    });

    input.addEventListener("compositionend", () => {
      composing.delete(input);
      phoneValidationShown.add(input);
      syncPhoneState(input, true);
    });

    input.addEventListener("paste", (event) => {
      const pasted = event.clipboardData && event.clipboardData.getData("text");
      if (!pasted) {
        return;
      }

      const parsed = parsePhone(pasted);
      const isEmptyPrefix = input.value === "+7 ";
      const isBlank = !input.value;
      const selectionCoversValue = input.selectionStart === 0 && input.selectionEnd === input.value.length;
      const pastedDigits = digits(pasted);
      const isCompleteDomestic = pastedDigits.length === 10 || (pastedDigits.length === 11 && /^[78]/.test(pastedDigits));
      const isExplicitInternational = pasted.trim().startsWith("+") && pastedDigits.length > 1;
      if (selectionCoversValue || isBlank || isEmptyPrefix || isCompleteDomestic || isExplicitInternational) {
        event.preventDefault();
        input.value = pasted;
        input.setSelectionRange(input.value.length, input.value.length);
        phoneValidationShown.add(input);
        syncPhoneState(input, true);
      }
    });

    input.addEventListener("beforeinput", (event) => {
      if (composing.has(input) || !["deleteContentBackward", "deleteContentForward"].includes(event.inputType)) {
        return;
      }

      const parsed = parsePhone(input.value);
      const start = input.selectionStart || 0;
      const end = input.selectionEnd || start;
      if (parsed.kind !== "domestic" || start !== end || input.value !== formatSubscriber(parsed.subscriber)) {
        return;
      }

      const at = subscriberDigitsBefore(input.value, start);
      const removeAt = event.inputType === "deleteContentBackward" ? at - 1 : at;
      if (removeAt < 0 || removeAt >= parsed.subscriber.length) {
        event.preventDefault();
        return;
      }

      event.preventDefault();
      const nextSubscriber = `${parsed.subscriber.slice(0, removeAt)}${parsed.subscriber.slice(removeAt + 1)}`;
      input.value = formatSubscriber(nextSubscriber);
      const caret = positionAfterSubscriberDigits(input.value, removeAt);
      input.setSelectionRange(caret, caret);
      phoneValidationShown.add(input);
      updatePhoneValidity(input, { kind: "domestic", subscriber: nextSubscriber });
    });

    input.addEventListener("input", () => {
      if (!composing.has(input)) {
        phoneValidationShown.add(input);
        syncPhoneState(input, true);
      }
    });

    input.addEventListener("change", () => {
      if (!composing.has(input)) {
        phoneValidationShown.add(input);
        syncPhoneState(input, false);
      }
    });

    input.addEventListener("blur", () => {
      phoneValidationShown.add(input);
      syncPhoneState(input, false, true);
    });

    input.addEventListener("invalid", () => {
      phoneValidationShown.add(input);
      syncPhoneState(input, false, true);
    });

    if (input.value) {
      phoneValidationShown.add(input);
      syncPhoneState(input, false, true);
    } else {
      resetPhoneState(input);
    }
  }

  function getFormParts(form) {
    return {
      consent: form.querySelector('[name="consent"]'),
      hint: form.querySelector(".enquiry-form__consent-hint"),
      submit: form.querySelector('[type="submit"]')
    };
  }

  function syncSubmitState(form) {
    const { consent, hint, submit } = getFormParts(form);
    if (!consent || !submit) {
      return;
    }

    submit.disabled = !consent.checked;
    if (hint) {
      hint.textContent = consent.checked ? consentReadyHint : consentRequiredHint;
      hint.classList.toggle("enquiry-form__consent-hint--ready", consent.checked);
    }
  }

  function syncPhoneInputs(form) {
    form.querySelectorAll('input[type="tel"]').forEach((input) => {
      phoneValidationShown.add(input);
      syncPhoneState(input, false, true);
    });
  }

  function initEnquiryForm(form) {
    if (!form || initialized.has(form)) {
      return;
    }

    const { consent, submit } = getFormParts(form);
    if (!consent || !submit) {
      return;
    }

    initialized.add(form);

    const purpose = form.querySelector('[name="purpose"]');
    if (purpose && form.dataset.purpose) {
      purpose.value = form.dataset.purpose;
    }

    form.querySelectorAll('input[type="tel"]').forEach(initPhoneInput);

    // This listener is deliberately registered before a checked consent can
    // enable the native submit control. The static demo never sends a request.
    form.addEventListener("submit", (event) => {
      event.preventDefault();

      syncPhoneInputs(form);

      if (!consent.checked) {
        return;
      }

      form.reportValidity();
    });

    consent.addEventListener("change", () => {
      syncSubmitState(form);
    });

    form.addEventListener("reset", () => {
      queueMicrotask(() => {
        form.querySelectorAll('input[type="tel"]').forEach(resetPhoneState);
        syncSubmitState(form);
      });
    });

    syncSubmitState(form);
  }

  function initAllEnquiryForms() {
    document.querySelectorAll("form[data-enquiry-form], form.wpcf7-form.enquiry-form").forEach(initEnquiryForm);
  }

  document.addEventListener("wpcf7mailsent", (event) => {
    const form = event.target && event.target.querySelector
      ? event.target.querySelector("form.wpcf7-form.enquiry-form")
      : null;

    if (form) {
      form.reset();
      queueMicrotask(() => syncSubmitState(form));
    }

    const dialog = event.target && event.target.closest
      ? event.target.closest("dialog.enquiry-dialog")
      : null;

    if (dialog && window.ArcticBehaviors && typeof window.ArcticBehaviors.closeEnquiryDialog === "function") {
      window.ArcticBehaviors.closeEnquiryDialog(dialog);
    }
  });

  window.ArcticBehaviors = window.ArcticBehaviors || {};
  window.ArcticBehaviors.initEnquiryForm = initEnquiryForm;
  window.ArcticBehaviors.syncEnquirySubmitState = syncSubmitState;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllEnquiryForms, { once: true });
  } else {
    initAllEnquiryForms();
  }
}());
