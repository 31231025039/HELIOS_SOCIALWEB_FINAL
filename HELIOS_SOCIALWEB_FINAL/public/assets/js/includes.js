/**
 * Helios — nạp partial HTML + CSS/JS chung.
 * Tự động tính base URL từ vị trí script, không cần root server = public/.
 */
(function () {
  // Tính base = thư mục public/ dựa theo vị trí của file includes.js (public/assets/js/includes.js)
  var scriptSrc = (document.currentScript && document.currentScript.src) || "";
  var BASE = scriptSrc
    ? new URL("../../", scriptSrc).href   // lên 2 cấp: js/ → assets/ → public/
    : "/";

  function resolveUrl(path) {
    return BASE + path.replace(/^\//, "");
  }

  function injectStylesheet(href) {
    var resolved = href && !href.startsWith("http") ? resolveUrl(href) : href;
    if (!resolved || document.querySelector('link[rel="stylesheet"][href="' + resolved + '"]')) return;
    var l = document.createElement("link");
    l.rel = "stylesheet";
    l.href = resolved;
    document.head.appendChild(l);
  }

  function injectCommonCss() {
    injectStylesheet("assets/css/style.css");
    var pageCss = document.documentElement.getAttribute("data-page-css");
    if (pageCss) injectStylesheet(pageCss.trim());
  }

  function applyNavActive(root, key) {
    if (!key) return;
    root.querySelectorAll(".nav-menu-item.active, .user-profile-nav.active").forEach(function (n) {
      n.classList.remove("active");
    });
    var target = root.querySelector('[data-nav="' + key + '"]');
    if (target) target.classList.add("active");
  }

  function loadOnePartial(el) {
    var url = el.getAttribute("data-include");
    if (!url) return Promise.resolve();
    var resolved = url.startsWith("/") ? resolveUrl(url) : new URL(url, window.location.href).href;
    return fetch(resolved, { credentials: "same-origin" }).then(function (r) {
      if (!r.ok) throw new Error(resolved + " " + r.status);
      return r.text();
    }).then(function (html) {
      el.innerHTML = html;
    });
  }

  function loadAllPartials() {
    var nodes = document.querySelectorAll("[data-include]");
    var chain =
      nodes.length === 0
        ? Promise.resolve()
        : Promise.all(Array.prototype.map.call(nodes, loadOnePartial))
            .then(function () {
              var nav = document.getElementById("site-navbar");
              var active = (document.documentElement.getAttribute("data-nav-active") || "").trim();
              if (nav && active) applyNavActive(nav, active);
            })
            .catch(function () {
              var first = document.querySelector("[data-include]");
              if (first && first.id === "site-navbar") {
                first.innerHTML =
                  '<div class="container-xl py-2"><p class="text-danger small mb-0">' +
                  "Không tải được partial. Kiểm tra lại đường dẫn components.</p></div>";
              }
            });
    return chain.finally(function () {
      document.dispatchEvent(new CustomEvent("helios:partials-loaded"));
    });
  }

  injectCommonCss();

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", loadAllPartials);
  } else {
    loadAllPartials();
  }
})();