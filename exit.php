<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Vehicle Exit</title>
  <style>
    body {
      background: #000;
      color: white;
      font-family: Poppins;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .box {
      padding: 30px;
      border-radius: 15px;
      background: rgba(255,255,255,0.05);
      text-align: center;
    }

    input, button {
      padding: 10px;
      margin: 10px;
      border-radius: 8px;
      border: none;
    }

    button {
      background: linear-gradient(45deg,#ef4444,#dc2626);
      color: white;
      cursor: pointer;
    }

    a {
      display: inline-block;
      margin-top: 10px;
      color: #38bdf8;
      text-decoration: none;
    }
  </style>
</head>

<body>

<div class="box">
  <h2>🚪 Vehicle Exit</h2>

  <form method="POST">
    <input type="text" name="vehicle_no" placeholder="Vehicle Number" required>
    <br>
    <button name="exit">Exit Vehicle</button>
  </form>

  <a href="dashboard.php">⬅ Back to Dashboard</a>

<?php
if(isset($_POST['exit'])){
  $vehicle_no = $_POST['vehicle_no'];

  // find vehicle
  $result = $conn->query("SELECT * FROM vehicles WHERE vehicle_no='$vehicle_no' AND exit_time IS NULL");

  if($result->num_rows > 0){
    $data = $result->fetch_assoc();
    $slot_id = $data['slot_id'];

    // update exit time
    $conn->query("UPDATE vehicles SET exit_time=NOW() WHERE id=".$data['id']);

    // free slot
    $conn->query("UPDATE slots SET status='available' WHERE slot_id=$slot_id");

    echo "<p style='color:lightgreen;'>Vehicle exited. Slot $slot_id is now free.</p>";

    // auto redirect
    echo "<script>
      setTimeout(() => {
        window.location.href='dashboard.php';
      }, 2000);
    </script>";

  } else {
    echo "<p style='color:red;'>Vehicle not found or already exited</p>";
  }
}
?>

</div>

</body>
</html>