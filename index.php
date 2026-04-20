<?php session_start(); $logged_in = isset($_SESSION["user_id"]); $role = $_SESSION["role"] ?? "user"; ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Valetra - Park Smarter</title><link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><style>:root{--bg:#09090b;--panel:#18181b;--border:#27272a;--text:#f4f4f5;--muted:#a1a1aa;--green:#22c55e;--gold:#eab308;--blue:#3b82f6;--purple:#a855f7;--red:#ef4444}*{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth}body{font-family:Inter,sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden}a{text-decoration:none;color:inherit}.nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:18px 60px;display:flex;justify-content:space-between;align-items:center;background:rgba(9,9,11,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}.brand{display:flex;align-items:center;gap:10px;font-family:"Instrument Serif",serif;font-size:26px}.nav-links{display:flex;gap:30px}.nav-links a{color:var(--muted);font-size:15px;transition:.2s}.nav-links a:hover{color:var(--text)}.nav-cta{display:flex;gap:12px}.btn-outline{border:1px solid var(--border);padding:8px 20px;border-radius:8px;font-size:14px;transition:.2s}.btn-outline:hover{border-color:#52525b}.btn-green{background:var(--green);color:#000;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;transition:.2s}.btn-green:hover{opacity:.9}.hero{min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;padding:120px 60px 80px}.hero-bg{position:absolute;inset:0;background:url("hero_parking.png") center/cover no-repeat;opacity:.25;z-index:0}.hero-overlay{position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(34,197,94,.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(59,130,246,.06) 0%,transparent 50%);z-index:1}.hero-content{position:relative;z-index:2;max-width:700px}.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--green);padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;margin-bottom:24px}.hero h1{font-family:"Instrument Serif",serif;font-size:72px;font-weight:400;line-height:1.1;margin-bottom:24px}.hero h1 em{color:var(--green);font-style:normal}.hero p{font-size:18px;color:var(--muted);max-width:540px;line-height:1.7;margin-bottom:40px}.hero-btns{display:flex;gap:16px;margin-bottom:60px}.btn-hero-primary{background:var(--green);color:#000;padding:14px 32px;border-radius:10px;font-size:16px;font-weight:600;transition:.2s}.btn-hero-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(34,197,94,.3)}.btn-hero-secondary{border:1px solid var(--border);padding:14px 32px;border-radius:10px;font-size:16px;transition:.2s}.btn-hero-secondary:hover{background:rgba(255,255,255,.05)}.hero-stats{display:flex;gap:48px}.stat-item{text-align:center}.stat-num{font-family:"Instrument Serif",serif;font-size:36px;color:var(--green)}.stat-lbl{font-size:13px;color:var(--muted)}.floating-card{position:absolute;right:60px;top:50%;transform:translateY(-50%);z-index:2;background:rgba(24,24,27,.9);border:1px solid var(--border);border-radius:20px;padding:30px;width:320px;backdrop-filter:blur(20px);animation:float 4s ease-in-out infinite}@keyframes float{0%,100%{transform:translateY(-50%) translateY(-10px)}50%{transform:translateY(-50%) translateY(10px)}}.fc-header{display:flex;align-items:center;gap:12px;margin-bottom:20px}.fc-dot{width:10px;height:10px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green)}.fc-title{font-size:14px;font-weight:600}.fc-slots{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:20px}.fc-slot{height:36px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600}.fc-avail{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.4);color:var(--green)}.fc-occ{background:#27272a;border:1px solid #3f3f46;color:#71717a}.fc-mine{background:var(--blue);border:1px solid var(--blue);color:#fff}.fc-footer{display:flex;justify-content:space-between;font-size:13px;color:var(--muted)}.section{padding:100px 60px}.section-label{font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--green);margin-bottom:12px}.section-title{font-family:"Instrument Serif",serif;font-size:48px;font-weight:400;margin-bottom:16px}.section-sub{color:var(--muted);font-size:17px;max-width:600px;line-height:1.7}.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:60px}.feat-card{background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:32px;transition:.3s;position:relative;overflow:hidden}.feat-card::before{content:"";position:absolute;top:0;left:0;right:0;height:2px;background:var(--accent, var(--green));opacity:0;transition:.3s}.feat-card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(0,0,0,.4)}.feat-card:hover::before{opacity:1}.feat-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px}.feat-card h3{font-size:20px;font-weight:600;margin-bottom:10px}.feat-card p{color:var(--muted);font-size:15px;line-height:1.6}.how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:40px;margin-top:60px;position:relative}.how-grid::before{content:"";position:absolute;top:40px;left:15%;right:15%;height:1px;background:linear-gradient(to right,transparent,var(--border),transparent)}.step-card{text-align:center;position:relative}.step-num{width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:"Instrument Serif",serif;font-size:32px;margin:0 auto 24px;border:2px solid var(--border);background:var(--panel);position:relative;z-index:1}.step-card h3{font-size:20px;margin-bottom:10px}.step-card p{color:var(--muted);font-size:15px;line-height:1.6}.stats-section{background:linear-gradient(135deg,rgba(34,197,94,.05) 0%,rgba(59,130,246,.05) 100%);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:0}.stats-item{text-align:center;padding:60px 40px;border-right:1px solid var(--border)}.stats-item:last-child{border-right:none}.stats-num{font-family:"Instrument Serif",serif;font-size:52px;margin-bottom:8px}.stats-lbl{color:var(--muted);font-size:15px}.plans-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:60px}.plan-mini{background:var(--panel);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:.3s}.plan-mini:hover{transform:translateY(-8px);box-shadow:0 15px 35px rgba(0,0,0,.5)}.plan-mini img{width:100%;height:160px;object-fit:cover}.plan-mini-body{padding:24px}.plan-mini-name{font-size:18px;font-weight:600;margin-bottom:4px}.plan-mini-price{font-family:"Instrument Serif",serif;font-size:30px;margin-bottom:16px}.plan-mini-btn{display:block;text-align:center;padding:10px;border-radius:8px;font-size:14px;font-weight:600;transition:.2s;border:1px solid}.plan-mini-btn:hover{opacity:.85}.testimonials{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:60px}.testi-card{background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:28px}.stars{color:var(--gold);margin-bottom:12px;font-size:14px}.testi-text{color:var(--muted);font-size:15px;line-height:1.7;margin-bottom:20px}.testi-author{display:flex;align-items:center;gap:12px}.testi-avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px}.testi-name{font-size:14px;font-weight:600}.testi-role{font-size:12px;color:var(--muted)}.faq-list{max-width:800px;margin:60px auto 0}.faq-item{border-bottom:1px solid var(--border)}.faq-q{width:100%;display:flex;justify-content:space-between;align-items:center;padding:22px 0;background:none;border:none;color:var(--text);font-size:17px;font-family:Inter,sans-serif;cursor:pointer;text-align:left}.faq-q i{color:var(--muted);transition:.3s}.faq-q.open i{transform:rotate(45deg);color:var(--green)}.faq-a{max-height:0;overflow:hidden;transition:max-height .35s ease,padding .35s}.faq-a.open{max-height:200px;padding-bottom:20px}.faq-a p{color:var(--muted);font-size:15px;line-height:1.7}.cta-section{padding:100px 60px;text-align:center;background:radial-gradient(ellipse at center,rgba(34,197,94,.08) 0%,transparent 70%)}.cta-section h2{font-family:"Instrument Serif",serif;font-size:56px;font-weight:400;margin-bottom:16px}.cta-section p{color:var(--muted);font-size:18px;margin-bottom:40px}.footer{background:var(--panel);border-top:1px solid var(--border);padding:60px}.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px}.footer-brand{font-family:"Instrument Serif",serif;font-size:24px;margin-bottom:12px}.footer-desc{color:var(--muted);font-size:14px;line-height:1.7;max-width:280px}.footer-col h4{font-size:14px;font-weight:600;margin-bottom:16px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}.footer-col a{display:block;color:var(--muted);font-size:14px;margin-bottom:10px;transition:.2s}.footer-col a:hover{color:var(--text)}.footer-bottom{border-top:1px solid var(--border);padding-top:30px;display:flex;justify-content:space-between;align-items:center}.footer-bottom p{color:var(--muted);font-size:14px}.social-links{display:flex;gap:16px}.social-links a{width:36px;height:36px;border-radius:8px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);transition:.2s}.social-links a:hover{border-color:var(--green);color:var(--green)}.reveal{opacity:0;transform:translateY(30px);transition:opacity .7s ease,transform .7s ease}.reveal.visible{opacity:1;transform:translateY(0)}@media(max-width:1024px){.hero h1{font-size:52px}.floating-card{display:none}.features-grid,.plans-grid{grid-template-columns:repeat(2,1fr)}.stats-row{grid-template-columns:repeat(2,1fr)}}@media(max-width:768px){.nav-links{display:none}.hero h1{font-size:40px}.section{padding:60px 24px}.features-grid,.plans-grid,.testimonials,.how-grid{grid-template-columns:1fr}.footer-grid{grid-template-columns:1fr 1fr}}</style>
<style>
  body, a, button, input, select {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%23eab308" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, auto !important;
  }
  /* Optional hover effect cursor (slightly larger or different color) */
  a:hover, button:hover {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%2322c55e" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, pointer !important;
  }
</style>

</head>
<body>
<nav class="nav">
  <div class="brand">
<svg width="32" height="32" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="valetraGold" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fef08a"/>
      <stop offset="40%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#713f12"/>
    </linearGradient>
  </defs>
  <path d="M50 90 L25 30 C35 30 45 50 50 70 C55 50 65 30 75 30 Z" fill="url(#valetraGold)"/>
  <path d="M15 35 C30 40 35 55 40 65 C30 55 20 45 10 35 Z" fill="url(#valetraGold)"/>
  <path d="M85 35 C70 40 65 55 60 65 C70 55 80 45 90 35 Z" fill="url(#valetraGold)"/>
</svg>
 Valetra</div>
  <div class="nav-links"><a href="#features">Features</a><a href="#how">How It Works</a><a href="subscriptions.php">Pricing</a><a href="#faq">FAQ</a><a href="#contact">Contact</a></div>
  <div class="nav-cta">
    <?php if($logged_in): ?>
      <a href="<?php echo $role==='admin'?'dashboard.php':'user_dashboard.php'; ?>" class="btn-green">Go to Dashboard</a>
    <?php else: ?>
      <a href="login.php" class="btn-outline">Sign In</a>
      <a href="register.php" class="btn-green">Get Started</a>
    <?php endif; ?>
  </div>
</nav>

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge"><span style="width:8px;height:8px;border-radius:50%;background:var(--green);display:inline-block;animation:pulse 2s infinite"></span> Live Slots Available Now</div>
    <h1>Command<br><em>Every Space</em></h1>
    <p>The AI-powered parking platform that finds, books, and secures your perfect spot in seconds. No stress, no circling, no wasted time.</p>
    <div class="hero-btns">
      <a href="register.php" class="btn-hero-primary"><i class="fa fa-bolt"></i> Start for Free</a>
      <a href="subscriptions.php" class="btn-hero-secondary"><i class="fa fa-star"></i> View Plans</a>
    </div>
    <div class="hero-stats">
      <div class="stat-item"><div class="stat-num" data-target="150">0</div><div class="stat-lbl">Total Slots</div></div>
      <div class="stat-item"><div class="stat-num" data-target="98">0</div><div class="stat-lbl">Uptime %</div></div>
      <div class="stat-item"><div class="stat-num" data-target="5000">0</div><div class="stat-lbl">Happy Drivers</div></div>
    </div>
  </div>
  <div class="floating-card">
    <div class="fc-header"><div class="fc-dot"></div><div class="fc-title">Live Floor Map</div><span style="margin-left:auto;font-size:12px;color:var(--muted)">Updating...</span></div>
    <div style="padding: 15px; display:flex; justify-content:center;"><img src="parking_loop.webp" style="width: 100%; max-width: 400px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 5px 15px rgba(0,0,0,0.5);" alt="Live Parking Video Loop"></div>
  </div>
</section>

<section class="section" id="features">
  <div class="section-label">Why Valetra</div>
  <div class="section-title">Everything you need,<br>nothing you don't</div>
  <p class="section-sub">From real-time slot tracking to EV charging zones and VIP reservations — we've built the complete parking experience.</p>
  <div class="features-grid">
    <div class="feat-card reveal" style="--accent:var(--green)">
      <div class="feat-icon" style="background:rgba(34,197,94,.1);color:var(--green)"><i class="fa fa-map-location-dot"></i></div>
      <h3>Real-Time Live Map</h3>
      <p>See every slot's live status on an interactive floor map. Your car is always highlighted with a glowing blue marker so you never get lost.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--gold)">
      <div class="feat-icon" style="background:rgba(234,179,8,.1);color:var(--gold)"><i class="fa fa-bolt"></i></div>
      <h3>EV Charging Zones</h3>
      <p>Dedicated EV bays with built-in charging stations. Reserve your charging slot in advance and arrive to a waiting, powered-up car.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--purple)">
      <div class="feat-icon" style="background:rgba(168,85,247,.1);color:var(--purple)"><i class="fa fa-crown"></i></div>
      <h3>VIP Platinum Slots</h3>
      <p>Premium covered spots closest to entrances with dedicated valet service. Arrive, hand over your keys, and walk straight in.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--blue)">
      <div class="feat-icon" style="background:rgba(59,130,246,.1);color:var(--blue)"><i class="fa fa-ticket-alt"></i></div>
      <h3>Instant Booking</h3>
      <p>BookMyShow-style visual slot selection. See the entire layout, pick your spot, and confirm in under 10 seconds. No calls needed.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--green)">
      <div class="feat-icon" style="background:rgba(34,197,94,.1);color:var(--green)"><i class="fa fa-coins"></i></div>
      <h3>Smart Coins Cashback</h3>
      <p>Earn 3% Smart Coins on every UPI or card payment. Redeem your coins for free bookings — the more you park, the more you save.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--gold)">
      <div class="feat-icon" style="background:rgba(234,179,8,.1);color:var(--gold)"><i class="fa fa-building"></i></div>
      <h3>Enterprise / B2B</h3>
      <p>Reserve blocks of VIP slots for your entire company, get corporate billing, API access, and a dedicated account manager.</p>
    </div>
  </div>
</section>

<section class="stats-section section"><div class="stats-row">
  <div class="stats-item reveal"><div class="stats-num" style="color:var(--green)" data-target="150">0</div><div class="stats-lbl">Parking Slots</div></div>
  <div class="stats-item reveal"><div class="stats-num" style="color:var(--gold)" data-target="4">0</div><div class="stats-lbl">Vehicle Categories</div></div>
  <div class="stats-item reveal"><div class="stats-num" style="color:var(--purple)" data-target="4">0</div><div class="stats-lbl">Subscription Plans</div></div>
  <div class="stats-item reveal"><div class="stats-num" style="color:var(--blue)" data-target="3">0</div><div class="stats-lbl">% Smart Coin Cashback</div></div>
</div></section>


<section class="section" id="impact" style="background:var(--bg);border-top:1px solid var(--border)">
  <div style="text-align:center">
    <div class="section-label">Social & Environmental Impact</div>
    <div class="section-title">Driving a better tomorrow</div>
    <p class="section-sub">Valetra isn't just about convenience. It's about creating smarter, greener, and safer cities.</p>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card reveal" style="--accent:var(--green)">
      <div class="feat-icon" style="background:rgba(34,197,94,.1);color:var(--green)"><i class="fa fa-leaf"></i></div>
      <h3>Reducing Traffic & Emissions</h3>
      <p>By eliminating the time spent circling for parking, we drastically reduce city congestion and cut down harmful vehicle emissions.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--blue)">
      <div class="feat-icon" style="background:rgba(59,130,246,.1);color:var(--blue)"><i class="fa fa-piggy-bank"></i></div>
      <h3>Economical Savings</h3>
      <p>Less idle driving means significantly lower fuel consumption. Our affordable, competitive pricing makes parking accessible to everyone.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--gold)">
      <div class="feat-icon" style="background:rgba(234,179,8,.1);color:var(--gold)"><i class="fa fa-shield-halved"></i></div>
      <h3>Enhanced Security</h3>
      <p>With precise digital tracking, dedicated VIP zones, and automated entry/exit logging, your vehicle is secured 24/7 in our monitored hubs.</p>
    </div>
  </div>
</section>

<section class="section" id="how" style="background:var(--panel);border-top:1px solid var(--border)">
  <div style="text-align:center">
    <div class="section-label">How It Works</div>
    <div class="section-title">Parked in 3 simple steps</div>
  </div>
  <div class="how-grid">
    <div class="step-card reveal"><div class="step-num" style="color:var(--green);border-color:rgba(34,197,94,.3)">1</div><h3>Create Account</h3><p>Sign up in 30 seconds. Add your vehicle details and payment method once. Everything auto-fills from then on.</p></div>
    <div class="step-card reveal"><div class="step-num" style="color:var(--gold);border-color:rgba(234,179,8,.3)">2</div><h3>Choose Your Slot</h3><p>Browse the live visual map, pick from VIP, EV, SUV or Normal zones, and confirm your booking with one tap.</p></div>
    <div class="step-card reveal"><div class="step-num" style="color:var(--blue);border-color:rgba(59,130,246,.3)">3</div><h3>Park & Relax</h3><p>Drive in, your slot is waiting. When done, hit "Exit Slot" from your dashboard and the system does the rest.</p></div>
  </div>
</section>

<section class="section" id="pricing">
  <div class="section-label">Pricing</div>
  <div class="section-title">Plans for every driver</div>
  <p class="section-sub">From the everyday commuter to large enterprises — all plans include Smart Coin cashback and live parking access.</p>
  <div class="plans-grid">
    <div class="plan-mini reveal"><img src="sub_basic.png" alt="Basic"><div class="plan-mini-body"><div class="plan-mini-name">Basic</div><div class="plan-mini-price" style="color:var(--muted)">?499<span style="font-size:16px;color:var(--muted)">/mo</span></div><a href="payment.php?plan=basic" class="plan-mini-btn" style="border-color:var(--muted);color:var(--muted)">Get Basic</a></div></div>
    <div class="plan-mini reveal"><img src="sub_premium.png" alt="Premium"><div class="plan-mini-body"><div class="plan-mini-name">Premium</div><div class="plan-mini-price" style="color:var(--gold)">?999<span style="font-size:16px;color:var(--muted)">/mo</span></div><a href="payment.php?plan=premium" class="plan-mini-btn" style="border-color:var(--gold);color:var(--gold)">Get Premium</a></div></div>
    <div class="plan-mini reveal"><img src="sub_ultimate.png" alt="Ultimate"><div class="plan-mini-body"><div class="plan-mini-name">Ultimate</div><div class="plan-mini-price" style="color:var(--purple)">?1999<span style="font-size:16px;color:var(--muted)">/mo</span></div><a href="payment.php?plan=ultimate" class="plan-mini-btn" style="border-color:var(--purple);color:var(--purple)">Get Ultimate</a></div></div>
    <div class="plan-mini reveal"><img src="sub_b2b.png" alt="B2B"><div class="plan-mini-body"><div class="plan-mini-name">Enterprise</div><div class="plan-mini-price" style="color:var(--blue)">?9999<span style="font-size:16px;color:var(--muted)">/mo</span></div><a href="payment.php?plan=b2b" class="plan-mini-btn" style="border-color:var(--blue);color:var(--blue)">Contact Sales</a></div></div>
  </div>
  <div style="text-align:center;margin-top:40px"><a href="subscriptions.php" class="btn-green" style="padding:14px 40px;border-radius:10px;font-size:16px">See Full Comparison <i class="fa fa-arrow-right"></i></a></div>
</section>

<section class="section" style="background:var(--panel);border-top:1px solid var(--border)">
  <div style="text-align:center"><div class="section-label">Testimonials</div><div class="section-title">Loved by drivers everywhere</div></div>
  <div class="testimonials">
    <div class="testi-card reveal"><div class="stars">?????</div><p class="testi-text">"I never worry about finding parking anymore. The live map is incredible — I can see my slot before I even leave home."</p><div class="testi-author"><div class="testi-avatar" style="background:rgba(34,197,94,.2);color:var(--green)">R</div><div><div class="testi-name">Rahul Sharma</div><div class="testi-role">Daily Commuter, Pune</div></div></div></div>
    <div class="testi-card reveal"><div class="stars">?????</div><p class="testi-text">"The Ultimate plan pays for itself. Free EV charging alone saves me ?2,000 a month, plus I love the VIP zone access."</p><div class="testi-author"><div class="testi-avatar" style="background:rgba(168,85,247,.2);color:var(--purple)">P</div><div><div class="testi-name">Priya Mehta</div><div class="testi-role">Tesla Owner, Mumbai</div></div></div></div>
    <div class="testi-card reveal"><div class="stars">?????</div><p class="testi-text">"The B2B plan is a game changer for our company. 10 reserved VIP slots for our executives and API integration made setup effortless."</p><div class="testi-author"><div class="testi-avatar" style="background:rgba(59,130,246,.2);color:var(--blue)">A</div><div><div class="testi-name">Ankit Joshi</div><div class="testi-role">Operations Manager, TechCorp</div></div></div></div>
  </div>
</section>

<section class="section" id="faq">
  <div style="text-align:center"><div class="section-label">FAQ</div><div class="section-title">Common Questions</div></div>
  <div class="faq-list">
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)">How does slot booking work? <i class="fa fa-plus"></i></button><div class="faq-a"><p>You select your preferred slot from our interactive live map (similar to BookMyShow seat selection). Choose your zone (VIP, EV, SUV, or Normal), confirm, and your slot is instantly reserved in our system.</p></div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)">What are Smart Coins? <i class="fa fa-plus"></i></button><div class="faq-a"><p>Smart Coins are our loyalty currency. You earn 3% of every payment you make via UPI or Card as Smart Coins. 1 Smart Coin = ?1. You can use your coins to pay for future bookings or subscriptions.</p></div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)">Can I see where my car is parked? <i class="fa fa-plus"></i></button><div class="faq-a"><p>Yes! Our "Find My Car" page shows a privacy-protected live map. Your car is highlighted with a pulsing blue glow, while other cars' details remain hidden to protect everyone's privacy.</p></div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)">How do I exit my parking slot? <i class="fa fa-plus"></i></button><div class="faq-a"><p>Simply go to your Dashboard, find your active parking session, and click the "Exit Slot" button. A confirmation dialog will appear, and once confirmed, your slot is immediately released and becomes available for others.</p></div></div>
    <div class="faq-item"><button class="faq-q" onclick="toggleFaq(this)">Is the B2B plan right for my company? <i class="fa fa-plus"></i></button><div class="faq-a"><p>The Enterprise/B2B plan is perfect for businesses that need dedicated parking for employees or executives. You get 10 reserved VIP slots, corporate billing, API integration, and a dedicated account manager.</p></div></div>
  </div>
</section>

<section class="cta-section" id="contact">
  <div class="section-label">Get Started Today</div>
  <h2>Your perfect spot<br>is waiting</h2>
  <p>Join thousands of smart drivers who never stress about parking.</p>
  <div style="display:flex;gap:16px;justify-content:center">
    <a href="register.php" class="btn-hero-primary" style="padding:16px 40px;font-size:17px;border-radius:10px;display:inline-block"><i class="fa fa-rocket"></i> Create Free Account</a>
    <a href="subscriptions.php" class="btn-hero-secondary" style="padding:16px 40px;font-size:17px;border-radius:10px;border:1px solid var(--border);display:inline-block">View Pricing</a>
  </div>
</section>


<section class="section" id="deepdive" style="background:#000;">
  <div style="text-align:center">
    <div class="section-label">Ecosystem</div>
    <div class="section-title">Command Your Network</div>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Drivers</h3>
      <p style="color:var(--muted)">Seamlessly locate, book, and pay for parking spots from your phone. Enjoy VIP zones, EV charging bays, and automated entry without ever rolling down your window.</p>
    </div>
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Businesses</h3>
      <p style="color:var(--muted)">Reserve blocks of spaces for your employees or clients. Utilize Valetra's B2B tools to manage corporate accounts and track fleet parking expenses dynamically.</p>
    </div>
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Operators</h3>
      <p style="color:var(--muted)">Transform your empty lots into smart hubs. Connect to the Valetra network to maximize utilization, automate billing, and increase your parking revenue instantly.</p>
    </div>
  </div>
</section>

<section class="section" id="testimonials" style="background:var(--bg);">
  <div style="text-align:center">
    <div class="section-label">Trust</div>
    <div class="section-title">Loved by Thousands</div>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"Valetra completely changed my commute. I no longer waste 20 minutes circling the block in Pune. I just book my slot and drive straight in."</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Rajesh K.</h4>
    </div>
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"The Smart Coin cashback is amazing. I use my cashback to buy car gear from the integrated store. Brilliant ecosystem!"</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Priya S.</h4>
    </div>
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"As a fleet manager, the B2B dashboard gives me total control over where my drivers are parking and exactly how much it costs."</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Amit D.</h4>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="footer-grid">
    <div><div class="footer-brand"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:8px"><path d="M12 2L14 9L21 12L14 15L12 22L10 15L3 12L10 9L12 2Z" fill="#eab308"/></svg>Valetra</div><p class="footer-desc">The AI-powered smart parking platform that makes urban commuting effortless, efficient, and enjoyable.</p></div>
    <div class="footer-col"><h4>Product</h4><a href="#features">Features</a><a href="subscriptions.php">Pricing</a><a href="book.php">Book Slot</a><a href="user_map.php">Live Map</a></div>
    <div class="footer-col"><h4>Account</h4><a href="login.php">Sign In</a><a href="register.php">Register</a><a href="profile.php">Profile</a><a href="user_dashboard.php">Dashboard</a></div>
    <div class="footer-col"><h4>Company</h4><a href="#contact">Contact</a><a href="#faq">FAQ</a><a href="subscriptions.php">Plans</a><a href="#">About Us</a></div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 Valetra. All rights reserved.</p>
    <div class="social-links">
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
      <a href="#"><i class="fa-brands fa-instagram"></i></a>
      <a href="#"><i class="fa-brands fa-linkedin"></i></a>
    </div>
  </div>
</footer>

<script>
const reveals=document.querySelectorAll(".reveal");
const obs=new IntersectionObserver(e=>{e.forEach(r=>{if(r.isIntersecting){r.target.classList.add("visible");obs.unobserve(r.target)}})},{threshold:.1});
reveals.forEach(r=>obs.observe(r));

document.querySelectorAll(".stat-num[data-target]").forEach(el=>{
  const target=+el.dataset.target;
  const obs2=new IntersectionObserver(entries=>{
    if(entries[0].isIntersecting){
      let c=0;const step=target/80;const t=setInterval(()=>{c=Math.min(c+step,target);el.textContent=target>100?Math.floor(c).toLocaleString():Math.floor(c)+(el.closest(".hero-stats")&&el.dataset.target==="98"?"%":"");if(c>=target)clearInterval(t)},20);obs2.unobserve(el)}
  },{threshold:.5});
  obs2.observe(el);
});

function toggleFaq(btn){
  const a=btn.nextElementSibling;
  const isOpen=a.classList.contains("open");
  document.querySelectorAll(".faq-a.open").forEach(x=>x.classList.remove("open"));
  document.querySelectorAll(".faq-q.open").forEach(x=>x.classList.remove("open"));
  if(!isOpen){a.classList.add("open");btn.classList.add("open")}
}

@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.4)}50%{box-shadow:0 0 0 8px rgba(34,197,94,0)}}
</script>
</body></html>
