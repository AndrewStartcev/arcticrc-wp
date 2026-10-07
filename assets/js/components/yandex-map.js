(function () {
  "use strict";

  function number(value, fallback) {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
  }

  function parsePoints(root) {
    const node = root.querySelector("[data-map-points]");
    if (!node) {
      return [];
    }

    try {
      const value = JSON.parse(node.textContent || "[]");
      return Array.isArray(value) ? value : [];
    } catch {
      return [];
    }
  }

  function initMap(root) {
    if (!root || root.dataset.mapReady === "true" || typeof window.ymaps === "undefined") {
      return;
    }

    const center = [
      number(root.dataset.centerLat, 61.5240),
      number(root.dataset.centerLng, 105.3188)
    ];
    const zoom = number(root.dataset.zoom, 3);

    const map = new window.ymaps.Map(root, {
      center,
      zoom,
      controls: ["zoomControl", "fullscreenControl"]
    }, {
      suppressMapOpenBlock: true
    });

    const points = parsePoints(root);
    const address = (root.dataset.address || "").trim();

    if (address) {
      window.ymaps.geocode(address, { results: 1 }).then((result) => {
        const first = result.geoObjects.get(0);

        if (!first) {
          return;
        }

        const coords = first.geometry.getCoordinates();
        map.setCenter(coords, zoom);
        first.properties.set("balloonContent", address);
        first.options.set("preset", "islands#blueCircleDotIcon");
        map.geoObjects.add(first);
      });
    } else {
      points.forEach((point) => {
        const lat = number(point.lat, null);
        const lng = number(point.lng, null);

        if (lat === null || lng === null) {
          return;
        }

        map.geoObjects.add(new window.ymaps.Placemark(
          [lat, lng],
          { balloonContent: point.title || "" },
          { preset: "islands#blueCircleDotIcon" }
        ));
      });
    }

    root.dataset.mapReady = "true";
  }

  function initAll() {
    if (typeof window.ymaps === "undefined") {
      return;
    }

    window.ymaps.ready(() => {
      document.querySelectorAll("[data-yandex-map]").forEach(initMap);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll, { once: true });
  } else {
    initAll();
  }
}());
