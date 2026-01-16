const CACHE_NAME = 'asistencia-alumno-v2'; // Incrementamos la versión
const urlsToCache = [
  'index.html',
  'home.html',
  'manifest.json',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://unpkg.com/html5-qrcode'
];

// Instalación del Service Worker: Guarda los archivos esenciales en el celular
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(urlsToCache);
      })
  );
});

// Estrategia: Network First (Prioriza la red para recibir datos reales de la BD)
// Si no hay internet, carga la interfaz desde la caché del celular
self.addEventListener('fetch', (event) => {
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});

// Limpieza de cachés antiguas cuando actualices la app
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});