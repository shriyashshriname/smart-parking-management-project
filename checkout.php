<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$user_id = $_SESSION['user_id'];
$message = "";

if(!isset($_GET['product_id'])) {
    header("Location: store.php");
    exit();
}

$product_id = intval($_GET['product_id']);
$prod = $conn->query("SELECT * FROM products WHERE id=$product_id")->fetch_assoc();
if(!$prod) {
    header("Location: store.php");
    exit();
}

$user = $conn->query("SELECT wallet_balance, pin FROM users WHERE id=$user_id")->fetch_assoc();

if(isset($_POST['confirm_purchase'])) {
    $method = $_POST['payment_method'];
    $pin = $_POST['pin'];
    $price = $prod['price'];

    if($pin !== $user['pin']) {
        $message = "<div class='error-alert'>Invalid Security PIN! Transaction failed.</div>";
    } else {
        // PIN correct, check stock
        $current_stock = $conn->query("SELECT stock FROM products WHERE id=$product_id")->fetch_assoc()['stock'];
        if($current_stock > 0) {
            if($method == 'coins') {
                if($user['wallet_balance'] >= $price) {
                    $new_balance = $user['wallet_balance'] - $price;
                    $conn->query("UPDATE users SET wallet_balance=$new_balance WHERE id=$user_id");
                    $conn->query("UPDATE products SET stock = stock - 1 WHERE id=$product_id");
                    $conn->query("INSERT INTO orders (user_id, product_id, total_price, status) VALUES ($user_id, $product_id, $price, 'Processing')");
                    
                    log_activity($conn, $user_id, 'STORE_PURCHASE', "Bought {$prod['name']} for ₹$price using Smart Coins.");
                    header("Location: history.php?success=1");
                    exit();
                } else {
                    $message = "<div class='error-alert'>Insufficient Smart Coins. Please use Card/UPI.</div>";
                }
            } else {
                // Card/UPI payment
                $cashback = round($price * 0.03, 2);
                $conn->query("UPDATE users SET wallet_balance = wallet_balance + $cashback WHERE id=$user_id");
                $conn->query("UPDATE products SET stock = stock - 1 WHERE id=$product_id");
                $conn->query("INSERT INTO orders (user_id, product_id, total_price, status) VALUES ($user_id, $product_id, $price, 'Processing')");
                
                log_activity($conn, $user_id, 'STORE_PURCHASE', "Bought {$prod['name']} for ₹$price using Card/UPI. Earned ₹$cashback cashback.");
                header("Location: history.php?success=2");
                exit();
            }
        } else {
            $message = "<div class='error-alert'>Sorry, item just went out of stock.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - <?php echo htmlspecialchars($prod['name']); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-gold: #eab308; --primary-blue: #3b82f6; --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 900px; }
  .page-header { margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; }
  .page-header p { color: var(--text-muted); margin-top: 5px; }

  .error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }

  .checkout-container { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

  .product-summary { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; text-align: center; }
  .product-img { width: 100%; height: 200px; border-radius: 12px; object-fit: cover; margin-bottom: 20px; background: #27272a;}
  .product-title { font-size: 24px; font-weight: 600; margin-bottom: 10px; }
  .product-price { font-family: 'Instrument Serif', serif; font-size: 36px; color: var(--primary-gold); margin-bottom: 20px;}
  
  .payment-panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; }
  .form-group { margin-bottom: 20px; }
  .form-group label { display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600;}
  
  .pay-select, .pin-input {
    width: 100%; background: var(--bg-color); border: 1px solid var(--border-color);
    color: var(--text-main); padding: 12px 16px; border-radius: 8px; font-size: 15px; outline: none;
    transition: 0.3s;
  }
  .pay-select:focus, .pin-input:focus { border-color: var(--primary-gold); }

  .btn-confirm {
    width: 100%; background: var(--primary-green); color: #000; border: none; padding: 14px;
    border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.2s; margin-top: 10px;
  }
  .btn-confirm:hover { background: #1da851; transform: translateY(-2px); }

  .back-link { display: inline-block; color: var(--text-muted); text-decoration: none; margin-bottom: 20px; transition: 0.2s; }
  .back-link:hover { color: var(--text-main); }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <a href="store.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Store</a>
  
  <div class="page-header">
    <h1>Secure Checkout</h1>
    <p>Confirm your payment method and enter your security PIN to purchase.</p>
  </div>

  <?php echo $message; ?>

  <div class="checkout-container">
    <div class="product-summary">
        <?php 
          $img_src = 'placeholder.png';
          $n = strtolower($prod['name']);
          if(strpos($n, 'dashcam') !== false) $img_src = 'dashcam.png';
          if(strpos($n, 'tyre') !== false || strpos($n, 'inflator') !== false) $img_src = 'inflator.png';
          if(strpos($n, 'vacuum') !== false) $img_src = 'vacuum.png';
          if(strpos($n, 'fire') !== false) $img_src = 'fire_extinguisher.png';
          if(strpos($n, 'pillow') !== false) $img_src = 'neck_pillow.png';
          if(strpos($n, 'rescue') !== false) $img_src = 'rescuekit.png'; 
        ?>
      <img src="<?php echo $img_src; ?>" class="product-img" alt="<?php echo htmlspecialchars($prod['name']); ?>">
      <div class="product-title"><?php echo htmlspecialchars($prod['name']); ?></div>
      <div class="product-price">₹<?php echo number_format($prod['price']); ?></div>
      <p style="color: var(--text-muted); font-size: 14px;">Ships securely to your registered address.</p>
    </div>

    <div class="payment-panel">
      <form method="POST">
        <div class="form-group">
          <label>Select Payment Method</label>
          <select name="payment_method" class="pay-select" required>
            <option value="card">Card / UPI (Earn 3% Cashback)</option>
            <option value="coins">Smart Coins (Balance: ₹<?php echo number_format($user['wallet_balance'], 2); ?>)</option>
          </select>
        </div>

        <div class="form-group">
          <label>4-Digit Security PIN</label>
          <input type="password" name="pin" class="pin-input" maxlength="4" placeholder="Enter PIN (Default: 1234)" required autocomplete="off">
        </div>

        <button type="submit" name="confirm_purchase" class="btn-confirm">
          <i class="fa fa-lock"></i> Confirm Purchase
        </button>
      </form>
    </div>
  </div>

</main>
</body>
</html>
