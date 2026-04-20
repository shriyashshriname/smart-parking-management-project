<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
  header("Location: login.php");
  exit();
}
include 'db.php';

// Aggregate subscription stats
$stats = $conn->query("
  SELECT 
    plan,
    COUNT(*) as total_users
  FROM users
  GROUP BY plan
")->fetch_all(MYSQLI_ASSOC);

$plan_map = [
  'free'    => ['name'=>'Free',     'color'=>'#71717a',  'price'=>0    ],
  'basic'   => ['name'=>'Basic',    'color'=>'#a1a1aa',  'price'=>499  ],
  'premium' => ['name'=>'Premium',  'color'=>'#eab308',  'price'=>999  ],
  'ultimate'=> ['name'=>'Ultimate', 'color'=>'#a855f7',  'price'=>1999 ],
  'b2b'     => ['name'=>'B2B',      'color'=>'#3b82f6',  'price'=>9999 ],
];

$total_revenue = 0;
$stats_by_plan = [];
foreach($stats as $row) {
  $p = $row['plan'] ?? 'free';
  $price = $plan_map[$p]['price'] ?? 0;
  $total_revenue += $price * $row['total_users'];
  $stats_by_plan[$p] = $row['total_users'];
}

// Recent subscribers
$recent = $conn->query("SELECT name, email, plan, wallet_balance FROM users WHERE plan != 'free' ORDER BY id DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Smart Parking - Admin Subscriptions</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-gold: #eab308; --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; }

  .sidebar {
    width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color);
    position: fixed; height: 100vh; display: flex; flex-direction: column; padding: 24px;
  }
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

  /* Revenue Banner */
  .revenue-banner {
    background: linear-gradient(135deg, rgba(34,197,94,0.1) 0%, #09090b 70%), var(--panel-bg);
    border: 1px solid rgba(34,197,94,0.2); border-radius: 20px; padding: 30px 40px; margin-bottom: 30px;
    display: flex; justify-content: space-between; align-items: center;
  }
  .revenue-banner h3 { color: var(--text-muted); font-size: 14px; font-weight: 500; margin-bottom: 5px; }
  .revenue-banner .amount { font-family: 'Instrument Serif', serif; font-size: 52px; color: var(--primary-green); }
  .revenue-banner .label { color: var(--text-muted); }

  /* Plan Cards */
  .plan-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 30px; }
  .plan-card {
    background: var(--panel-bg); border: 1px solid var(--border-color);
    border-radius: 14px; padding: 20px; text-align: center; transition: 0.2s;
  }
  .plan-card:hover { transform: translateY(-3px); }
  .plan-count { font-family: 'Instrument Serif', serif; font-size: 40px; margin-bottom: 5px; }
  .plan-name { font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
  .plan-revenue { font-size: 12px; color: var(--text-muted); }

  /* Chart */
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; }
  .panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; }
  .panel h3 { font-size: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

  /* Table */
  table { width: 100%; border-collapse: collapse; }
  th { text-align: left; padding: 12px; font-size: 13px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
  td { padding: 12px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.03); }
  tr:hover td { background: rgba(255,255,255,0.02); }
  .badge { font-size: 11px; padding: 3px 8px; border-radius: 5px; font-weight: 600; text-transform: uppercase; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <h1>Subscription Analytics</h1>
  <p class="subtitle">Overview of all user subscription plans and revenue metrics.</p>

  <!-- Revenue Banner -->
  <div class="revenue-banner">
    <div>
      <h3>Total Projected Monthly Revenue</h3>
      <div class="amount">₹<?php echo number_format($total_revenue); ?></div>
    </div>
    <div style="text-align: right;">
      <div class="label">Total Subscribed Users</div>
      <div style="font-size: 32px; font-weight: 600;"><?php echo array_sum(array_column($stats, 'total_users')); ?></div>
    </div>
  </div>

  <!-- Plan Count Cards -->
  <div class="plan-grid">
    <?php foreach($plan_map as $key => $p): 
      $count = $stats_by_plan[$key] ?? 0;
      $rev = $p['price'] * $count;
    ?>
    <div class="plan-card" style="border-color: <?php echo $p['color']; ?>20;">
      <div class="plan-count" style="color: <?php echo $p['color']; ?>"><?php echo $count; ?></div>
      <div class="plan-name"><?php echo $p['name']; ?></div>
      <div class="plan-revenue">₹<?php echo number_format($rev); ?>/mo</div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Chart + Table -->
  <div class="grid-2">
    <div class="panel">
      <h3><i class="fa fa-chart-pie"></i> Plan Distribution</h3>
      <canvas id="planChart"></canvas>
    </div>
    <div class="panel">
      <h3><i class="fa fa-users"></i> Recent Subscribers</h3>
      <table>
        <tr><th>Name</th><th>Plan</th><th>Wallet</th></tr>
        <?php while($row = $recent->fetch_assoc()):
          $color = $plan_map[$row['plan']]['color'] ?? '#a1a1aa';
        ?>
        <tr>
          <td><?php echo htmlspecialchars($row['name']); ?><br><small style="color:var(--text-muted);"><?php echo htmlspecialchars($row['email']); ?></small></td>
          <td><span class="badge" style="background: <?php echo $color; ?>20; color: <?php echo $color; ?>;"><?php echo ucfirst($row['plan']); ?></span></td>
          <td style="color: var(--primary-gold);"><?php echo number_format($row['wallet_balance'], 2); ?> ⓖ</td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</main>

<script>
Chart.defaults.color = '#a1a1aa';
Chart.defaults.font.family = 'Inter';

new Chart(document.getElementById('planChart'), {
  type: 'doughnut',
  data: {
    labels: ['Free', 'Basic', 'Premium', 'Ultimate', 'B2B'],
    datasets: [{
      data: [
        <?php echo $stats_by_plan['free'] ?? 0; ?>,
        <?php echo $stats_by_plan['basic'] ?? 0; ?>,
        <?php echo $stats_by_plan['premium'] ?? 0; ?>,
        <?php echo $stats_by_plan['ultimate'] ?? 0; ?>,
        <?php echo $stats_by_plan['b2b'] ?? 0; ?>
      ],
      backgroundColor: ['#27272a', '#a1a1aa', '#eab308', '#a855f7', '#3b82f6'],
      borderColor: '#09090b',
      borderWidth: 3
    }]
  },
  options: {
    plugins: { legend: { position: 'bottom' } },
    cutout: '65%'
  }
});
</script>
</body>
</html>
