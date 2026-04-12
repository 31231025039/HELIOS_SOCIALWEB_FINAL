/**
 * Trang chủ Helios: feed bài viết + dock tin nhắn.
 * Thêm bài: push object vào FEED_POSTS — không cần copy HTML.
 */
(function () {
  "use strict";

  /** @typedef {{ avatarBg: string, initials: string, name: string, connection: string, meta: string, body: string, hashtags?: string[], mediaLabel?: string | null, reactions: number, comments: number }} FeedPost */

  /** @type {FeedPost[]} */
  const FEED_POSTS = [
    {
      avatarBg: "bg-primary",
      initials: "TN",
      name: "Thanh Thảo Nguyễn",
      connection: "· 2nd",
      meta: "Senior Product Designer · 2 giờ trước",
      body:
        'Kỹ năng quan trọng nhất của một BA/Designer chính là thuyết phục stakeholder. Một bản thiết kế đẹp mà không "sell" được thì vẫn chỉ là file vẽ. 🚀 ',
      hashtags: ["HELIOS", "Career"],
      mediaLabel: "INSIGHT THIẾT KẾ",
      reactions: 234,
      comments: 48,
    },
    {
      avatarBg: "bg-success",
      initials: "TN",
      name: "Thảo Nguyên",
      connection: "· 3rd",
      meta: "Thực tập sinh Marketing · 4 giờ trước",
      body:
        "Mình vừa hoàn thành portfolio cá nhân sau 2 tuần chỉnh sửa liên tục. Bài học lớn nhất là: đừng đợi hoàn hảo mới bắt đầu nộp CV. ",
      hashtags: ["TạoCV", "ThựcTập"],
      mediaLabel: null,
      reactions: 126,
      comments: 21,
    },
    {
      avatarBg: "bg-info",
      initials: "HP",
      name: "Hải Phong",
      connection: "· 2nd",
      meta: "Data Analyst · 1 ngày trước",
      body:
        "Workshop “Phân tích dữ liệu cho người mới bắt đầu” sẽ mở đăng ký vào tối nay. Bạn nào muốn học thực chiến với dữ liệu thật thì tham gia nhé! ",
      hashtags: ["DataAnalytics", "PhòngHọcẢo"],
      mediaLabel: "WORKSHOP DỮ LIỆU",
      reactions: 302,
      comments: 67,
    },
  ];

  function escapeHtml(s) {
    if (s == null) return "";
    const d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  }

  function renderBodyHtml(post) {
    let html = escapeHtml(post.body);
    if (post.hashtags && post.hashtags.length) {
      html +=
        post.hashtags
          .map((h) => `<span class="hashtag">#${escapeHtml(h)}</span>`)
          .join(" ");
    }
    return html;
  }

  function renderPost(post) {
    const tpl = document.getElementById("feed-post-template");
    if (!tpl) return null;

    const frag = document.importNode(tpl.content, true);
    const article = frag.querySelector("article");
    if (!article) return null;

    const av = article.querySelector("[data-post-avatar]");
    if (av) {
      av.textContent = post.initials;
      av.className = "post-avatar " + (post.avatarBg || "bg-primary");
    }

    const nameEl = article.querySelector("[data-post-name]");
    if (nameEl) nameEl.textContent = post.name;

    const connOuter = article.querySelector("[data-post-connection-outer]");
    if (connOuter) {
      if (post.connection) {
        connOuter.textContent = " " + post.connection;
        connOuter.classList.remove("d-none");
      } else {
        connOuter.textContent = "";
        connOuter.classList.add("d-none");
      }
    }

    const metaEl = article.querySelector("[data-post-meta]");
    if (metaEl) metaEl.textContent = post.meta;

    const bodyEl = article.querySelector("[data-post-body]");
    if (bodyEl) bodyEl.innerHTML = renderBodyHtml(post);

    const mediaEl = article.querySelector("[data-post-media]");
    if (mediaEl) {
      if (post.mediaLabel) {
        mediaEl.textContent = post.mediaLabel;
        mediaEl.classList.remove("d-none");
      } else {
        mediaEl.classList.add("d-none");
      }
    }

    const reactCount = article.querySelector("[data-post-reactions]");
    if (reactCount) reactCount.textContent = post.reactions + " tương tác";

    const cmtCount = article.querySelector("[data-post-comments]");
    if (cmtCount) cmtCount.textContent = post.comments + " bình luận";

    return article;
  }

  function initFeed() {
    const root = document.getElementById("feed-posts");
    if (!root) return;
    const frag = document.createDocumentFragment();
    FEED_POSTS.forEach(function (p) {
      const el = renderPost(p);
      if (el) frag.appendChild(el);
    });
    root.appendChild(frag);
  }

  function initMessengerDock() {
    const root = document.getElementById("msgFloatingRoot");
    const panel = document.getElementById("msgPanel");
    const dockToggle = document.getElementById("msgDockToggle");
    const collapseBtn = document.getElementById("msgPanelCollapse");
    const composeHeader = document.getElementById("msgDockCompose");
    const composeDock = document.getElementById("msgDockBarCompose");

    if (!root || !panel || !dockToggle) return;

    function isOpen() {
      return root.classList.contains("is-open");
    }

    function setOpen(open) {
      if (open) {
        root.classList.add("is-open");
        panel.removeAttribute("hidden");
        dockToggle.setAttribute("aria-expanded", "true");
      } else {
        root.classList.remove("is-open");
        panel.setAttribute("hidden", "");
        dockToggle.setAttribute("aria-expanded", "false");
      }
    }

    dockToggle.addEventListener("click", function () {
      setOpen(!isOpen());
    });

    if (collapseBtn) {
      collapseBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        setOpen(false);
      });
    }

    function openForCompose(e) {
      e.stopPropagation();
      setOpen(true);
    }

    if (composeHeader) composeHeader.addEventListener("click", openForCompose);
    if (composeDock) composeDock.addEventListener("click", openForCompose);

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isOpen()) setOpen(false);
    });
  }

  function boot() {
    initFeed();
    initMessengerDock();
    // Tooltip xuất hiện trong tìm kiếm
  const searchBar = document.getElementById("searchBar");
  const searchTooltip = document.getElementById("searchTooltip");
  if (searchBar && searchTooltip) {
    searchBar.addEventListener("mouseenter", function () {
      searchTooltip.style.display = "block";
    });
    searchBar.addEventListener("mouseleave", function () {
      searchTooltip.style.display = "none";
    });
  }

  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
