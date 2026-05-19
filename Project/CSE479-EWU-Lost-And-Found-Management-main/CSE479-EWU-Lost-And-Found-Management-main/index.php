<?php
// Attractive landing page at root with animated hero and quick links
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lost & Found · Home</title>
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <style>
      .hero {
        position: relative; overflow: hidden; border-radius: 20px;
        border: 1px solid rgba(148,163,184,0.15);
        min-height: 360px; display: grid; place-items: center;
        background: radial-gradient(1000px 500px at 30% 30%, #0ea5e9, #0f172a);
      }
      .orbs { position:absolute; inset:0; pointer-events:none; }
      .orb { position:absolute; width:220px; height:220px; border-radius:50%; filter: blur(40px); opacity:0.35; animation: float 12s ease-in-out infinite; }
      .orb.o1 { background:#60a5fa; left:10%; top:20%; }
      .orb.o2 { background:#f472b6; right:12%; top:30%; animation-delay: 3s; }
      .orb.o3 { background:#34d399; left:20%; bottom:12%; animation-delay: 6s; }
      @keyframes float { 0%{transform:translate3d(0,0,0)} 50%{transform:translate3d(20px,-14px,0)} 100%{transform:translate3d(0,0,0)} }
      .hero-title { font-size:2.2rem; font-weight:800; color:#fff; text-align:center; letter-spacing:0.4px; }
      .hero-sub { color:#cbd5e1; text-align:center; max-width:720px; margin:0.5rem auto 1rem; }
      .cta { display:flex; gap:0.8rem; justify-content:center; flex-wrap:wrap; }
      .marquee { overflow:hidden; white-space:nowrap; border-top:1px solid rgba(148,163,184,0.15); border-bottom:1px solid rgba(148,163,184,0.15); }
      .marquee-inner { display:inline-block; padding:0.6rem 0; animation: scrollX 18s linear infinite; color:#94a3b8; }
      @keyframes scrollX { from { transform: translateX(0);} to { transform: translateX(-50%);} }
    </style>
  </head>
  <body>
    <header class="app-header">
      <div class="app-header-inner container">
        <div class="brand"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" />Lost & Found</div>
        <button class="menu-toggle" data-menu-toggle>☰</button>
        <nav class="nav">
          <a href="/public/">Browse Items</a>
          <a href="/public/about.php">About</a>
          <a href="/public/contact.php">Contact</a>
          <a href="/public/submit.php">Post Found Item</a>
          <a href="/public/terms.php">Terms</a>
          <a href="/public/privacy.php">Policy</a>
          <a href="/admin/">Admin</a>
        </nav>
        <button class="btn" title="Change theme" data-theme-toggle>🌙</button>
      </div>
    </header>
    <main class="container" style="padding-top:1rem;">
      <section class="hero">
        <div class="orbs"><div class="orb o1"></div><div class="orb o2"></div><div class="orb o3"></div></div>
        <div>
          <h1 class="hero-title">Find it. Return it. Smile.</h1>
          <p class="hero-sub">A simple, beginner-friendly Lost & Found system to publish unclaimed items, approve public submissions, and manage messages—styled with cool CSS3 animations.</p>
          <div class="cta">
            <a class="btn primary" href="/public/">Start Browsing</a>
            <a class="btn" href="/public/submit.php">Post Found Item</a>
            <a class="btn" href="/admin/">Admin Login</a>
          </div>
        </div>
      </section>
      <div class="marquee" style="margin-top:1rem;">
        <div class="marquee-inner">Electronics · Documents · Bags · Clothing · Campus · Library · Hallway · Electronics · Documents · Bags · Clothing · Campus · Library · Hallway · </div>
      </div>

      <div class="grid cards" style="margin-top:1rem;">
        <div class="card"><div class="card-body"><div class="card-title">Publish Items</div><div class="muted">Browse and filter items by category.</div></div></div>
        <div class="card"><div class="card-body"><div class="card-title">Approve Submissions</div><div class="muted">Admin reviews public found-item posts.</div></div></div>
        <div class="card"><div class="card-body"><div class="card-title">Message Center</div><div class="muted">Handle inquiries and respond promptly.</div></div></div>
      </div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
    <script src="/assets/js/app.js"></script>
  </body>
</html>
