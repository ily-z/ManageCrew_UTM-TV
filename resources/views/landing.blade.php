<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, viewport-fit=cover"
    />
    <title>UTM TV – Sistem Administrasi Crew</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,800&family=Figtree:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <style>
      :root {
        --bg: #f7f7f7;
        --surface: #fff;
        --ink: #0f1330;
        --mute: #5b6080;
        --line: #e3e5ef;
        --blue: #00a0e3;
        --indigo: #283593;
        --yellow: #ffd400;
        --orange: #ff4500;
        --glow: rgba(0, 160, 227, 0.16);
        color-scheme: light;
        box-sizing: border-box;
        padding-top: env(safe-area-inset-top, 0px);
        padding-bottom: env(safe-area-inset-bottom, 0px);
      }
      @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) {
          --bg: #0b0e24;
          --surface: #141836;
          --ink: #f2f4ff;
          --mute: #9aa0c8;
          --line: #262b55;
          --glow: rgba(255, 100, 0, 0.14);
          color-scheme: dark;
        }
      }
      :root[data-theme="dark"] {
        --bg: #0b0e24;
        --surface: #141836;
        --ink: #f2f4ff;
        --mute: #9aa0c8;
        --line: #262b55;
        --glow: rgba(255, 100, 0, 0.14);
        color-scheme: dark;
      }
      * {
        box-sizing: border-box;
      }
      html {
        scroll-padding-top: env(safe-area-inset-top, 0px);
      }
      body {
        margin: 0;
        background: var(--bg);
        color: var(--ink);
        font:
          16px/1.6 Figtree,
          system-ui,
          sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
      }
      h1,
      h2,
      h3 {
        font-family: "Bricolage Grotesque", Figtree, system-ui, sans-serif;
        line-height: 1.05;
        margin: 0;
      }
      .grad {
        background: linear-gradient(
          90deg,
          var(--blue),
          var(--indigo) 30%,
          var(--yellow) 55%,
          var(--orange)
        );
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
      .logo-card img {
        display: block;
        width: 100%;
        height: auto;
      }
      :root[data-theme="dark"] .wm {
        filter: invert(1);
      }
      @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) .wm {
          filter: invert(1);
        }
      }
      .view {
        display: none;
      }
      .view.on {
        display: block;
      }
      a,
      button {
        font: inherit;
      }
      :focus-visible {
        outline: 3px solid var(--blue);
        outline-offset: 3px;
        border-radius: 10px;
      }
      /* hero */
      .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 22px 90px;
        overflow: hidden;
        background:
          radial-gradient(900px 500px at 15% 0, var(--glow), transparent 60%),
          radial-gradient(900px 500px at 90% 100%, var(--glow), transparent 60%);
      }
      .pillars {
        position: absolute;
        inset: auto 0 0 0;
        height: 46%;
        display: flex;
        gap: 2.2vw;
        justify-content: center;
        align-items: flex-end;
        pointer-events: none;
        opacity: 0.9;
      }
      .pillars i {
        width: min(9vw, 84px);
        border-radius: 999px 999px 0 0;
        transform-origin: bottom;
        animation: rise 1.1s cubic-bezier(0.2, 0.9, 0.2, 1) both;
      }
      .pillars i:nth-child(1) {
        height: 55%;
        background: linear-gradient(90deg, var(--blue), var(--indigo));
      }
      .pillars i:nth-child(2) {
        height: 38%;
        background: linear-gradient(90deg, var(--blue), var(--indigo));
        animation-delay: 0.08s;
      }
      .pillars i:nth-child(3) {
        height: 80%;
        background: linear-gradient(90deg, var(--yellow), #ffa000);
        animation-delay: 0.16s;
      }
      .pillars i:nth-child(4) {
        height: 62%;
        background: linear-gradient(90deg, #ffa000, var(--orange));
        animation-delay: 0.24s;
      }
      .pillars i:nth-child(5) {
        height: 45%;
        background: linear-gradient(90deg, var(--yellow), var(--orange));
        animation-delay: 0.32s;
      }
      @keyframes rise {
        from {
          transform: scaleY(0);
        }
      }
      .hero > *:not(.pillars) {
        position: relative;
        z-index: 1;
      }
      .logo-card {
        width: min(260px, 62vw);
      }

      .badge {
        margin-top: 26px;
        padding: 8px 20px;
        border: 1px solid var(--line);
        border-radius: 999px;
        background: var(--surface);
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--indigo);
      }
      @media (prefers-color-scheme: dark) {
        .badge {
          color: var(--blue);
        }
      }
      .hero h1 {
        font-size: clamp(2.4rem, 7vw, 5rem);
        font-weight: 800;
        letter-spacing: -0.035em;
        margin-top: 22px;
        max-width: 15ch;
      }
      .hero p {
        max-width: 68ch;
        color: var(--mute);
        font-size: 1.1rem;
        margin: 20px 0 30px;
      }
      .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: 0;
        cursor: pointer;
        border-radius: 999px;
        padding: 16px 34px;
        font-weight: 600;
        font-size: 1.05rem;
        text-decoration: none;
        transition:
          transform 0.15s,
          box-shadow 0.15s;
      }
      .btn.main {
        color: #fff;
        background: linear-gradient(90deg, var(--blue), var(--indigo));
        box-shadow: 0 12px 30px -10px var(--indigo);
      }
      .btn.main:hover {
        transform: translateY(-2px);
      }
      .btn.alt {
        background: var(--surface);
        color: var(--ink);
        border: 1px solid var(--line);
      }
      .cta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
      }
      .foot {
        position: absolute;
        bottom: 14px;
        left: 0;
        right: 0;
        z-index: 2;
        text-align: center;
        font-size: 0.85rem;
        color: var(--mute);
        background: linear-gradient(transparent, var(--bg) 60%);
        padding-top: 30px;
      }
      .foot a {
        color: var(--mute);
        margin: 0 10px;
      }
      /* features */
      .sec {
        max-width: 1080px;
        margin: 0 auto;
        padding: 90px 22px;
      }
      .sec h2 {
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        max-width: 18ch;
      }
      .sec > p {
        color: var(--mute);
        max-width: 56ch;
        margin: 14px 0 40px;
      }
      .grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
      }
      .f {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 26px;
        padding: 28px;
        grid-column: span 2;
        position: relative;
        overflow: hidden;
      }
      .f.w {
        grid-column: span 3;
      }
      .f.big {
        grid-column: span 4;
      }
      .f h3 {
        font-size: 1.4rem;
        font-weight: 800;
        margin: 44px 0 8px;
        letter-spacing: -0.02em;
      }
      .f p {
        margin: 0;
        color: var(--mute);
        max-width: 42ch;
      }
      .f b {
        position: absolute;
        top: 24px;
        left: 28px;
        width: 34px;
        height: 12px;
        border-radius: 99px;
      }
      .f:nth-child(1) b {
        background: linear-gradient(90deg, var(--blue), var(--indigo));
        width: 60px;
      }
      .f:nth-child(2) b {
        background: linear-gradient(90deg, var(--yellow), var(--orange));
      }
      .f:nth-child(3) b {
        background: var(--blue);
      }
      .f:nth-child(4) b {
        background: linear-gradient(90deg, var(--yellow), #ffa000);
        width: 48px;
      }
      .f:nth-child(5) b {
        background: var(--orange);
      }
      @media (max-width: 760px) {
        .f,
        .f.w,
        .f.big {
          grid-column: span 6;
        }
        .pillars i:nth-child(n + 5) {
          display: none;
        }
      }
      .band {
        background: linear-gradient(
          100deg,
          var(--indigo),
          #1a237e 40%,
          #b73a00 100%
        );
        color: #fff;
        text-align: center;
        padding: 70px 22px;
      }
      .band h2 {
        font-size: clamp(1.7rem, 4vw, 2.6rem);
        font-weight: 800;
        margin: 0 auto 12px;
        max-width: 20ch;
      }
      .band p {
        opacity: 0.85;
        margin: 0 0 26px;
      }
      .band .btn {
        background: #fff;
        color: var(--indigo);
      }
      footer {
        padding: 26px 22px;
        text-align: center;
        font-size: 0.85rem;
        color: var(--mute);
      }
      /* login */
      .lg {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 28px 18px;
        background:
          radial-gradient(
            800px 500px at 20% 10%,
            rgba(0, 160, 227, 0.25),
            transparent 60%
          ),
          radial-gradient(
            800px 500px at 85% 95%,
            rgba(255, 100, 0, 0.22),
            transparent 60%
          ),
          #0b0e24;
      }
      .card {
        width: min(460px, 100%);
        background: var(--surface);
        border-radius: 32px;
        padding: 30px 30px 20px;
        box-shadow: 0 40px 90px -30px #000;
      }
      .card .logo-card {
        width: 170px;
        margin: 0 auto 18px;
      }
      .card h2 {
        font-size: 1.6rem;
        text-align: center;
        font-weight: 800;
        letter-spacing: -0.02em;
      }
      .card .sub {
        text-align: center;
        color: var(--mute);
        margin: 6px 0 22px;
        font-size: 0.95rem;
      }
      label {
        display: block;
        font-weight: 600;
        font-size: 0.92rem;
        margin: 14px 0 6px;
      }
      .in {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid var(--line);
        background: var(--bg);
        border-radius: 16px;
        padding: 0 14px;
      }
      .in:focus-within {
        border-color: var(--blue);
        box-shadow: 0 0 0 4px var(--glow);
      }
      .in input {
        flex: 1;
        border: 0;
        background: none;
        color: var(--ink);
        padding: 14px 0;
        font: inherit;
        outline: 0;
        min-width: 0;
      }
      .in button {
        border: 0;
        background: none;
        color: var(--blue);
        font-weight: 600;
        cursor: pointer;
      }
      .card .btn {
        width: 100%;
        margin-top: 20px;
        border-radius: 16px;
        padding: 15px;
      }
      .demo {
        margin-top: 20px;
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 16px;
        background: var(--bg);
      }
      .demo h3 {
        font-size: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
      }
      .tag {
        font: 600 0.75rem Figtree;
        background: rgba(0, 160, 227, 0.14);
        color: var(--blue);
        padding: 3px 10px;
        border-radius: 99px;
      }
      .demo p {
        font-size: 0.85rem;
        color: var(--mute);
        margin: 6px 0 12px;
      }
      .pov {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
      }
      .pov button {
        text-align: left;
        background: var(--surface);
        border: 1px solid var(--line);
        color: var(--ink);
        border-radius: 14px;
        padding: 11px 12px;
        cursor: pointer;
        transition: border-color 0.15s;
      }
      .pov button:hover {
        border-color: var(--orange);
      }
      .pov span {
        display: block;
        font-size: 0.78rem;
        color: var(--mute);
      }
      .note {
        font-size: 0.78rem;
        color: var(--mute);
        text-align: center;
        margin-top: 10px;
      }
      .back {
        display: block;
        text-align: center;
        margin: 16px 0 4px;
        color: var(--blue);
        font-weight: 600;
        text-decoration: none;
      }
      .msg {
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        font-size: 0.9rem;
        display: none;
      }
      .msg.ok {
        display: block;
        background: rgba(0, 160, 120, 0.14);
        color: #0a8f6a;
      }
      .msg.err {
        display: block;
        background: rgba(255, 69, 0, 0.12);
        color: var(--orange);
      }
      @media (prefers-reduced-motion: reduce) {
        * {
          animation: none !important;
          transition: none !important;
        }
      }
      .pillars {
        height: 32%;
        opacity: 0.3;
      }
      .hero {
        padding-bottom: 120px;
      }
      .hero {
        padding: 24px 22px 90px;
      }
      .logo-card {
        width: min(210px, 26vh);
      }
      .hero h1 {
        font-size: clamp(2.2rem, 5.2vw, 4.4rem);
        margin-top: 16px;
      }
      .hero p {
        margin: 14px 0 24px;
        font-size: 1.05rem;
      }
      .badge {
        margin-top: 16px;
      }
      .pillars {
        height: 24%;
        opacity: 0.35;
        z-index: 0;
      }
      .in button {
        display: grid;
        place-items: center;
        color: var(--mute);
        padding: 4px;
      }
      .in button:hover {
        color: var(--blue);
      }
    </style>
  </head>
  <body>
    <main id="home" class="view on">
      <section class="hero">
        <div class="logo-card">
          <img data-m alt="Logo UTM TV" width="420" height="322" /><img
            class="wm"
            data-w
            alt="Integritas Terpercaya"
            width="420"
            height="140"
          />
        </div>
        <div class="badge">Sistem Administrasi Crew</div>
        <h1><span class="grad">Media Penyiaran Terpercaya.</span></h1>
        <p>
          UTM TV merupakan media resmi kampus Universitas Trunodjoyo Madura
          dibawah naungan BAKK. Yang bergerak dibidang penyiaran, jurnalistik,
          dan media kreatif untuk informasi, edukasi, dan publikasi kegiatan
          kampus
        </p>
        <div class="cta">
          <a class="btn main" href="/login">Login ke Dashboard</a>
        </div>
        <div class="pillars" aria-hidden="true">
          <i></i><i></i><i></i><i></i><i></i>
        </div>
        <div class="foot"></div>
      </section>
    </main>
    <script>
      (function () {
        var M =
            "data:image/png;base64,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",
          W =
            "data:image/png;base64,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";
        document.querySelectorAll("[data-m]").forEach(function (x) {
          x.src = M;
        });
        document.querySelectorAll("[data-w]").forEach(function (x) {
          x.src = W;
        });
        var cta = document.querySelector(".cta .btn.main");
        if (cta && localStorage.getItem("redaksi_token")) {
          cta.textContent = "Buka Dashboard";
          cta.href = "/beranda";
        }
      })();
    </script>
  </body>
</html>
