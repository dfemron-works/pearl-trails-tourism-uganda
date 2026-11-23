const CACHE_NAME = "pearl-trails-ug-offline-v1";
const OFFLINE_ASSETS = [
  "./",
  "index.php",
  "safari_map.php",
  "my_bookings.php",
  "weather_geography.php",
  "mobile_app.html",
  "style.css",
];

self.addEventListener("install", function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(OFFLINE_ASSETS);
    }),
  );
  self.skipWaiting();
});

self.addEventListener("activate", function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys
          .filter(function (key) {
            return key !== CACHE_NAME;
          })
          .map(function (key) {
            return caches.delete(key);
          }),
      );
    }),
  );
  self.clients.claim();
});

self.addEventListener("fetch", function (event) {
  if (event.request.method !== "GET") return;

  event.respondWith(
    fetch(event.request)
      .then(function (networkRes) {
        const resClone = networkRes.clone();
        caches.open(CACHE_NAME).then(function (cache) {
          cache.put(event.request, resClone);
        });
        return networkRes;
      })
      .catch(function () {
        return caches.match(event.request);
      }),
  );
});
