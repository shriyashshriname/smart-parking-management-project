<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
  header("Location: login.php");
  exit();
}
include 'db.php';

// Fetch all slots
$slots_result = $conn->query("SELECT * FROM slots ORDER BY slot_id ASC");
$slots_db = [];
while($row = $slots_result->fetch_assoc()) {
  $slots_db[$row['slot_id']] = $row;
}

// Fetch active vehicles to overlay on slots
$vehicles_res = $conn->query("SELECT slot_id, vehicle_no FROM vehicles WHERE exit_time IS NULL");
$active_vehicles = [];
while($v = $vehicles_res->fetch_assoc()) {
    $active_vehicles[$v['slot_id']] = $v['vehicle_no'];
}

// Stats
$total = count($slots_db);
$occupied = count($active_vehicles);
$available = $total - $occupied;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="10"> <!-- Auto refresh every 10 seconds -->
<title>Smart Parking - Admin Live Map</title>

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
    --primary-gold: #eab308;
    --sidebar-width: 260px;
    --seat-size: 40px;
    --seat-gap: 8px;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar { width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color); position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px; z-index: 100; }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; color: var(--text-main); margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu li { margin-bottom: 8px; }
  .nav-menu a { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; font-size: 15px; font-weight: 500; transition: all 0.2s; }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255, 255, 255, 0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { margin-top: auto; display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; font-size: 15px; transition: all 0.2s; }
  .logout-btn:hover { background: rgba(239, 68, 68, 0.1); color: var(--primary-red); }

  /* Main Content */
  .content { flex: 1; margin-left: var(--sidebar-width); padding: 40px; max-width: 1400px; }
  .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; }
  .page-header p { color: var(--text-muted); margin-top: 5px; }

  /* Live Stats */
  .live-stats { display: flex; gap: 20px; }
  .stat-badge { background: var(--panel-bg); border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 12px; text-align: center; }
  .stat-badge .num { font-family: 'Instrument Serif', serif; font-size: 28px; }
  .stat-badge .lbl { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

  /* Map Container */
  .layout-container { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 40px; overflow-x: auto; }

  .category-section { margin-bottom: 50px; }
  .category-title { text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 15px; }
  .category-title::before, .category-title::after { content: ''; height: 1px; width: 100px; background: var(--border-color); }

  .seat-row { display: flex; align-items: center; justify-content: center; margin-bottom: var(--seat-gap); gap: var(--seat-gap); }
  .row-label { width: 30px; text-align: right; font-size: 13px; color: var(--text-muted); font-weight: 600; padding-right: 15px;}
  .seat-group { display: flex; gap: var(--seat-gap); }
  .seat-gap-large { width: 40px; }

  .seat {
    width: var(--seat-size); height: var(--seat-size); border-radius: 6px; display: flex; flex-direction: column; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 600; transition: all 0.2s; position: relative;
  }
  .seat .v-no { font-size: 9px; color: #fff; margin-top: 2px; }

  /* Seat States */
  .seat.available { border: 1px solid var(--primary-green); color: var(--primary-green); background: rgba(34, 197, 94, 0.05); }
  .seat.occupied { background: #27272a; color: #a1a1aa; border: 1px solid #3f3f46; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
  .seat.occupied .v-no { color: #f4f4f5; }

  /* Legend */
  .legend-bar { display: flex; justify-content: center; gap: 30px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); }
  .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); }
  .legend-box { width: 16px; height: 16px; border-radius: 4px; }
  .lb-available { border: 1px solid var(--primary-green); background: rgba(34, 197, 94, 0.05); }
  .lb-occupied { background: #27272a; border: 1px solid #3f3f46; }

  .live-indicator {
    display: inline-flex; align-items: center; gap: 8px; background: rgba(239,68,68,0.1); color: var(--primary-red);
    padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(239,68,68,0.3);
  }
  .live-dot { width: 8px; height: 8px; background: var(--primary-red); border-radius: 50%; animation: blink 1.5s infinite; }
  @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <div>
      <h1>Live Floor Map</h1>
      <p>Real-time tracking of parking slot occupancy across all zones.</p>
    </div>
    <div class="live-stats">
      <div class="stat-badge"><div class="num" style="color: var(--primary-green);"><?php echo $available; ?></div><div class="lbl">Available</div></div>
      <div class="stat-badge"><div class="num" style="color: var(--text-muted);"><?php echo $occupied; ?></div><div class="lbl">Occupied</div></div>
      <div class="live-indicator"><div class="live-dot"></div> LIVE AUTO-REFRESH</div>
    </div>
  </div>

  <div class="layout-container">
    
    <!-- VIP SECTION -->
    <div class="category-section">
      <div class="category-title">VIP PLATINUM</div>
      <?php
        $row_letters = ['A', 'B', 'C'];
        $slot_counter = 1;
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- EV SECTION -->
    <div class="category-section">
      <div class="category-title">EV CHARGING ZONE</div>
      <?php
        $row_letters = ['D', 'E', 'F'];
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- SUV SECTION -->
    <div class="category-section">
      <div class="category-title">SUV / HEAVY</div>
      <?php
        $row_letters = ['G', 'H', 'I'];
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div><div class='seat-group'>";
          for($c=1; $c<=3; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=4; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=3; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- NORMAL SECTION -->
    <div class="category-section">
      <div class="category-title">NORMAL</div>
      <?php
        $row_letters = ['J', 'K', 'L', 'M', 'N', 'O'];
        for($r=0; $r<4; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_admin_seat($slot_counter); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <div class="legend-bar">
      <div class="legend-item"><div class="legend-box lb-available"></div> Available</div>
      <div class="legend-item"><div class="legend-box lb-occupied"></div> Occupied</div>
    </div>
  </div>

</main>

</body>
</html>

<?php
function render_admin_seat($id) {
  global $slots_db, $active_vehicles;
  $status = isset($slots_db[$id]) ? $slots_db[$id]['status'] : 'available'; 
  $display_num = str_pad($id, 2, '0', STR_PAD_LEFT);
  if (strlen($display_num) > 2) $display_num = substr($display_num, -2);
  
  $v_no = '';
  if($status == 'occupied' && isset($active_vehicles[$id])) {
      $v_no = "<div class='v-no'>" . htmlspecialchars($active_vehicles[$id]) . "</div>";
  }

  echo "<div class='seat $status' data-slot='$id'><div>$display_num</div>$v_no</div>";
}
?>
