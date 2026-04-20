<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';

$message = "";

// No purchase logic here, handled in checkout.php
$products = $conn->query("SELECT * FROM products ORDER BY id ASC");
$user_data = $conn->query("SELECT wallet_balance FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Car Gear Store</title>
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

  .sidebar { width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color); position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px; z-index: 100; }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s; }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto; }
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1400px; }
  .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; }
  .page-header p { color: var(--text-muted); margin-top: 5px; }

  .wallet-pill { background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); color: var(--primary-gold); padding: 10px 20px; border-radius: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px; }

  .success-alert { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }
  .error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }

  .store-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
  
  .product-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; }
  .product-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
  
  .product-img { height: 200px; background: #27272a; display: flex; align-items: center; justify-content: center; font-size: 40px; color: var(--text-muted); }
  
  .product-info { padding: 24px; display: flex; flex-direction: column; flex: 1; }
  .product-category { font-size: 11px; font-weight: 600; color: var(--primary-green); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .product-title { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
  .product-desc { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; flex: 1; }
  
  .product-footer { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
  .product-price { font-family: 'Instrument Serif', serif; font-size: 28px; color: var(--text-main); }
  .product-stock { font-size: 12px; padding: 4px 10px; border-radius: 12px; background: rgba(255,255,255,0.05); color: var(--text-muted); }
  .product-stock.low { color: var(--primary-red); background: rgba(239,68,68,0.1); }
  
  .buy-form { display: flex; flex-direction: column; gap: 10px; }
  .pay-select { background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 10px; border-radius: 8px; font-size: 14px; outline: none; }
  .btn-buy { background: var(--text-main); color: #000; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
  .btn-buy:hover { background: #fff; transform: translateY(-2px); }
  .btn-buy:disabled { background: #3f3f46; color: #71717a; cursor: not-allowed; transform: none; }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <div>
      <h1>Safety & Gear Store</h1>
      <p>Premium car accessories delivered to your parking spot or home.</p>
    </div>
    <div class="wallet-pill">
      <i class="fa-brands fa-gg-circle"></i> <?php echo number_format($user_data['wallet_balance'], 2); ?> Smart Coins
    </div>
  </div>

  <?php echo $message; ?>

  <div class="store-grid">
    <?php while($row = $products->fetch_assoc()): ?>
      <div class="product-card">
        <div class="product-img">
          <?php 
            $img_src = 'placeholder.png'; // default fallback
            $n = strtolower($row['name']);
            if(strpos($n, 'dashcam') !== false) $img_src = 'dashcam.png';
            if(strpos($n, 'tyre') !== false || strpos($n, 'inflator') !== false) $img_src = 'inflator.png';
            if(strpos($n, 'vacuum') !== false) $img_src = 'vacuum.png';
            if(strpos($n, 'fire') !== false) $img_src = 'fire_extinguisher.png';
            if(strpos($n, 'pillow') !== false) $img_src = 'neck_pillow.png';
            // Assuming no image for rescue kit currently, it will fallback or use rescue_kit.png if we ever add it
            if(strpos($n, 'rescue') !== false) $img_src = 'rescuekit.png'; // Temporary fallback for rescue kit
          ?>
          <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="product-info">
          <div class="product-category"><?php echo htmlspecialchars($row['category']); ?></div>
          <div class="product-title"><?php echo htmlspecialchars($row['name']); ?></div>
          <div class="product-desc"><?php echo htmlspecialchars($row['description']); ?></div>
          
          <div class="product-footer">
            <div class="product-price">₹<?php echo number_format($row['price']); ?></div>
            <div class="product-stock <?php echo $row['stock'] < 5 ? 'low' : ''; ?>">
              <?php echo $row['stock'] > 0 ? $row['stock'].' in stock' : 'Out of Stock'; ?>
            </div>
          </div>

          <div style="margin-top: auto;">
            <?php if($row['stock'] > 0): ?>
              <a href="checkout.php?product_id=<?php echo $row['id']; ?>" class="btn-buy" style="display: block; text-align: center; text-decoration: none;">Buy Now</a>
            <?php else: ?>
              <button class="btn-buy" disabled style="width: 100%;">Sold Out</button>
            <?php endif; ?>
          </div>

        </div>
      </div>
    <?php endwhile; ?>
  </div>

</main>
</body>
</html>