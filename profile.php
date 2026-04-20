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

// Handle Profile Update
if(isset($_POST['update_profile'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $address = $conn->real_escape_string($_POST['address']);
    
    $vehicle_no = strtoupper(trim($conn->real_escape_string($_POST['vehicle_no'])));
    $car_model = $conn->real_escape_string($_POST['car_model']);
    
    // File Uploads
    $profile_image_query = "";
    $car_image_query = "";
    
    $target_dir = "uploads/";
    
    if(isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0){
        $profile_file = $target_dir . "user_" . $user_id . "_" . basename($_FILES["profile_image"]["name"]);
        if(move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_file)) {
            $profile_image_query = ", profile_image='$profile_file'";
        }
    }
    
    if(isset($_FILES["car_image"]) && $_FILES["car_image"]["error"] == 0){
        $car_file = $target_dir . "car_" . $user_id . "_" . basename($_FILES["car_image"]["name"]);
        if(move_uploaded_file($_FILES["car_image"]["tmp_name"], $car_file)) {
            $car_image_query = ", car_image='$car_file'";
        }
    }

    $query = "UPDATE users SET 
              name='$name', email='$email', contact_no='$contact_no', 
              dob='$dob', address='$address', vehicle_no='$vehicle_no', 
              car_model='$car_model'
              $profile_image_query
              $car_image_query
              WHERE id=$user_id";
              
    if($conn->query($query)){
        $_SESSION['name'] = $name;
        log_activity($conn, $user_id, 'UPDATE_PROFILE', "Profile updated");
        $message = "<div class='success-alert'>Profile updated successfully!</div>";
    } else {
        $message = "<div class='error-alert'>Error updating profile.</div>";
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$avatar_url = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name='.urlencode($user['name']).'&background=27272a&color=fff';
$car_url = !empty($user['car_image']) ? $user['car_image'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Profile</title>
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
  h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; margin-bottom: 10px; }
  p.subtitle { color: var(--text-muted); margin-bottom: 30px; }

  .success-alert { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
  .error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 20px; }

  .grid-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
  }

  .profile-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; margin-bottom: 30px;}
  .profile-card h3 { font-size: 18px; margin-bottom: 20px; color: var(--primary-green); border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
  
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .form-group { margin-bottom: 20px; }
  .form-group.full { grid-column: 1 / -1; }
  .form-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 13px; font-weight: 500;}
  .form-group input, .form-group textarea {
    width: 100%; padding: 12px 16px; background: var(--bg-color); border: 1px solid var(--border-color);
    border-radius: 8px; color: var(--text-main); font-size: 14px; outline: none; transition: border 0.2s;
  }
  .form-group textarea { resize: vertical; min-height: 80px; }
  .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-green); }
  .form-group input[type="file"] { padding: 8px; font-size: 13px; color: var(--text-muted); }

  .btn-save {
    background: var(--primary-green); color: var(--bg-color); border: none; padding: 12px 24px;
    border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s;
    width: 100%; margin-top: 10px;
  }
  .btn-save:hover { box-shadow: 0 4px 15px rgba(34,197,94,0.3); transform: translateY(-1px); }

  /* Wallet Card */
  .wallet-card {
    background: linear-gradient(135deg, #18181b 0%, #09090b 100%);
    border: 1px solid var(--primary-gold);
    border-radius: 16px; padding: 24px; text-align: center;
    box-shadow: 0 10px 30px rgba(234, 179, 8, 0.1);
    margin-bottom: 20px;
  }
  .wallet-card i { font-size: 32px; color: var(--primary-gold); margin-bottom: 10px; }
  .wallet-balance { font-family: 'Instrument Serif', serif; font-size: 42px; color: var(--primary-gold); margin-bottom: 4px; }
  .wallet-label { color: var(--text-muted); font-size: 13px; margin-bottom: 15px;}
  .cashback-badge { background: rgba(34,197,94,0.1); color: var(--primary-green); padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; border: 1px solid rgba(34,197,94,0.3);}

  /* Car Visual */
  .car-visual {
    border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);
    position: relative; margin-bottom: 20px; height: 220px;
  }
  .car-visual img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .car-visual-overlay {
    position: absolute; bottom: 0; left: 0; right: 0; padding: 20px;
    background: linear-gradient(to top, rgba(9,9,11,0.9), transparent);
  }
  .car-visual-overlay h4 { font-family: 'Instrument Serif', serif; font-size: 20px; }
  .car-visual-overlay p { font-size: 13px; color: var(--text-muted); }

  /* Plan Status Card */
  .plan-status-card {
    border-radius: 16px; padding: 20px; margin-bottom: 20px;
    border: 1px solid;
  }
  .plan-status-card h4 { font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
  .plan-status-card .plan-name-big { font-family: 'Instrument Serif', serif; font-size: 26px; margin-bottom: 12px; }
  .plan-status-card a { display: block; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; }
  .plan-status-card a:hover { opacity: 0.85; }

  /* Pay Methods */
  .pay-methods { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; }
  .pay-methods h4 { font-size: 14px; font-weight: 600; margin-bottom: 15px; color: var(--text-muted); }
  .pay-method-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
  .pay-method-item:last-child { border-bottom: none; }
  .pay-method-item i { font-size: 20px; width: 24px; text-align: center; }
  .pay-method-item span { font-size: 14px; font-weight: 500; }
  .pay-method-item small { margin-left: auto; font-size: 12px; color: var(--text-muted); }

  /* Image Previews */
  .avatar-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); margin-bottom: 15px; display: block;}
  .car-preview { width: 100%; height: 150px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color); margin-bottom: 15px; display: block; background: #27272a;}
  
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <h1>Profile Settings</h1>
  <p class="subtitle">Manage your personal details, vehicle information, and wallet.</p>
  
  <?php echo $message; ?>

  <div class="grid-container">
    
    <!-- Left Column: Forms -->
    <div class="forms-col">
      <form method="POST" enctype="multipart/form-data">
        
        <!-- Personal Info -->
        <div class="profile-card">
          <h3><i class="fa fa-address-card"></i> Personal Information</h3>
          
          <img src="<?php echo $avatar_url; ?>" alt="Avatar" class="avatar-preview">
          
          <div class="form-group full">
            <label>Profile Picture</label>
            <input type="file" name="profile_image" accept="image/*">
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no'] ?? ''); ?>" placeholder="+91 9876543210">
            </div>
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
            </div>
            <div class="form-group full">
              <label>Address</label>
              <textarea name="address" placeholder="Enter your full address..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Vehicle Info -->
        <div class="profile-card">
          <h3><i class="fa fa-car"></i> Vehicle Information</h3>
          
          <?php if($car_url): ?>
            <img src="<?php echo $car_url; ?>" alt="Car" class="car-preview">
          <?php endif; ?>

          <div class="form-group full">
            <label>Car Image Upload</label>
            <input type="file" name="car_image" accept="image/*">
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Default License Plate</label>
              <input type="text" name="vehicle_no" style="text-transform: uppercase;" value="<?php echo htmlspecialchars($user['vehicle_no'] ?? ''); ?>" placeholder="MH12AB1234">
            </div>
            <div class="form-group">
              <label>Car Model & Make</label>
              <input type="text" name="car_model" value="<?php echo htmlspecialchars($user['car_model'] ?? ''); ?>" placeholder="e.g. Tesla Model 3">
            </div>
          </div>
        </div>

        <button type="submit" name="update_profile" class="btn-save"><i class="fa fa-save"></i> Save All Changes</button>
      </form>
    </div>

    <!-- Right Column: Wallet & Visuals -->
    <div class="stats-col">

      <!-- Decorative Car Visual -->
      <div class="car-visual">
        <img src="profile_car.png" alt="Premium Parking">
        <div class="car-visual-overlay">
          <h4>Smart Parking</h4>
          <p>Your vehicle, your space — secured.</p>
        </div>
      </div>

      <!-- Current Plan Status -->
      <?php
        $plan_colors = ['free'=>'#71717a','basic'=>'#a1a1aa','premium'=>'#eab308','ultimate'=>'#a855f7','b2b'=>'#3b82f6'];
        $cur_plan = $user['plan'] ?? 'free';
        $pc = $plan_colors[$cur_plan] ?? '#fefeffff';
      ?>
      <div class="plan-status-card" style="border-color: <?php echo $pc; ?>30; background: <?php echo $pc; ?>08;">
        <h4 style="color: <?php echo $pc; ?>;">Current Plan</h4>
        <div class="plan-name-big" style="color: <?php echo $pc; ?>;"><?php echo ucfirst($cur_plan); ?></div>
        <?php if($cur_plan === 'free' || $cur_plan === 'basic'): ?>
        <a href="subscriptions.php" style="background: <?php echo $pc; ?>20; color: <?php echo $pc; ?>; border: 1px solid <?php echo $pc; ?>40;">
          <i class="fa fa-arrow-up"></i> Upgrade Plan
        </a>
        <?php else: ?>
        <p style="font-size:13px; color: var(--text-muted); text-align:center;">You're on our best plan!</p>
        <?php endif; ?>
      </div>

      <!-- Wallet Card -->
      <div class="wallet-card">
        <i class="fa-brands fa-gg-circle"></i>
        <div class="wallet-balance"><?php echo number_format($user['wallet_balance'], 2); ?></div>
        <div class="wallet-label">Smart Coins Balance</div>
        <div class="cashback-badge"><i class="fa fa-coins"></i> 3% Cashback on UPI/Card</div>
      </div>

      <!-- Payment Methods -->
      <div class="pay-methods">
        <h4>Accepted Payment Methods</h4>
        <div class="pay-method-item">
          <i class="fa fa-mobile-alt" style="color: #3b82f6;"></i>
          <span>UPI</span>
          <small>+3% cashback</small>
        </div>
        <div class="pay-method-item">
          <i class="fa fa-credit-card" style="color: #eab308;"></i>
          <span>Credit / Debit Card</span>
          <small>+3% cashback</small>
        </div>
        <div class="pay-method-item">
          <i class="fa-brands fa-gg-circle" style="color: #eab308;"></i>
          <span>Smart Coins Wallet</span>
          <small>instant</small>
        </div>
      </div>

    </div>

  </div>

</main>

</body>
</html>
