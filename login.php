<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Smart Parking Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

  <style>
    /* same styles as before (shortened for clarity) */
    body {
      background: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: Poppins;
    }

    .login-box {
      width: 360px;
      padding: 40px;
      border-radius: 20px;
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(15px);
      color: white;
      text-align: center;
    }

    input, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 10px;
      border: none;
    }

    button {
      background: linear-gradient(45deg,#0ea5e9,#9333ea);
      color: white;
      cursor: pointer;
    }

    .error {
      color: red;
    }
  </style>
</head>

<body>

<div class="login-box">
  <h2>Smart Parking 🚗</h2>

  <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="login">Login</button>
  </form>

  <div class="link">
    <a href="register.php">Create Account</a>
  </div>

  <?php
  if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($result->num_rows > 0){
      $user = $result->fetch_assoc();

      if($password == $user['password']){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        header("Location: dashboard.php");
      } else {
        echo "<p class='error'>Wrong Password</p>";
      }
    } else {
      echo "<p class='error'>User not found</p>";
    }
  }
  ?>

</div>

</body>
</html>