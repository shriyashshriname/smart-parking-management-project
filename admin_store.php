<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$message = "";

// Update Order Status
if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    $conn->query("UPDATE orders SET status='$new_status' WHERE id=$order_id");
    log_activity($conn, $_SESSION['user_id'], 'UPDATE_ORDER', "Updated order #$order_id to $new_status");
    $message = "<div class='success-alert'>Order #$order_id status updated to $new_status.</div>";
}

// Fetch all orders
$orders = $conn->query("
    SELECT o.*, u.name as user_name, u.email, p.name as product_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN products p ON o.product_id = p.id
    ORDER BY o.created_at DESC
");

// Fetch all financial transactions
$transactions = $conn->query("
    SELECT a.*, u.name as user_name 
    FROM activity_logs a
    JOIN users u ON a.user_id = u.id
    WHERE a.action LIKE '%PURCHASE%' OR a.action LIKE '%PAYMENT%' OR a.action LIKE '%PENALTY%'
    ORDER BY a.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Valetra - Admin Store & Transactions</title>
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

  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1400px; }
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
  
  .badge.status-processing { background: rgba(234,179,8,0.1); color: var(--primary-gold); border: 1px solid rgba(234,179,8,0.3); }
  .badge.status-shipped { background: rgba(59,130,246,0.1); color: var(--primary-blue); border: 1px solid rgba(59,130,246,0.3); }
  .badge.status-delivered { background: rgba(34,197,94,0.1); color: var(--primary-green); border: 1px solid rgba(34,197,94,0.3); }

  .status-form { display: flex; gap: 10px; }
  .status-select { background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 6px 10px; border-radius: 6px; outline: none; }
  .btn-update { background: var(--primary-gold); color: #000; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; }
  .btn-update:hover { opacity: 0.9; }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <div>
      <h1>Store Orders & Transactions</h1>
      <p>Manage physical store orders and track global financial transactions.</p>
    </div>
  </div>

  <?php echo $message; ?>

  <div class="tabs">
    <button class="tab-btn active" onclick="switchTab('orders')"><i class="fa fa-box"></i> Physical Store Orders</button>
    <button class="tab-btn" onclick="switchTab('transactions')"><i class="fa fa-receipt"></i> Global Transactions</button>
  </div>

  <!-- Orders Tab -->
  <div id="tab-orders" class="tab-content active">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Product</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($orders->num_rows > 0): while($row = $orders->fetch_assoc()): ?>
          <tr>
            <td style="font-family: monospace;">#<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
            <td>
              <div><?php echo htmlspecialchars($row['user_name']); ?></div>
              <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($row['email']); ?></div>
            </td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td style="color: var(--primary-gold); font-weight: 600;">₹<?php echo number_format($row['total_price']); ?></td>
            <td style="color: var(--text-muted);"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
            <td><span class="badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
            <td>
              <form method="POST" class="status-form">
                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                <select name="status" class="status-select">
                  <option value="Processing" <?php echo $row['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                  <option value="Shipped" <?php echo $row['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                  <option value="Delivered" <?php echo $row['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                </select>
                <button type="submit" name="update_status" class="btn-update">Save</button>
              </form>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No store orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Transactions Tab -->
  <div id="tab-transactions" class="tab-content">
    <table>
      <thead>
        <tr>
          <th>User</th>
          <th>Date & Time</th>
          <th>Type</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <?php if($transactions->num_rows > 0): while($row = $transactions->fetch_assoc()): ?>
          <?php
            $typeClass = 'pay';
            if(strpos($row['action'], 'PENALTY') !== false) $typeClass = 'penalty';
            if(strpos($row['action'], 'PURCHASE') !== false) $typeClass = 'purchase';
          ?>
          <tr>
            <td style="font-weight: 500;"><?php echo htmlspecialchars($row['user_name']); ?></td>
            <td style="color: var(--text-muted);"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
            <td><span class="badge <?php echo $typeClass; ?>"><?php echo str_replace('_', ' ', $row['action']); ?></span></td>
            <td><?php echo htmlspecialchars($row['detail']); ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">No transaction history found.</td></tr>
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
