<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Fetch active bookings for this user
$bookings_res = $conn->query("SELECT * FROM vehicles WHERE user_id=$user_id AND exit_time IS NULL ORDER BY entry_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - My Dashboard</title>
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
    --primary-blue: #3b82f6;
    --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color);
    position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px;
  }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a {
    display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none;
    padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s;
  }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto;}
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  /* Content */
  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1200px; }
  
  .welcome-banner {
    padding: 40px; border-radius: 20px; margin-bottom: 32px;
    background: linear-gradient(135deg, rgba(34,197,94,0.1) 0%, rgba(9,9,11,1) 100%), var(--panel-bg);
    border: 1px solid var(--border-color);
    display: flex; justify-content: space-between; align-items: center;
  }
  .welcome-banner h2 { font-family: 'Instrument Serif', serif; font-size: 40px; font-weight: 400; margin-bottom: 8px; }
  .welcome-banner p { color: var(--text-muted); }
  .wallet-widget { text-align: right; }
  .wallet-widget .coin-val { font-family: 'Instrument Serif', serif; font-size: 32px; color: #eab308; }
  .wallet-widget small { color: var(--text-muted); font-size: 13px; }
  .plan-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }

  .section-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
  
  .booking-card {
    background: var(--panel-bg); border: 1px solid var(--primary-green); border-radius: 16px; padding: 24px;
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;
    box-shadow: 0 0 20px rgba(34,197,94,0.05);
  }
  .booking-details h3 { font-size: 24px; color: var(--primary-green); margin-bottom: 5px; }
  .booking-details p { color: var(--text-muted); font-size: 14px; }
  
  .booking-actions { display: flex; gap: 15px; }
  .btn-find { background: rgba(59,130,246,0.1); color: var(--primary-blue); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: 0.2s; border: 1px solid rgba(59,130,246,0.3); }
  .btn-find:hover { background: rgba(59,130,246,0.2); }

  .empty-state { text-align: center; padding: 50px; background: var(--panel-bg); border: 1px dashed var(--border-color); border-radius: 16px; color: var(--text-muted); }
  .empty-state i { font-size: 40px; margin-bottom: 15px; opacity: 0.5; }
  .btn-primary { display: inline-block; background: var(--primary-green); color: var(--bg-color); padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 15px; }

  /* Subscription Recs */
  .sub-recs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
  .sub-rec-card {
    background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px;
    overflow: hidden; transition: 0.3s; text-decoration: none; color: var(--text-main);
  }
  .sub-rec-card:hover { transform: translateY(-4px); border-color: rgba(255,255,255,0.2); box-shadow: 0 10px 25px rgba(0,0,0,0.4); }
  .sub-rec-card img { width: 100%; height: 130px; object-fit: cover; }
  .sub-rec-info { padding: 16px; }
  .sub-rec-info h4 { font-size: 16px; margin-bottom: 5px; }
  .sub-rec-info .price { font-size: 20px; font-family: 'Instrument Serif', serif; margin-bottom: 10px; }
  .sub-rec-info p { font-size: 13px; color: var(--text-muted); }
  .btn-exit { background: rgba(239,68,68,0.1); color: var(--primary-red); padding: 10px 20px; border-radius: 8px; font-weight: 500; transition: 0.2s; border: 1px solid rgba(239,68,68,0.3); cursor: pointer; font-size: 14px; font-family: 'Inter', sans-serif; }
  .btn-exit:hover { background: rgba(239,68,68,0.2); }

  /* Confirm Modal */
  .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
  .modal-overlay.show { opacity:1; pointer-events: all; }
  .modal-box { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 40px; text-align: center; max-width: 400px; transform: scale(0.85); transition: 0.3s; }
  .modal-overlay.show .modal-box { transform: scale(1); }
  .modal-icon { font-size: 50px; color: var(--primary-red); margin-bottom: 15px; }
  .modal-box h2 { font-family: 'Instrument Serif', serif; font-size: 28px; margin-bottom: 10px; }
  .modal-box p { color: var(--text-muted); margin-bottom: 25px; }
  .modal-actions { display: flex; gap: 12px; justify-content: center; }
  .btn-modal-cancel { background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-muted); padding: 10px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; }
  .btn-modal-confirm { background: var(--primary-red); border: none; color: white; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  
  <?php
    $plan_colors = ['free'=>'#71717a','basic'=>'#a1a1aa','premium'=>'#eab308','ultimate'=>'#a855f7','b2b'=>'#3b82f6'];
    $current_plan = $user['plan'] ?? 'free';
    $plan_color = $plan_colors[$current_plan] ?? '#71717a';
  ?>
  <div class="welcome-banner">
    <div>
      <span class="plan-badge" style="background: <?php echo $plan_color; ?>20; color: <?php echo $plan_color; ?>; border: 1px solid <?php echo $plan_color; ?>40;"><?php echo ucfirst($current_plan); ?> Plan</span>
      <h2>Welcome back, <?php echo explode(' ', trim($_SESSION['name']))[0]; ?>!</h2>
      <p>View your active parking sessions and manage your account.</p>
    </div>
    <div class="wallet-widget">
      <small><i class="fa fa-coins"></i> Smart Coins</small><br>
      <div class="coin-val"><?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></div>
      <small>1 coin = ₹1 value</small>
    </div>
  </div>

  <h3 class="section-title"><i class="fa fa-car"></i> Active Parking Sessions</h3>

  <?php if($bookings_res->num_rows > 0): ?>
    <?php while($booking = $bookings_res->fetch_assoc()): ?>
      <div class="booking-card">
        <div class="booking-details">
          <h3>Slot #<?php echo $booking['slot_id']; ?></h3>
          <p>Vehicle: <strong><?php echo $booking['vehicle_no']; ?></strong></p>
          <p>Parked Since: <?php echo date('M d, g:i A', strtotime($booking['entry_time'])); ?></p>
        </div>
        <div class="booking-actions">
          <a href="user_map.php" class="btn-find"><i class="fa fa-location-crosshairs"></i> Locate Car</a>
          <button class="btn-exit"
            onclick="openExitModal(<?php echo $booking['id']; ?>, <?php echo $booking['slot_id']; ?>, '<?php echo $booking['vehicle_no']; ?>')"
          ><i class="fa fa-right-from-bracket"></i> Exit Slot</button>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="empty-state">
      <i class="fa fa-parking"></i>
      <h3>No Active Sessions</h3>
      <p>You don't have any vehicles currently parked.</p>
      <a href="book.php" class="btn-primary">Book a Slot Now</a>
    </div>
  <?php endif; ?>

  <?php if(in_array($current_plan, ['free', 'basic'])): ?>
  <h3 class="section-title" style="margin-top: 40px;"><i class="fa fa-star" style="color: #eab308;"></i> Upgrade Your Plan</h3>
  <div class="sub-recs-grid">
    <a href="payment.php?plan=premium" class="sub-rec-card">
      <img src="sub_premium.png" alt="Premium">
      <div class="sub-rec-info">
        <h4>Premium Plan</h4>
        <div class="price" style="color: #eab308;">₹999<span style="font-size:14px; color:var(--text-muted)">/mo</span></div>
        <p>SUV & Normal slots, 10% discount, Priority support & monthly valet.</p>
      </div>
    </a>
    <a href="payment.php?plan=ultimate" class="sub-rec-card">
      <img src="sub_ultimate.png" alt="Ultimate">
      <div class="sub-rec-info">
        <h4>Ultimate Plan</h4>
        <div class="price" style="color: #a855f7;">₹1999<span style="font-size:14px; color:var(--text-muted)">/mo</span></div>
        <p>VIP & EV slots, zero booking fees, free EV charging & parking assistant.</p>
      </div>
    </a>
    <a href="payment.php?plan=b2b" class="sub-rec-card">
      <img src="sub_b2b.png" alt="B2B">
      <div class="sub-rec-info">
        <h4>Enterprise / B2B</h4>
        <div class="price" style="color: #3b82f6;">₹9999<span style="font-size:14px; color:var(--text-muted)">/mo</span></div>
        <p>Reserve 10 slots, corporate billing, API access & dedicated account manager.</p>
      </div>
    </a>
  </div>
  <?php endif; ?>

</main>

<!-- Exit Confirmation Modal -->
<div class="modal-overlay" id="exitModal">
  <div class="modal-box">
    <div class="modal-icon"><i class="fa fa-circle-exclamation"></i></div>
    <h2>Exit Parking Slot?</h2>
    <p id="modalDesc">Are you sure you want to release your parking slot? Your slot will become available for others.</p>
    <form method="POST" action="exit_slot.php" id="exitForm">
      <input type="hidden" name="exit_booking" value="1">
      <input type="hidden" name="vehicle_id" id="exitVehicleId">
      <input type="hidden" name="slot_id" id="exitSlotId">
      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="closeExitModal()">Cancel</button>
        <button type="submit" class="btn-modal-confirm"><i class="fa fa-right-from-bracket"></i> Yes, Exit</button>
      </div>
    </form>
  </div>
</div>

<script>
function openExitModal(vehicleId, slotId, vehicleNo) {
  document.getElementById('exitVehicleId').value = vehicleId;
  document.getElementById('exitSlotId').value = slotId;
  document.getElementById('modalDesc').textContent =
    'Are you sure you want to exit Slot #' + slotId + ' for vehicle ' + vehicleNo + '? The slot will be released and available for others.';
  document.getElementById('exitModal').classList.add('show');
}
function closeExitModal() {
  document.getElementById('exitModal').classList.remove('show');
}
// Click outside to close
document.getElementById('exitModal').addEventListener('click', function(e) {
  if(e.target === this) closeExitModal();
});
</script>

</body>
</html>
