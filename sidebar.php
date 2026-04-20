
<style>
  body, a, button, input, select {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%23eab308" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, auto !important;
  }
  /* Optional hover effect cursor (slightly larger or different color) */
  a:hover, button:hover {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%2322c55e" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, pointer !important;
  }
</style>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? 'user';
$wallet = 0;
if(isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $u_data = $conn->query("SELECT name, email, wallet_balance, plan FROM users WHERE id=$uid")->fetch_assoc();
    $wallet = $u_data['wallet_balance'];
}
?>

<style>
  /* Advanced Sidebar Styling */
  .sidebar {
    width: var(--sidebar-width); background: rgba(24, 24, 27, 0.95); backdrop-filter: blur(20px);
    border-right: 1px solid var(--border-color); position: fixed; height: 100vh;
    display: flex; flex-direction: column; padding: 24px 20px; z-index: 1000;
  }
  .brand {
    display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif;
    font-size: 26px; margin-bottom: 30px; padding: 0 10px; color: var(--text-main);
  }
  .brand svg { filter: drop-shadow(0 0 8px rgba(234,179,8,0.5)); }
  
  .nav-group { margin-bottom: 25px; }
  .nav-label { font-size: 11px; font-weight: 600; color: #52525b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding: 0 10px; }
  
  .nav-menu { list-style: none; margin: 0; padding: 0; }
  .nav-menu a {
    display: flex; align-items: center; gap: 14px; color: var(--text-muted); text-decoration: none;
    padding: 12px 14px; border-radius: 12px; margin-bottom: 4px; font-size: 14px; font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;
  }
  .nav-menu a i { font-size: 16px; width: 20px; text-align: center; opacity: 0.8; transition: 0.3s; }
  
  .nav-menu a:hover { color: var(--text-main); background: rgba(255,255,255,0.03); transform: translateX(4px); }
  .nav-menu a:hover i { opacity: 1; transform: scale(1.1); }
  
  .nav-menu a.active { background: rgba(255,255,255,0.06); color: var(--text-main); box-shadow: inset 3px 0 0 var(--primary-green); }
  .nav-menu a.active i { color: var(--primary-green); opacity: 1; }

  .user-card {
    margin-top: auto; padding: 15px; border-radius: 14px; background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px;
    transition: 0.3s;
  }
  .user-card:hover { border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); }
  .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: #27272a; display: flex; align-items: center; justify-content: center; color: var(--primary-gold); font-weight: bold; }
  .user-info { flex: 1; overflow: hidden; }
  .user-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main); }
  .user-plan { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;}
  .user-plan i { color: var(--primary-gold); font-size: 10px; }
  
  .logout-btn { color: var(--text-muted); cursor: pointer; padding: 8px; transition: 0.3s; }
  .logout-btn:hover { color: var(--primary-red); transform: scale(1.1); }
</style>

<aside class="sidebar">
  <div class="brand">
    
<svg width="32" height="32" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="valetraGold" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fef08a"/>
      <stop offset="40%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#713f12"/>
    </linearGradient>
  </defs>
  <path d="M50 90 L25 30 C35 30 45 50 50 70 C55 50 65 30 75 30 Z" fill="url(#valetraGold)"/>
  <path d="M15 35 C30 40 35 55 40 65 C30 55 20 45 10 35 Z" fill="url(#valetraGold)"/>
  <path d="M85 35 C70 40 65 55 60 65 C70 55 80 45 90 35 Z" fill="url(#valetraGold)"/>
</svg>

    Valetra
  </div>
  
  <div style="flex: 1; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
    
    <?php if($user_role == 'admin'): ?>
      <div class="nav-group">
        <div class="nav-label">Main</div>
        <ul class="nav-menu">
          <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fa fa-border-all"></i> Overview</a></li>
        </ul>
      </div>

      <div class="nav-group">
        <div class="nav-label">Management</div>
        <ul class="nav-menu">
          <li><a href="book.php" class="<?php echo $current_page == 'book.php' ? 'active' : ''; ?>"><i class="fa fa-ticket-alt"></i> Manage Slots</a></li>
          <li><a href="admin_livemap.php" class="<?php echo $current_page == 'admin_livemap.php' ? 'active' : ''; ?>"><i class="fa fa-map-location-dot"></i> Live Map</a></li>
          <li><a href="admin_vehicles.php" class="<?php echo $current_page == 'admin_vehicles.php' ? 'active' : ''; ?>"><i class="fa fa-car-side"></i> Vehicles & Activity</a></li>
          <li><a href="admin_subscriptions.php" class="<?php echo $current_page == 'admin_subscriptions.php' ? 'active' : ''; ?>"><i class="fa fa-star"></i> Subscriptions</a></li>
          <li><a href="admin_store.php" class="<?php echo $current_page == 'admin_store.php' ? 'active' : ''; ?>"><i class="fa fa-box"></i> Store Orders</a></li>
        </ul>
      </div>

    <?php else: ?>
      <div class="nav-group">
        <div class="nav-label">Home</div>
        <ul class="nav-menu">
          <li><a href="user_dashboard.php" class="<?php echo $current_page == 'user_dashboard.php' ? 'active' : ''; ?>"><i class="fa fa-home"></i> My Dashboard</a></li>
          <li><a href="history.php" class="<?php echo $current_page == 'history.php' ? 'active' : ''; ?>"><i class="fa fa-clock-rotate-left"></i> History Log</a></li>
        </ul>
      </div>

      <div class="nav-group">
        <div class="nav-label">Services</div>
        <ul class="nav-menu">
          <li><a href="book.php" class="<?php echo $current_page == 'book.php' ? 'active' : ''; ?>"><i class="fa fa-ticket-alt"></i> Book a Slot</a></li>
          <li><a href="user_map.php" class="<?php echo $current_page == 'user_map.php' ? 'active' : ''; ?>"><i class="fa fa-map-location-dot"></i> Find Parking</a></li>
          <li><a href="store.php" class="<?php echo in_array($current_page, ['store.php', 'checkout.php']) ? 'active' : ''; ?>"><i class="fa fa-shopping-cart"></i> Car Gear Store</a></li>
        </ul>
      </div>

    <?php endif; ?>

    <div class="nav-group">
      <div class="nav-label">Support & Account</div>
      <ul class="nav-menu">
        <li><a href="ai_assistant.php" class="<?php echo $current_page == 'ai_assistant.php' ? 'active' : ''; ?>"><i class="fa fa-robot"></i> AI Assistant</a></li>
        <?php if($user_role != 'admin'): ?>
          <li><a href="subscriptions.php" class="<?php echo $current_page == 'subscriptions.php' ? 'active' : ''; ?>"><i class="fa fa-star"></i> Upgrade Plan</a></li>
        <?php endif; ?>
        <li><a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>"><i class="fa fa-user"></i> Profile Settings</a></li>
      </ul>
    </div>
  </div>

  <?php if(isset($u_data)): ?>
  <div class="user-card">
    <div class="user-avatar"><?php echo strtoupper(substr($u_data['name'], 0, 1)); ?></div>
    <div class="user-info">
      <div class="user-name"><?php echo htmlspecialchars($u_data['name']); ?></div>
      <div class="user-plan">
        <i class="fa fa-star"></i> <?php echo ucfirst($u_data['plan']); ?> Plan
      </div>
    </div>
    <a href="logout.php" class="logout-btn" title="Logout"><i class="fa fa-arrow-right-from-bracket"></i></a>
  </div>
  <?php endif; ?>
</aside>
<?php include 'chat_widget.php'; ?>

