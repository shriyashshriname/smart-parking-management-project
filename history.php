<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$message = "";

if(isset($_GET['success'])) {
    if($_GET['success'] == 1) $message = "<div class='success-alert'>Payment successful! Paid with Smart Coins.</div>";
    if($_GET['success'] == 2) $message = "<div class='success-alert'>Payment successful! 3% Cashback credited to your wallet.</div>";
}

// Fetch Activity Logs for Transactions & Payments
$logs_res = $conn->query("SELECT * FROM activity_logs WHERE user_id=$user_id AND (action LIKE '%PURCHASE%' OR action LIKE '%PAYMENT%' OR action LIKE '%PENALTY%') ORDER BY created_at DESC");

// Fetch Vehicle History
$vehicles_res = $conn->query("SELECT *, UNIX_TIMESTAMP(exit_time) - UNIX_TIMESTAMP(entry_time) as duration_sec FROM vehicles WHERE user_id=$user_id ORDER BY entry_time DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - History</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-gold: #eab308; --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1200px; }
  .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; }
  .page-header p { color: var(--text-muted); margin-top: 5px; }

  .success-alert { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; }

  /* Tabs */
  .tabs { display: flex; gap: 15px; background: var(--panel-bg); padding: 5px; border-radius: 12px; border: 1px solid var(--border-color); width: fit-content; margin-bottom: 25px;}
  .tab-btn { background: transparent; color: var(--text-muted); border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.3s; }
  .tab-btn.active { background: var(--border-color); color: var(--text-main); }
  .tab-btn:hover:not(.active) { color: var(--text-main); }

  .tab-content { display: none; }
  .tab-content.active { display: block; }

  /* Table Styles */
  table { width: 100%; border-collapse: collapse; background: var(--panel-bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
  th, td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
  th { background: rgba(255,255,255,0.02); font-weight: 600; color: var(--text-muted); text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
  tr:last-child td { border-bottom: none; }
  
  .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
  .badge.pay { background: rgba(59,130,246,0.1); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
  .badge.penalty { background: rgba(239,68,68,0.1); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
  .badge.purchase { background: rgba(34,197,94,0.1); color: #86efac; border: 1px solid rgba(34,197,94,0.3); }
  .badge.active { background: rgba(234,179,8,0.1); color: var(--primary-gold); border: 1px solid rgba(234,179,8,0.3); }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <div>
      <h1>History Log</h1>
      <p>Track your payments, purchases, and parking durations.</p>
    </div>
  </div>

  <?php echo $message; ?>

  <div class="tabs">
    <button class="tab-btn active" onclick="switchTab('transactions')"><i class="fa fa-receipt"></i> Transactions</button>
    <button class="tab-btn" onclick="switchTab('vehicles')"><i class="fa fa-car"></i> Parking History</button>
  </div>

  <!-- Transactions Tab -->
  <div id="tab-transactions" class="tab-content active">
    <table>
      <thead>
        <tr>
          <th>Date & Time</th>
          <th>Type</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <?php if($logs_res->num_rows > 0): while($row = $logs_res->fetch_assoc()): ?>
          <?php
            $typeClass = 'pay';
            if(strpos($row['action'], 'PENALTY') !== false) $typeClass = 'penalty';
            if(strpos($row['action'], 'PURCHASE') !== false) $typeClass = 'purchase';
          ?>
          <tr>
            <td style="color: var(--text-muted);"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
            <td><span class="badge <?php echo $typeClass; ?>"><?php echo str_replace('_', ' ', $row['action']); ?></span></td>
            <td><?php echo htmlspecialchars($row['detail']); ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 30px;">No transaction history found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Vehicles Tab -->
  <div id="tab-vehicles" class="tab-content">
    <table>
      <thead>
        <tr>
          <th>Vehicle No</th>
          <th>Entry Time</th>
          <th>Exit Time</th>
          <th>Duration</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if($vehicles_res->num_rows > 0): while($row = $vehicles_res->fetch_assoc()): ?>
          <tr>
            <td style="font-family: monospace; font-size: 15px; font-weight: 600;"><?php echo htmlspecialchars($row['vehicle_no']); ?></td>
            <td style="color: var(--text-muted);"><?php echo date('M d, Y h:i A', strtotime($row['entry_time'])); ?></td>
            <td style="color: var(--text-muted);">
              <?php echo $row['exit_time'] ? date('M d, Y h:i A', strtotime($row['exit_time'])) : '-'; ?>
            </td>
            <td>
              <?php 
                if($row['exit_time']) {
                    $mins = floor($row['duration_sec'] / 60);
                    $hrs = floor($mins / 60);
                    $rem_mins = $mins % 60;
                    echo "{$hrs}h {$rem_mins}m";
                } else {
                    echo "-";
                }
              ?>
            </td>
            <td>
              <?php if($row['exit_time']): ?>
                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border-color);">Completed</span>
              <?php else: ?>
                <span class="badge active"><i class="fa fa-circle" style="font-size: 8px;"></i> Parked</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">No parking history found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</main>

<script>
  function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    event.currentTarget.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
  }
</script>

</body>
</html>
