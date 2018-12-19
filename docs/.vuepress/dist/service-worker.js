/**
 * Welcome to your Workbox-powered service worker!
 *
 * You'll need to register this file in your web app and you should
 * disable HTTP caching for this file too.
 * See https://goo.gl/nhQhGp
 *
 * The rest of the code is auto-generated. Please don't update this file
 * directly; instead, make changes to your Workbox build configuration
 * and re-run your build process.
 * See https://goo.gl/2aRDsh
 */

importScripts("https://storage.googleapis.com/workbox-cdn/releases/3.6.3/workbox-sw.js");

/**
 * The workboxSW.precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */
self.__precacheManifest = [
  {
    "url": "404.html",
    "revision": "96cc7cac9ba7430ef59b734eae5ca526"
  },
  {
    "url": "advanced-settings.png",
    "revision": "a9bbfb730315784ab305f5ae4b86dc06"
  },
  {
    "url": "assets/css/0.styles.0f2a7d36.css",
    "revision": "eceda7f4c15a9809df9e3fe9229b69af"
  },
  {
    "url": "assets/img/search.83621669.svg",
    "revision": "83621669651b9a3d4bf64d1a670ad856"
  },
  {
    "url": "assets/js/2.065f523a.js",
    "revision": "0423ed6203b228bbe2ac4181f3e3e1de"
  },
  {
    "url": "assets/js/3.64b6542b.js",
    "revision": "10ea4533793f9ffec2b75157e6f7f778"
  },
  {
    "url": "assets/js/4.d04c1941.js",
    "revision": "8d423f2a8b5c17ac692c9b51a9227a57"
  },
  {
    "url": "assets/js/5.2dfbc805.js",
    "revision": "dbe9cb0e33308b08eafd16e3d25ad5a5"
  },
  {
    "url": "assets/js/6.48630f0e.js",
    "revision": "30462b3827ea8322f5d780a916549b0a"
  },
  {
    "url": "assets/js/7.e6f794bc.js",
    "revision": "17bff5c1446870d28fab00a7a92c4624"
  },
  {
    "url": "assets/js/app.138272fa.js",
    "revision": "9df0d3198c2854ea63d15c4dfcb8905e"
  },
  {
    "url": "banner-1544x500.png",
    "revision": "5e903c91f73eb5e48c1ddbce5756edb6"
  },
  {
    "url": "banner-772x250.png",
    "revision": "e1c141d365fca77bd803ff076929827d"
  },
  {
    "url": "choose-language.png",
    "revision": "402e788bfb23b6241eb926c34d4e5843"
  },
  {
    "url": "developer-docs/snippets-examples.html",
    "revision": "03d2abc5f9e4a96025d9cea5abb1c346"
  },
  {
    "url": "editor.png",
    "revision": "e59f717fb975bca11c1844c1a40069ef"
  },
  {
    "url": "favicon.png",
    "revision": "04c1a0c2a730b9dd8fe8bc7be5febbe9"
  },
  {
    "url": "icon-128x128.png",
    "revision": "561835a9159b574e131ab3919b4b0ad1"
  },
  {
    "url": "icon-256x256.png",
    "revision": "395835eea4bdae80ff9ddc02738aa0d1"
  },
  {
    "url": "index.html",
    "revision": "f7c894a6953976d69f43270a139f1e02"
  },
  {
    "url": "logo.png",
    "revision": "561835a9159b574e131ab3919b4b0ad1"
  },
  {
    "url": "main-settings.png",
    "revision": "986b4bd2f60e38c7603f433191a4a73c"
  },
  {
    "url": "settings.png",
    "revision": "99b6619dec325076ae6766da500c1d23"
  },
  {
    "url": "user-docs/index.html",
    "revision": "895677bc14171d28255b02ef2047f083"
  },
  {
    "url": "user-docs/install-multisite.html",
    "revision": "bea672b5bf1a7d05425c20bf6d0fc03f"
  },
  {
    "url": "widget.png",
    "revision": "50a87a476855be43813cb5d999c227d6"
  }
].concat(self.__precacheManifest || []);
workbox.precaching.suppressWarnings();
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});
addEventListener('message', event => {
  const replyPort = event.ports[0]
  const message = event.data
  if (replyPort && message && message.type === 'skip-waiting') {
    event.waitUntil(
      self.skipWaiting().then(
        () => replyPort.postMessage({ error: null }),
        error => replyPort.postMessage({ error })
      )
    )
  }
})
