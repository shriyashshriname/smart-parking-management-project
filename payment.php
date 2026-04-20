<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$user_id = $_SESSION['user_id'];
$plan = isset($_GET['plan']) ? $conn->real_escape_string($_GET['plan']) : 'basic';

// Plan details
$plans = [
  'basic'   => ['name' => 'Basic',    'price' => 499,  'color' => '#a1a1aa', 'img' => 'sub_basic.png'],
  'premium' => ['name' => 'Premium',  'price' => 999,  'color' => '#eab308', 'img' => 'sub_premium.png'],
  'ultimate'=> ['name' => 'Ultimate', 'price' => 1999, 'color' => '#a855f7', 'img' => 'sub_ultimate.png'],
  'b2b'     => ['name' => 'Enterprise / B2B', 'price' => 9999, 'color' => '#3b82f6', 'img' => 'sub_b2b.png'],
];

if (!array_key_exists($plan, $plans)) {
  header("Location: subscriptions.php");
  exit();
}

$plan_info = $plans[$plan];
$cashback = round($plan_info['price'] * 0.03, 2);

$message = "";

// Handle payment submission
if(isset($_POST['pay'])) {
  $method = $conn->real_escape_string($_POST['method']);
  $coins_used = 0;
  $paid_amount = $plan_info['price'];
  
  $user_row = $conn->query("SELECT wallet_balance FROM users WHERE id=$user_id")->fetch_assoc();
  $wallet = $user_row['wallet_balance'];
  
  if($method == 'coins') {
    if($wallet >= $paid_amount) {
      // Deduct from wallet
      $new_balance = $wallet - $paid_amount;
      $conn->query("UPDATE users SET plan='$plan', wallet_balance=$new_balance WHERE id=$user_id");
      log_activity($conn, $user_id, 'PAYMENT', "Paid ₹$paid_amount for $plan plan using Smart Coins");
      $message = "success";
    } else {
      $message = "insufficient";
    }
  } else {
    // UPI or Card — add 3% cashback in Smart Coins
    $new_wallet = $wallet + $cashback;
    $conn->query("UPDATE users SET plan='$plan', wallet_balance=$new_wallet WHERE id=$user_id");
    log_activity($conn, $user_id, 'PAYMENT', "Paid ₹$paid_amount for $plan plan using $method. Earned ₹$cashback cashback.");
    $message = "success_cashback";
  }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Checkout</title>
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
    --primary-red: #ef4444;
    --primary-gold: #eab308;
    --accent: <?php echo $plan_info['color']; ?>;
    --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color);
    position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px; z-index: 10;
  }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s; }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto;}
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  /* Content */
  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; }
  
  .checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 900px; }

  /* Plan Summary Card */
  .plan-summary {
    background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden;
  }
  .plan-summary img { width: 100%; height: 200px; object-fit: cover; }
  .plan-info { padding: 24px; }
  .plan-info h2 { font-family: 'Instrument Serif', serif; font-size: 28px; margin-bottom: 5px; }
  .plan-info .price-tag { font-size: 40px; font-family: 'Instrument Serif', serif; color: var(--accent); margin-bottom: 15px; }
  .cashback-pill { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: var(--primary-green); padding: 6px 14px; border-radius: 20px; font-size: 13px; display: inline-block; }
  .wallet-info { margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 14px; }
  .wallet-info strong { color: var(--primary-gold); }

  /* Payment Form */
  .payment-form-card {
    background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 30px;
  }
  .payment-form-card h2 { font-size: 22px; margin-bottom: 25px; }

  .method-group { margin-bottom: 25px; }
  .method-group label { display: block; font-size: 14px; color: var(--text-muted); margin-bottom: 12px; font-weight: 500;}
  
  .method-options { display: flex; flex-direction: column; gap: 12px; }
  .method-option {
    display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-radius: 10px;
    border: 1px solid var(--border-color); cursor: pointer; transition: 0.2s;
  }
  .method-option:has(input:checked) { border-color: var(--primary-green); background: rgba(34,197,94,0.05); }
  .method-option input[type="radio"] { accent-color: var(--primary-green); width: 16px; height: 16px; cursor: pointer; }
  .method-option i { font-size: 20px; color: var(--text-muted); }
  .method-option.upi i { color: #3b82f6; }
  .method-option.card i { color: #eab308; }
  .method-option.coins i { color: var(--primary-gold); }
  .method-option span { font-weight: 500; }
  .method-option small { margin-left: auto; font-size: 12px; color: var(--text-muted); }

  /* UPI/Card Details Sections */
  .detail-section { display: none; margin-top: 15px; padding: 15px; background: var(--bg-color); border-radius: 8px; border: 1px solid var(--border-color); }
  .detail-section.show { display: block; }
  .detail-section input {
    width: 100%; padding: 10px 14px; background: var(--panel-bg); border: 1px solid var(--border-color);
    border-radius: 8px; color: var(--text-main); font-size: 14px; outline: none; margin-bottom: 10px;
  }
  .detail-section input:focus { border-color: var(--primary-green); }
  
  .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

  .btn-pay {
    width: 100%; background: var(--accent); color: white; border: none; padding: 14px; border-radius: 10px;
    font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 20px;
  }
  .btn-pay:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,0,0,0.3); }

  /* Success Modal */
  .modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center;
    z-index: 1000; opacity: 0; pointer-events: none; transition: 0.3s;
  }
  .modal-overlay.show { opacity: 1; pointer-events: all; }
  .modal-box {
    background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 20px;
    padding: 40px; text-align: center; max-width: 420px; transform: scale(0.8); transition: 0.3s;
  }
  .modal-overlay.show .modal-box { transform: scale(1); }
  .modal-icon { font-size: 60px; color: var(--primary-green); margin-bottom: 20px; }
  .modal-box h2 { font-family: 'Instrument Serif', serif; font-size: 32px; margin-bottom: 10px; }
  .modal-box p { color: var(--text-muted); margin-bottom: 25px; font-size: 15px; }
  .btn-modal-go { display: inline-block; background: var(--primary-green); color: black; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div style="margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
    <h1 style="font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400;">Checkout</h1>
    <p style="color: var(--text-muted);">Complete your subscription to unlock premium benefits.</p>
  </div>

  <div class="checkout-grid">
    <!-- Plan Summary -->
    <div class="plan-summary">
      <img src="<?php echo $plan_info['img']; ?>" alt="<?php echo $plan_info['name']; ?>">
      <div class="plan-info">
        <h2><?php echo $plan_info['name']; ?> Plan</h2>
        <div class="price-tag">₹<?php echo number_format($plan_info['price']); ?></div>
        <span class="cashback-pill"><i class="fa fa-coins"></i> Earn ₹<?php echo $cashback; ?> Smart Coins (3% back!)</span>
        <div class="wallet-info">
          Your current Smart Coins balance: <strong><?php echo number_format($user['wallet_balance'], 2); ?> coins</strong><br>
          <small>(1 Smart Coin = ₹1)</small>
        </div>
      </div>
    </div>

    <!-- Payment Form -->
    <div class="payment-form-card">
      <h2>Select Payment Method</h2>
      <form method="POST" id="paymentForm">
        <div class="method-group">
          <label>How would you like to pay?</label>
          <div class="method-options">
            <label class="method-option upi">
              <input type="radio" name="method" value="upi" id="upiOpt" required>
              <i class="fa fa-mobile-alt"></i>
              <span>UPI Payment</span>
              <small>+3% Smart Coins cashback</small>
            </label>
            <label class="method-option card">
              <input type="radio" name="method" value="card" id="cardOpt">
              <i class="fa fa-credit-card"></i>
              <span>Credit / Debit Card</span>
              <small>+3% Smart Coins cashback</small>
            </label>
            <label class="method-option coins">
              <input type="radio" name="method" value="coins" id="coinsOpt">
              <i class="fa-brands fa-gg-circle"></i>
              <span>Pay with Smart Coins</span>
              <small><?php echo number_format($user['wallet_balance'], 2); ?> available</small>
            </label>
          </div>
        </div>

        <!-- UPI Details -->
        <div class="detail-section" id="upiSection">
          <input type="text" placeholder="Enter your UPI ID (e.g. name@upi)">
        </div>

        <!-- Card Details -->
        <div class="detail-section" id="cardSection">
          <input type="text" placeholder="Card Number" maxlength="19">
          <input type="text" placeholder="Cardholder Name">
          <div class="input-row">
            <input type="text" placeholder="MM/YY" maxlength="5">
            <input type="text" placeholder="CVV" maxlength="3">
          </div>
        </div>

        <button type="submit" name="pay" class="btn-pay" id="payBtn">
          Pay ₹<?php echo number_format($plan_info['price']); ?>
        </button>
      </form>
    </div>
  </div>
</main>

<!-- Success Modal -->
<div class="modal-overlay" id="successModal">
  <div class="modal-box">
    <div class="modal-icon"><i class="fa fa-circle-check"></i></div>
    <h2>Payment Successful!</h2>
    <p id="modalMsg">Your <?php echo $plan_info['name']; ?> plan is now active.</p>
    <a href="user_dashboard.php" class="btn-modal-go">Go to Dashboard</a>
  </div>
</div>

<script>
  // Show/hide payment detail sections
  document.querySelectorAll('input[name="method"]').forEach(r => {
    r.addEventListener('change', function() {
      document.getElementById('upiSection').classList.remove('show');
      document.getElementById('cardSection').classList.remove('show');
      if(this.value === 'upi') document.getElementById('upiSection').classList.add('show');
      if(this.value === 'card') document.getElementById('cardSection').classList.add('show');
    });
  });

  // Show success modal if payment was processed
  <?php if($message === 'success'): ?>
    const modal = document.getElementById('successModal');
    modal.classList.add('show');
    document.getElementById('modalMsg').textContent = 'Your <?php echo $plan_info['name']; ?> plan is now active. Paid with Smart Coins.';
  <?php elseif($message === 'success_cashback'): ?>
    const modal = document.getElementById('successModal');
    modal.classList.add('show');
    document.getElementById('modalMsg').textContent = 'Your <?php echo $plan_info['name']; ?> plan is now active! ₹<?php echo $cashback; ?> Smart Coins have been credited to your wallet.';
  <?php elseif($message === 'insufficient'): ?>
    alert('Insufficient Smart Coins balance! Please choose UPI or Card.');
  <?php endif; ?>
</script>
</body>
</html>
