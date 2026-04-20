<?php
session_start();
include 'db.php';
include 'log_activity.php';

$error = null;
$success = null;

if(isset($_POST['register'])){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Check if user already exists
  $check = $conn->query("SELECT * FROM users WHERE email='$email'");

  if($check->num_rows > 0){
    $error = "Email already in use";
  } else {
    // Note: Password should ideally be hashed with password_hash() but keeping it plain to match login.php logic
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    
    if($conn->query($sql) === TRUE){
      $new_user_id = $conn->insert_id;
      log_activity($conn, $new_user_id, 'REGISTER', "New user registered: $email");
      $success = "Account created successfully! <a href='login.php' style='color:#fff; text-decoration:underline;'>Login here</a>";
    } else {
      $error = "Error: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Valetra - Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-color: #121212;
      --text-main: #f3f3f3;
      --text-muted: #a1a1aa;
      --border-color: #27272a;
      --panel-bg: #18181b;
      --input-bg: #27272a;
      --primary-white: #ffffff;
      --primary-black: #000000;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-main);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      border-bottom: 1px solid transparent;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 20px;
      font-family: 'Instrument Serif', serif;
      font-weight: 500;
      color: var(--text-main);
      text-decoration: none;
    }

    .nav-links {
      display: flex;
      gap: 24px;
      align-items: center;
    }

    .nav-links a {
      color: var(--text-muted);
      text-decoration: none;
      font-size: 14px;
      font-weight: 400;
      transition: color 0.2s;
    }

    .nav-links a:hover {
      color: var(--text-main);
    }

    .nav-actions {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--border-color);
      color: var(--text-main);
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.2s;
    }

    .btn-outline:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .btn-solid {
      background: var(--primary-white);
      color: var(--primary-black);
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: opacity 0.2s;
    }

    .btn-solid:hover {
      opacity: 0.9;
    }

    /* Main Content */
    .main-container {
      display: flex;
      flex: 1;
      padding: 40px;
      gap: 60px;
      align-items: center;
      justify-content: center;
    }

    .left-col {
      flex: 1;
      max-width: 480px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .left-col h1 {
      font-family: 'Instrument Serif', serif;
      font-size: 56px;
      font-weight: 400;
      line-height: 1.1;
      margin-bottom: 16px;
    }

    .left-col p.subtitle {
      font-size: 16px;
      color: #d4d4d8;
      margin-bottom: 40px;
    }

    .auth-box {
      width: 100%;
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 32px;
    }

    .btn-google {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: #09090b;
      border: 1px solid var(--border-color);
      color: var(--text-main);
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      margin-bottom: 24px;
      transition: background 0.2s;
    }

    .btn-google:hover {
      background: #18181b;
    }

    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      color: var(--text-muted);
      font-size: 12px;
      margin-bottom: 24px;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid var(--border-color);
    }

    .divider:not(:empty)::before { margin-right: .5em; }
    .divider:not(:empty)::after { margin-left: .5em; }

    .input-group {
      margin-bottom: 16px;
    }

    .input-group input {
      width: 100%;
      background: var(--input-bg);
      border: 1px solid transparent;
      color: var(--text-main);
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: border 0.2s;
    }

    .input-group input:focus {
      border: 1px solid #52525b;
    }

    button.btn-submit {
      width: 100%;
      background: var(--primary-white);
      color: var(--primary-black);
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: opacity 0.2s;
    }

    button.btn-submit:hover {
      opacity: 0.9;
    }

    .error-msg {
      margin-top: 16px;
      padding: 10px;
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 8px;
      color: #fca5a5;
      font-size: 13px;
    }

    .success-msg {
      margin-top: 16px;
      padding: 10px;
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.2);
      border-radius: 8px;
      color: #86efac;
      font-size: 13px;
    }

    .btn-app {
      display: block;
      width: fit-content;
      margin: 24px auto 0;
      background: transparent;
      border: 1px solid var(--border-color);
      color: var(--text-muted);
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      text-decoration: none;
      transition: color 0.2s, border-color 0.2s;
    }
    .btn-app:hover {
      color: var(--text-main);
      border-color: #52525b;
    }

    /* Right Column Image */
    .right-col {
      flex: 1;
      max-width: 600px;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #fafafa;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 0 0 1px rgba(255,255,255,0.05);
      position: relative;
    }

    .right-col img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    @media (max-width: 900px) {
      .main-container {
        flex-direction: column;
      }
      .right-col {
        display: none;
      }
    }
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

  <!-- Navbar -->
  <nav class="navbar">
    <a href="#" class="logo">
      
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
    <div class="nav-links">
      <a href="#">About</a>
      <a href="#">Platform</a>
      <a href="#">Solutions</a>
      <a href="subscriptions.php">Pricing</a>
      <a href="#">Contact</a>
    </div>
    <div class="nav-actions">
      <a href="login.php" class="btn-outline">Login</a>
      <a href="register.php" class="btn-solid">Sign Up</a>
    </div>
  </nav>

  <!-- Main Split Layout -->
  <div class="main-container">
    
    <!-- Left Column: Auth -->
    <div class="left-col">
      <h1>Create your<br>account</h1>
      <p class="subtitle">Join thousands managing their parking with AI</p>

      <div class="auth-box">
        <button type="button" class="btn-google">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Sign up with Google
        </button>

        <div class="divider">OR</div>

        <form method="POST">
          <div class="input-group">
            <input type="text" name="name" placeholder="Enter your full name" required>
          </div>
          <div class="input-group">
            <input type="email" name="email" placeholder="Enter your email" required>
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="Create a password" required>
          </div>
          <button type="submit" name="register" class="btn-submit">Sign Up with email</button>
        </form>

        <?php if($error): ?>
          <div class='error-msg'><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
          <div class='success-msg'><?php echo $success; ?></div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Column: Showcase -->
    <div class="right-col">
      <img src="showcase.png" alt="Valetra App Showcase">
    </div>

  </div>

</body>
</html>
