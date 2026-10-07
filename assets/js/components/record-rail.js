(function () {
  "use strict";

  const initialized = new WeakSet();

  function paddedNumber(value) {
    return String(value).padStart(2, "0");
  }

  function setControlState(control, disabled) {
    if (control) {
      control.disabled = disabled;
    }
  }

  function initRecordRail(rail) {
    if (!rail || initialized.has(rail)) {
      return;
    }

    const track = rail.querySelector("[data-rail-track]");
    const previous = rail.querySelector("[data-rail-prev]");
    const next = rail.querySelector("[data-rail-next]");
    const current = rail.querySelector("[data-rail-current]");

    if (!track) {
      return;
    }

    initialized.add(rail);
    let activeIndex = 0;
    let pendingIndex = null;
    let settleTimer = null;
    let dragState = null;
    let clickSuppressTimer = null;
    let suppressClick = false;

    function snapUnits() {
      return Array.from(track.querySelectorAll("[data-rail-item], [data-rail-page]")).filter((item) => {
        return getComputedStyle(item).scrollSnapAlign !== "none";
      });
    }

    function metrics() {
      const maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      return {
        maximum,
        position: Math.min(maximum, Math.max(0, track.scrollLeft)),
        hasOverflow: maximum > 1
      };
    }

    function nearestIndex(units, position) {
      if (!units.length) {
        return -1;
      }

      const trackLeft = track.getBoundingClientRect().left;
      // Padded rails use the same inset for padding and scroll-padding.
      // Read the resolved padding: scroll-padding may still contain a CSS calc().
      const inset = parseFloat(getComputedStyle(track).paddingLeft) || 0;
      const maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      let nearest = 0;
      let distance = Infinity;
      units.forEach((unit, index) => {
        const unitPosition = unit.getBoundingClientRect().left - trackLeft + track.scrollLeft;
        const snapPosition = Math.max(0, Math.min(maximum, unitPosition - inset));
        const nextDistance = Math.abs(snapPosition - position);
        if (nextDistance < distance) {
          distance = nextDistance;
          nearest = index;
        }
      });
      return nearest;
    }

    function renderState(units, currentMetrics) {
      if (current) {
        current.textContent = units.length ? paddedNumber(activeIndex + 1) : "00";
      }

      const enabled = units.length > 1 && currentMetrics.hasOverflow;
      rail.dataset.railEnhanced = enabled ? "true" : "false";
      if (previous) {
        previous.hidden = !enabled;
      }
      if (next) {
        next.hidden = !enabled;
      }
      const atFirst = pendingIndex === null ? currentMetrics.position <= 1 : activeIndex === 0;
      const atLast = pendingIndex === null
        ? currentMetrics.position >= currentMetrics.maximum - 1
        : activeIndex === units.length - 1;
      setControlState(previous, !enabled || atFirst);
      setControlState(next, !enabled || atLast);
    }

    function synchronize(settled) {
      const units = snapUnits();
      const currentMetrics = metrics();
      const nearest = nearestIndex(units, currentMetrics.position);

      if (nearest >= 0 && (settled || pendingIndex === null)) {
        activeIndex = nearest;
      }
      activeIndex = Math.max(0, Math.min(activeIndex, Math.max(0, units.length - 1)));
      renderState(units, currentMetrics);
    }

    function moveBy(offset) {
      const units = snapUnits();
      const currentMetrics = metrics();
      if (units.length < 2 || !currentMetrics.hasOverflow) {
        synchronize(true);
        return;
      }

      const visible = nearestIndex(units, currentMetrics.position);
      const base = pendingIndex === null ? visible : pendingIndex;
      const destination = Math.max(0, Math.min(units.length - 1, base + offset));
      if (destination === base) {
        synchronize(false);
        return;
      }
      pendingIndex = destination;
      activeIndex = destination;
      renderState(units, currentMetrics);
      units[destination].scrollIntoView({ block: "nearest", inline: "start" });
      scheduleSettledState();
    }

    function scheduleSettledState() {
      if (settleTimer !== null) {
        window.clearTimeout(settleTimer);
      }
      settleTimer = window.setTimeout(() => {
        settleTimer = null;
        pendingIndex = null;
        synchronize(true);
      }, 160);
    }

    function clearPendingIntent() {
      if (pendingIndex === null) {
        return;
      }
      pendingIndex = null;
      if (settleTimer !== null) {
        window.clearTimeout(settleTimer);
        settleTimer = null;
      }
    }

    function clearSettleTimer() {
      if (settleTimer !== null) {
        window.clearTimeout(settleTimer);
        settleTimer = null;
      }
    }

    function clearClickSuppression() {
      suppressClick = false;
      if (clickSuppressTimer !== null) {
        window.clearTimeout(clickSuppressTimer);
        clickSuppressTimer = null;
      }
    }

    function suppressNextClick() {
      clearClickSuppression();
      suppressClick = true;
      clickSuppressTimer = window.setTimeout(clearClickSuppression, 0);
    }

    function supportsMouseDrag() {
      return ["hero", "clients", "tests", "projects"].some((variant) => {
        return rail.classList.contains("record-rail--" + variant);
      });
    }

    function completeMouseDrag(event, cancelled) {
      const state = dragState;
      if (!state) {
        return;
      }
      if (event && event.pointerId !== state.pointerId) {
        return;
      }

      if (!cancelled && event && state.axis === "x" && event.clientX !== state.currentX) {
        followMouseDrag(event);
      }

      dragState = null;
      rail.removeAttribute("data-rail-dragging");
      if (state.captured && track.hasPointerCapture(state.pointerId)) {
        track.releasePointerCapture(state.pointerId);
      }

      if (state.axis !== "x") {
        return;
      }

      suppressNextClick();
      if (rail.classList.contains("record-rail--clients")) {
        // Logo lists follow the full drag distance and settle at native snap
        // positions; the image/card rails below advance one adjacent record.
        clearPendingIntent();
        synchronize(true);
        scheduleSettledState();
        return;
      }
      const units = snapUnits();
      const currentMetrics = metrics();
      const distance = state.currentX - state.startX;
      const shouldAdvance = !cancelled && (
        Math.abs(distance) > 38 || Math.abs(state.velocity) > 0.35
      );
      const direction = distance < 0 || (distance === 0 && state.velocity < 0) ? 1 : -1;
      const destination = shouldAdvance
        ? Math.max(0, Math.min(units.length - 1, state.startIndex + direction))
        : state.startIndex;

      if (!units.length || !currentMetrics.hasOverflow) {
        synchronize(true);
        return;
      }

      pendingIndex = destination;
      activeIndex = destination;
      renderState(units, currentMetrics);
      units[destination].scrollIntoView({ block: "nearest", inline: "start" });
      scheduleSettledState();
    }

    function followMouseDrag(event) {
      const state = dragState;
      if (!state || state.axis !== "x" || event.pointerId !== state.pointerId) {
        return;
      }

      const now = event.timeStamp || performance.now();
      const elapsed = Math.max(1, now - state.lastTime);
      state.velocity = (event.clientX - state.lastX) / elapsed;
      state.lastX = event.clientX;
      state.lastTime = now;
      state.currentX = event.clientX;
      const currentMetrics = metrics();
      track.scrollLeft = Math.max(0, Math.min(
        currentMetrics.maximum,
        state.startPosition - (event.clientX - state.startX)
      ));
      synchronize(false);
      event.preventDefault();
    }

    function beginMouseDrag(event) {
      if (!supportsMouseDrag() || event.pointerType !== "mouse" || !event.isPrimary || event.button !== 0 || !metrics().hasOverflow) {
        return;
      }

      dragState = {
        pointerId: event.pointerId,
        startX: event.clientX,
        startY: event.clientY,
        currentX: event.clientX,
        lastX: event.clientX,
        lastTime: event.timeStamp || performance.now(),
        startPosition: 0,
        startIndex: 0,
        velocity: 0,
        axis: null,
        captured: false
      };
    }

    function handlePointerDown(event) {
      if (event.pointerType !== "mouse") {
        clearPendingIntent();
        return;
      }
      beginMouseDrag(event);
    }

    function moveMouseDrag(event) {
      const state = dragState;
      if (!state || event.pointerId !== state.pointerId) {
        return;
      }

      if (state.axis === null) {
        const deltaX = event.clientX - state.startX;
        const deltaY = event.clientY - state.startY;
        if (Math.max(Math.abs(deltaX), Math.abs(deltaY)) < 8) {
          return;
        }
        if (Math.abs(deltaY) > Math.abs(deltaX) || !metrics().hasOverflow) {
          dragState = null;
          return;
        }

        state.axis = "x";
        state.startPosition = metrics().position;
        state.startIndex = nearestIndex(snapUnits(), state.startPosition);
        clearPendingIntent();
        clearSettleTimer();
        track.setPointerCapture(state.pointerId);
        state.captured = true;
        rail.dataset.railDragging = "true";
      }

      followMouseDrag(event);
    }

    if (previous) {
      previous.addEventListener("click", () => moveBy(-1));
    }
    if (next) {
      next.addEventListener("click", () => moveBy(1));
    }

    track.addEventListener("scroll", () => {
      synchronize(false);
      if (!dragState || dragState.axis !== "x") {
        scheduleSettledState();
      }
    }, { passive: true });
    if (supportsMouseDrag()) {
      track.addEventListener("pointerdown", handlePointerDown, { passive: true });
      track.addEventListener("pointermove", moveMouseDrag, { passive: false });
      track.addEventListener("pointerup", (event) => completeMouseDrag(event, false));
      track.addEventListener("pointercancel", (event) => completeMouseDrag(event, true));
      track.addEventListener("lostpointercapture", (event) => completeMouseDrag(event, true));
      track.addEventListener("dragstart", (event) => {
        if (dragState && event.target.closest("img, a")) {
          event.preventDefault();
        }
      });
      track.addEventListener("click", (event) => {
        if (!suppressClick) {
          return;
        }
        clearClickSuppression();
        event.preventDefault();
        event.stopImmediatePropagation();
      }, true);
    } else {
      track.addEventListener("pointerdown", () => {
        clearPendingIntent();
      }, { passive: true });
    }
    track.addEventListener("wheel", clearPendingIntent, { passive: true });
    track.addEventListener("keydown", (event) => {
      if ([" ", "ArrowDown", "ArrowLeft", "ArrowRight", "End", "Home", "PageDown", "PageUp"].includes(event.key)) {
        clearPendingIntent();
      }
    });
    window.addEventListener("resize", () => {
      if (supportsMouseDrag()) {
        completeMouseDrag(null, true);
      }
      clearPendingIntent();
      window.requestAnimationFrame(() => synchronize(true));
    });
    if (supportsMouseDrag()) {
      window.addEventListener("blur", () => completeMouseDrag(null, true));
      window.addEventListener("pointerup", (event) => completeMouseDrag(event, false));
      window.addEventListener("pointercancel", (event) => completeMouseDrag(event, true));
    }
    window.requestAnimationFrame(() => synchronize(true));
  }

  function initAllRecordRails() {
    document.querySelectorAll("[data-record-rail]").forEach(initRecordRail);
  }

  window.ArcticBehaviors = window.ArcticBehaviors || {};
  window.ArcticBehaviors.initRecordRail = initRecordRail;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllRecordRails, { once: true });
  } else {
    initAllRecordRails();
  }
}());
