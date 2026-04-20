<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
  header("Location: login.php");
  exit();
}
include 'db.php';

// Fetch all vehicles with user info
$vehicles_query = "
    SELECT v.*, u.name as user_name, u.email 
    FROM vehicles v
    LEFT JOIN users u ON v.user_id = u.id
    ORDER BY v.entry_time DESC
    LIMIT 100
";
$vehicles_res = $conn->query($vehicles_query);

// Fetch recent activity logs
$logs_query = "
    SELECT a.*, u.name as user_name, u.email 
    FROM activity_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 100
";
$logs_res = $conn->query($logs_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Admin Vehicles & Activity</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-gold: #eab308; --primary-blue: #3b82f6; --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; }

  .sidebar { width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color); position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px; }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s; }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto; }
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1200px; }
  h1 { font-family: 'Instrument Serif', serif; font-size: 36px; margin-bottom: 5px; }
  p.subtitle { color: var(--text-muted); margin-bottom: 30px; }

  .tabs { display: flex; gap: 20px; border-bottom: 1px solid var(--border-color); margin-bottom: 30px; }
  .tab { padding: 12px 24px; cursor: pointer; color: var(--text-muted); font-weight: 500; border-bottom: 2px solid transparent; transition: 0.2s; }
  .tab:hover { color: var(--text-main); }
  .tab.active { color: var(--primary-green); border-bottom-color: var(--primary-green); }

  .tab-content { display: none; }
  .tab-content.active { display: block; }

  /* Table styling */
  .panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  th { text-align: left; padding: 12px 16px; font-size: 13px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); white-space: nowrap; }
  td { padding: 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.03); }
  tr:hover td { background: rgba(255,255,255,0.02); }
  
  .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
  .badge-active { background: rgba(34,197,94,0.1); color: var(--primary-green); border: 1px solid rgba(34,197,94,0.2); }
  .badge-ended { background: rgba(161,161,170,0.1); color: var(--text-muted); border: 1px solid rgba(161,161,170,0.2); }

  .action-badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; min-width: 90px; text-align: center; }
  .bg-blue { background: rgba(59,130,246,0.1); color: var(--primary-blue); }
  .bg-green { background: rgba(34,197,94,0.1); color: var(--primary-green); }
  .bg-gold { background: rgba(234,179,8,0.1); color: var(--primary-gold); }
  .bg-red { background: rgba(239,68,68,0.1); color: var(--primary-red); }
  .bg-gray { background: rgba(161,161,170,0.1); color: var(--text-muted); }

  .user-cell { display: flex; flex-direction: column; }
  .user-cell strong { color: var(--text-main); font-weight: 500; }
  .user-cell small { color: var(--text-muted); font-size: 12px; }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <h1>User Tracking & Activity Logs</h1>
  <p class="subtitle">Monitor all parked vehicles and track real-time user interactions across the platform.</p>

  <div class="tabs">
    <div class="tab active" onclick="switchTab('vehicles')"><i class="fa fa-car"></i> Vehicle Tracking</div>
    <div class="tab" onclick="switchTab('logs')"><i class="fa fa-list"></i> Activity Logs</div>
  </div>

  <!-- Vehicles Tab -->
  <div id="tab-vehicles" class="tab-content active">
    <div class="panel">
      <table>
        <thead>
          <tr>
            <th>Vehicle No</th>
            <th>Slot ID</th>
            <th>User</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if($vehicles_res->num_rows > 0): ?>
            <?php while($row = $vehicles_res->fetch_assoc()): ?>
              <tr>
                <td><strong style="font-size: 15px; letter-spacing: 1px;"><?php echo htmlspecialchars($row['vehicle_no']); ?></strong></td>
                <td><span style="font-family: 'Instrument Serif', serif; font-size: 20px; color: var(--primary-gold);">#<?php echo $row['slot_id']; ?></span></td>
                <td class="user-cell">
                  <strong><?php echo htmlspecialchars($row['user_name'] ?? 'Guest/Unknown'); ?></strong>
                  <small><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></small>
                </td>
                <td style="color: var(--text-muted);"><?php echo date('M d, g:i A', strtotime($row['entry_time'])); ?></td>
                <td style="color: var(--text-muted);"><?php echo $row['exit_time'] ? date('M d, g:i A', strtotime($row['exit_time'])) : '--'; ?></td>
                <td>
                  <?php if(is_null($row['exit_time'])): ?>
                    <span class="badge badge-active">Active</span>
                  <?php else: ?>
                    <span class="badge badge-ended">Ended</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">No vehicles found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Logs Tab -->
  <div id="tab-logs" class="tab-content">
    <div class="panel">
      <table>
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Action</th>
            <th>User</th>
            <th>Details</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php if($logs_res->num_rows > 0): ?>
            <?php while($row = $logs_res->fetch_assoc()): 
                $action_class = 'bg-gray';
                if($row['action'] == 'LOGIN') $action_class = 'bg-green';
                elseif($row['action'] == 'LOGIN_FAILED') $action_class = 'bg-red';
                elseif($row['action'] == 'BOOK_SLOT') $action_class = 'bg-blue';
                elseif($row['action'] == 'PAYMENT') $action_class = 'bg-gold';
                elseif($row['action'] == 'EXIT_SLOT') $action_class = 'bg-gray';
                elseif($row['action'] == 'REGISTER') $action_class = 'bg-blue';
                elseif($row['action'] == 'UPDATE_PROFILE') $action_class = 'bg-gold';
            ?>
              <tr>
                <td style="color: var(--text-muted); white-space: nowrap;"><?php echo date('M d, H:i:s', strtotime($row['created_at'])); ?></td>
                <td><span class="action-badge <?php echo $action_class; ?>"><?php echo htmlspecialchars($row['action']); ?></span></td>
                <td class="user-cell">
                  <strong><?php echo htmlspecialchars($row['user_name'] ?? 'System/Guest'); ?></strong>
                  <small><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                </td>
                <td style="color: var(--text-main);"><?php echo htmlspecialchars($row['detail']); ?></td>
                <td style="color: var(--text-muted); font-size: 12px;"><?php echo htmlspecialchars($row['ip_address'] ?? 'Unknown'); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">No activity logs found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<script>
  function switchTab(tabId) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    event.currentTarget.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
  }
</script>

</body>
</html>
