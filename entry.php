<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
$slots = $conn->query("SELECT * FROM slots");

?>

<!DOCTYPE html>
<html>
<head>
  <title>Vehicle Entry</title>
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
  background: linear-gradient(45deg,#0ea5e9,#9333ea);
  transition: 0.3s;
}

button:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px #0ea5e9;
}
.slot-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap: 20px;
  margin: 30px 0;
}

.slot {
  cursor: pointer;
}

.slot input {
  display: none;
}

.slot-box {
  position: relative;
  overflow: hidden;
  animation: fadeUp 0.6s ease forwards;
  opacity: 0;
}

@keyframes fadeUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

/* Available */
.slot.available .slot-box {
  border: 1px solid #22c55e;
}

.slot.available:hover .slot-box {
  transform: translateY(-5px) scale(1.08);
  box-shadow: 
    0 0 10px #22c55e,
    0 0 20px #22c55e,
    0 0 40px #22c55e;
}

/* Occupied */
.slot.occupied .slot-box {
  border: 1px solid #ef4444;
  opacity: 0.6;
  cursor: not-allowed;
}

/* Selected */
.slot input:checked + .slot-box {
  animation: selectPop 0.3s ease;
}

@keyframes selectPop {
  0% { transform: scale(1); }
  50% { transform: scale(1.15); }
  100% { transform: scale(1.1); }
}

/* Text */
.slot-id {
  display: block;
  font-size: 16px;
  font-weight: bold;
}

.slot-status {
  font-size: 12px;
  opacity: 0.7;
}
.bg-glow {
  position: fixed;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, #0ea5e9, transparent);
  filter: blur(200px);
  top: -150px;
  left: -150px;
  z-index: -1;
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0% { transform: translate(0,0); }
  50% { transform: translate(50px,50px); }
  100% { transform: translate(0,0); }
}
@keyframes ripple {
  to {
    transform: scale(4);
    opacity: 0;
  }
}
  </style>
</head>
<script>
document.querySelectorAll('.slot-box').forEach(box => {
  box.addEventListener('click', function(e){
    let ripple = document.createElement("span");
    ripple.style.position = "absolute";
    ripple.style.width = "100px";
    ripple.style.height = "100px";
    ripple.style.background = "rgba(255,255,255,0.3)";
    ripple.style.borderRadius = "50%";
    ripple.style.transform = "scale(0)";
    ripple.style.left = e.offsetX + "px";
    ripple.style.top = e.offsetY + "px";
    ripple.style.animation = "ripple 0.6s linear";

    this.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
  });
});
</script>
<script>
function loadSlots(){
  fetch('get_slots.php')
    .then(res => res.json())
    .then(data => {
      let selected = document.querySelector('input[name="slot_id"]:checked')?.value;
      let html = "";

      data.forEach(slot => {
        let status = slot.status;
        let disabled = status === 'occupied' ? 'disabled' : '';

        html += `
        <label class="slot ${status}">
          <input type="radio" name="slot_id" value="${slot.slot_id}" 
           ${disabled} ${selected == slot.slot_id ? 'checked' : ''}>
          <div class="slot-box">
            <span class="slot-id">S${slot.slot_id}</span>
            <span class="slot-status">${status}</span>
          </div>
        </label>
        `;
      });

      document.getElementById("slotContainer").innerHTML = html;
    });
}

// load first time
loadSlots();

// auto refresh every 3 seconds
setInterval(loadSlots, 3000);
</script>

<body>
  <div class="bg-glow"></div>
  
  <div style="position:absolute; top:20px; left:20px;">
  <a href="dashboard.php" style="
    padding:10px 15px;
    border-radius:8px;
    background:#111;
    color:white;
    text-decoration:none;
  ">
    ⬅ Dashboard
  </a>
</div>



<div class="box">
<h2 style="text-align:center;">🚗 Select Parking Slot</h2>

<form method="POST">
<div style="display:flex; gap:15px; justify-content:center; margin-bottom:10px;">
  <span style="color:#22c55e;">● Available</span>
  <span style="color:#ef4444;">● Occupied</span>
</div>
<div id="slotContainer" class="slot-container">

<?php while($row = $slots->fetch_assoc()){ 
  $status = $row['status'];
  $disabled = $status == 'occupied' ? 'disabled' : '';
?>

<label class="slot <?php echo $status; ?>">
  <input type="radio" name="slot_id" value="<?php echo $row['slot_id']; ?>" <?php echo $disabled; ?>>
  
  <div class="slot-box">
    <span class="slot-id">S<?php echo $row['slot_id']; ?></span>
    <span class="slot-status"><?php echo ucfirst($status); ?></span>
  </div>
</label>

<?php } ?>

</div>

<input type="text" name="vehicle_no" placeholder="Vehicle Number" required>
<button name="add">Park Vehicle</button>

</form>

<?php
if(isset($_POST['add'])){
  $vehicle_no = $_POST['vehicle_no'];
  $slot_id = $_POST['slot_id'];

  // check if slot is still available
  $check = $conn->query("SELECT * FROM slots WHERE slot_id=$slot_id AND status='available'");

  if($check->num_rows > 0){

    // insert vehicle
    $conn->query("INSERT INTO vehicles (vehicle_no, slot_id) VALUES ('$vehicle_no', '$slot_id')");

    // update slot
    $conn->query("UPDATE slots SET status='occupied' WHERE slot_id=$slot_id");

    echo "<p style='color:lightgreen;'>Vehicle parked at Slot $slot_id</p>";

    echo "<script>
      setTimeout(() => {
        window.location.href='dashboard.php';
      }, 2000);
    </script>";

  } else {
    echo "<p style='color:red;'>Slot already occupied!</p>";
  }
}
?>

</div>


</body>
</html>