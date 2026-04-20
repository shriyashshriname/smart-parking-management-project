<?php
session_start();
include 'db.php';
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Pricing</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-color: #09090b;
    --panel-bg: #18181b;
    --border-color: #27272a;
    --text-main: #f4f4f5;
    --text-muted: #a1a1aa;
    --primary-green: #22c55e;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); }
  
  .navbar {
    padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);
  }
  .brand { font-family: 'Instrument Serif', serif; font-size: 28px; display: flex; align-items: center; gap: 10px; color: white; text-decoration: none;}
  .nav-links a { color: var(--text-muted); text-decoration: none; margin-left: 20px; transition: 0.2s; }
  .nav-links a:hover { color: white; }
  
  .hero { text-align: center; padding: 80px 20px 40px; }
  .hero h1 { font-family: 'Instrument Serif', serif; font-size: 56px; margin-bottom: 15px; }
  .hero p { color: var(--text-muted); font-size: 18px; max-width: 600px; margin: 0 auto; }

  .pricing-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; padding: 40px; max-width: 1400px; margin: 0 auto;
  }

  .plan-card {
    background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; transition: transform 0.3s;
    display: flex; flex-direction: column;
  }
  .plan-card:hover { transform: translateY(-10px); border-color: rgba(255,255,255,0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
  
  .plan-img { width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid var(--border-color); }
  
  .plan-content { padding: 30px; display: flex; flex-direction: column; flex: 1; }
  .plan-name { font-size: 20px; font-weight: 600; margin-bottom: 10px; color: white;}
  .plan-price { font-family: 'Instrument Serif', serif; font-size: 40px; margin-bottom: 20px; color: var(--primary-green); }
  .plan-price span { font-size: 16px; font-family: 'Inter', sans-serif; color: var(--text-muted); }
  
  .features { list-style: none; margin-bottom: 30px; flex: 1;}
  .features li { margin-bottom: 12px; font-size: 14px; color: var(--text-muted); display: flex; align-items: center; gap: 10px;}
  .features li i { color: var(--primary-green); font-size: 12px; }

  .btn-subscribe {
    background: white; color: black; text-align: center; text-decoration: none; padding: 12px; border-radius: 8px; font-weight: 600; transition: 0.2s;
  }
  .btn-subscribe:hover { background: var(--primary-green); color: white; }

  /* Highlight Ultimate */
  .ultimate { border-color: #a855f7; box-shadow: 0 0 20px rgba(168, 85, 247, 0.1); }
  .ultimate .plan-price { color: #a855f7; }
  .ultimate .btn-subscribe { background: #a855f7; color: white; }
  .ultimate .features li i { color: #a855f7; }

  @media (max-width: 1024px) { .pricing-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { .pricing-grid { grid-template-columns: 1fr; } }

</style>
</head>
<body>

<nav class="navbar">
  <a href="login.php" class="brand">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L14 9L21 12L14 15L12 22L10 15L3 12L10 9L12 2Z" fill="#eab308"/></svg>
    Smart Parking
  </a>
  <div class="nav-links">
    <?php if($is_logged_in): ?>
      <a href="user_dashboard.php">Dashboard</a>
    <?php else: ?>
      <a href="login.php">Sign In</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</nav>

<div class="hero">
  <h1>Choose Your Parking Plan</h1>
  <p>Whether you're a daily commuter or an enterprise, we have a plan designed to guarantee your spot.</p>
</div>

<div class="pricing-grid">
  
  <!-- Basic -->
  <div class="plan-card">
    <img src="sub_basic.png" class="plan-img" alt="Basic">
    <div class="plan-content">
      <div class="plan-name">Basic Model</div>
      <div class="plan-price">₹499<span>/mo</span></div>
      <ul class="features">
        <li><i class="fa fa-check"></i> Easy Access to Normal Slots</li>
        <li><i class="fa fa-check"></i> 5% Discount on Bookings</li>
        <li><i class="fa fa-check"></i> Standard Customer Support</li>
        <li><i class="fa fa-check"></i> Save 4500/year</li>
      </ul>
      <a href="payment.php?plan=basic" class="btn-subscribe">Get Basic</a>
    </div>
  </div>

  <!-- Premium -->
  <div class="plan-card">
    <img src="sub_premium.png" class="plan-img" alt="Premium">
    <div class="plan-content">
      <div class="plan-name">Premium Model</div>
      <div class="plan-price" style="color: #eab308;">₹999<span>/mo</span></div>
      <ul class="features">
        <li><i class="fa fa-check" style="color: #eab308;"></i> Access to SUV & Normal Slots</li>
        <li><i class="fa fa-check" style="color: #eab308;"></i> 10% Discount on Bookings</li>
        <li><i class="fa fa-check" style="color: #eab308;"></i> Priority Support</li>
        <li><i class="fa fa-check" style="color: #eab308;"></i> Free Valet 15 times a Month</li>
      </ul>
      <a href="payment.php?plan=premium" class="btn-subscribe" style="background: #eab308; color: black;">Get Premium</a>
    </div>
  </div>

  <!-- Ultimate -->
  <div class="plan-card ultimate">
    <img src="sub_ultimate.png" class="plan-img" alt="Ultimate">
    <div class="plan-content">
      <div class="plan-name">Ultimate Model</div>
      <div class="plan-price">₹1999<span>/mo</span></div>
      <ul class="features">
        <li><i class="fa fa-check"></i> Access to VIP & EV Slots</li>
        <li><i class="fa fa-check"></i> Zero Booking Fees</li>
        <li><i class="fa fa-check"></i> Dedicated Parking Assistant</li>
        <li><i class="fa fa-check"></i> Free EV Charging</li>
        <li><i class="fa fa-check"></i> Unlock all new features first</li>
        <li><i class="fa fa-check"></i> Save upto 15,000/year</li>
      </ul>
      <a href="payment.php?plan=ultimate" class="btn-subscribe">Get Ultimate</a>
    </div>
  </div>

  <!-- B2B -->
  <div class="plan-card">
    <img src="sub_b2b.png" class="plan-img" alt="B2B">
    <div class="plan-content">
      <div class="plan-name">Enterprise / B2B</div>
      <div class="plan-price" style="color: #3b82f6;">₹9999<span>/mo</span></div>
      <ul class="features">
        <li><i class="fa fa-check" style="color: #3b82f6;"></i> Reserve up to 10 VIP Slots</li>
        <li><i class="fa fa-check" style="color: #3b82f6;"></i> Corporate Billing</li>
        <li><i class="fa fa-check" style="color: #3b82f6;"></i> API Access</li>
        <li><i class="fa fa-check" style="color: #3b82f6;"></i> Dedicated Account Manager</li>
        <li><i class="fa fa-check" style="color: #3b82f6;"></i> Personalize parking system for employees</li>
      </ul>
      <a href="payment.php?plan=b2b" class="btn-subscribe" style="background: #3b82f6; color: white;">Contact Sales</a>
    </div>
  </div>

</div>

</body>
</html>
