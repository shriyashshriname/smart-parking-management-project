<?php
session_start();
include 'db.php';
include 'log_activity.php';

$error = null;

if(isset($_POST['login'])){
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = $conn->query("SELECT * FROM users WHERE email='$email'");

  if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if($password == $user['password']){
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['name'] = $user['name'];
      $_SESSION['role'] = $user['role'];

      log_activity($conn, $user['id'], 'LOGIN', 'User logged in');

      if ($user['role'] == 'admin') {
          header("Location: dashboard.php");
      } else {
          header("Location: user_dashboard.php");
      }
      exit();
    } else {
      log_activity($conn, null, 'LOGIN_FAILED', "Failed login attempt for $email");
      $error = "Incorrect password";
    }
  } else {
    $error = "Account not found";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Valetra - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-color: #09090b;
      --text-main: #f3f3f3;
      --text-muted: #a1a1aa;
      --border-color: rgba(255, 255, 255, 0.1);
      --input-bg: rgba(255, 255, 255, 0.05);
      --primary-green: #22c55e;
      --primary-gold: #eab308;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background-color: var(--bg-color);
      color: var(--text-main);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }

    /* Animated Background Orbs */
    .bg-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      z-index: -1;
      opacity: 0.5;
    }
    .orb-1 { width: 400px; height: 400px; background: rgba(34, 197, 94, 0.3); top: -100px; left: -100px; animation: float1 10s infinite alternate; }
    .orb-2 { width: 500px; height: 500px; background: rgba(234, 179, 8, 0.2); bottom: -150px; right: -100px; animation: float2 15s infinite alternate; }
    .orb-3 { width: 300px; height: 300px; background: rgba(59, 130, 246, 0.2); top: 50%; left: 50%; transform: translate(-50%, -50%); animation: float3 12s infinite alternate; }

    @keyframes float1 { 0% { transform: translate(0, 0); } 100% { transform: translate(100px, 100px); } }
    @keyframes float2 { 0% { transform: translate(0, 0); } 100% { transform: translate(-100px, -50px); } }
    @keyframes float3 { 0% { transform: translate(-50%, -50%) scale(1); } 100% { transform: translate(-40%, -60%) scale(1.2); } }

    /* Navbar */
    .navbar {
      display: flex; justify-content: space-between; align-items: center; padding: 25px 50px; z-index: 10;
    }
    .logo {
      display: flex; align-items: center; gap: 12px; font-size: 24px; font-family: 'Instrument Serif', serif;
      color: var(--text-main); text-decoration: none; text-shadow: 0 0 10px rgba(255,255,255,0.2);
    }
    .nav-actions a {
      color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; transition: 0.3s;
    }
    .nav-actions a:hover { color: var(--text-main); }

    /* Main Content */
    .main-container {
      display: flex; flex: 1; align-items: center; justify-content: center; z-index: 10; padding: 20px;
    }

    .auth-box {
      width: 100%; max-width: 420px;
      background: rgba(24, 24, 27, 0.6);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid var(--border-color);
      border-radius: 24px;
      padding: 40px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .auth-header { text-align: center; margin-bottom: 35px; }
    .auth-header h1 { font-family: 'Instrument Serif', serif; font-size: 42px; font-weight: 400; margin-bottom: 8px; }
    .auth-header p { color: var(--text-muted); font-size: 15px; }

    .input-group { margin-bottom: 20px; position: relative; }
    .input-group input {
      width: 100%; background: var(--input-bg); border: 1px solid var(--border-color);
      color: var(--text-main); padding: 14px 18px; border-radius: 12px; font-size: 15px;
      outline: none; transition: 0.3s;
    }
    .input-group input:focus { border-color: var(--primary-gold); background: rgba(255,255,255,0.1); box-shadow: 0 0 15px rgba(234,179,8,0.2); }

    button.btn-submit {
      width: 100%; background: var(--text-main); color: #000; border: none; padding: 14px;
      border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s;
      margin-top: 10px;
    }
    button.btn-submit:hover { background: var(--primary-gold); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(234,179,8,0.3); }

    .divider {
      display: flex; align-items: center; color: var(--text-muted); font-size: 13px; margin: 25px 0;
    }
    .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border-color); }
    .divider::before { margin-right: 15px; } .divider::after { margin-left: 15px; }

    .btn-google {
      width: 100%; display: flex; align-items: center; justify-content: center; gap: 12px;
      background: transparent; border: 1px solid var(--border-color); color: var(--text-main);
      padding: 12px; border-radius: 12px; font-size: 15px; font-weight: 500; cursor: pointer; transition: 0.3s;
    }
    .btn-google:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.3); }

    .error-msg {
      margin-top: 20px; padding: 12px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 10px; color: #fca5a5; font-size: 14px; text-align: center;
    }

    .signup-link { text-align: center; margin-top: 25px; font-size: 14px; color: var(--text-muted); }
    .signup-link a { color: var(--primary-gold); text-decoration: none; font-weight: 600; transition: 0.2s; }
    .signup-link a:hover { text-shadow: 0 0 10px rgba(234,179,8,0.5); }
  </style>

<style>
  body, a, button, input, select {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%23eab308" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, auto !important;
  }
  /* Optional hover effect cursor (slightly larger or different color) */
  a:hover, button:hover {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="%2322c55e" stroke="black" stroke-width="1"><path d="M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z" transform="rotate(-45 12 12)"/></svg>') 4 4, pointer !important;
  }
</style>

</head>
<body>

  <!-- Background Orbs -->
  <div class="bg-orb orb-1"></div>
  <div class="bg-orb orb-2"></div>
  <div class="bg-orb orb-3"></div>

  <!-- Navbar -->
  <nav class="navbar">
    <a href="index.php" class="logo">
      
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
    </a>
    <div class="nav-actions">
      <a href="index.php">Back to Home</a>
    </div>
  </nav>

  <!-- Main Login Box -->
  <div class="main-container">
    <div class="auth-box">
      
      <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Enter your details to access your dashboard</p>
      </div>

      <form method="POST">
        <div class="input-group">
          <input type="email" name="email" placeholder="Email Address" required autocomplete="email">
        </div>
        <div class="input-group">
          <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
        </div>
        <button type="submit" name="login" class="btn-submit">Sign In</button>
      </form>

      <?php if($error): ?>
        <div class='error-msg'><i class="fa fa-circle-exclamation"></i> <?php echo $error; ?></div>
      <?php endif; ?>

      <div class="divider">OR</div>

      <button type="button" class="btn-google">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
      </button>

      <div class="signup-link">
        Don't have an account? <a href="register.php">Sign up now</a>
      </div>

    </div>
  </div>

</body>
</html>