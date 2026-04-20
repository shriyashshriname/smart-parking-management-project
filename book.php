<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$user_id = $_SESSION['user_id'];
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$user_details = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$default_vehicle = $user_details['vehicle_no'] ?? '';

$booking_message = "";

// Process Booking
if(isset($_POST['book_slots'])) {
  $selected_slots = explode(',', $_POST['selected_slots']);
  $vehicle_no = strtoupper(trim($_POST['vehicle_no']));
  
  if(!empty($selected_slots) && !empty($vehicle_no)) {
    $success_count = 0;
    foreach($selected_slots as $slot_id) {
      if(is_numeric($slot_id)) {
        // Update slot status
        $conn->query("UPDATE slots SET status='occupied' WHERE slot_id=$slot_id");
        // Insert into vehicles table with user_id
        $conn->query("INSERT INTO vehicles (vehicle_no, slot_id, user_id, entry_time) VALUES ('$vehicle_no', $slot_id, $user_id, NOW())");
        $success_count++;
      }
    }
    if($success_count > 0) {
      log_activity($conn, $user_id, 'BOOK_SLOT', "Booked $success_count slot(s) for vehicle $vehicle_no");
      $booking_message = "<div class='success-alert'>Successfully booked $success_count slot(s) for vehicle $vehicle_no.</div>";
    }
  } else {
    $booking_message = "<div class='error-alert'>Please select slots and enter a vehicle number.</div>";
  }
}

// Fetch all slots
$slots_result = $conn->query("SELECT * FROM slots ORDER BY slot_id ASC");
$slots_db = [];
while($row = $slots_result->fetch_assoc()) {
  $slots_db[$row['slot_id']] = $row['status'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - Book Slots</title>

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
    --seat-size: 32px;
    --seat-gap: 8px;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-main);
    display: flex;
    min-height: 100vh;
  }

  /* Sidebar */
  .sidebar {
    width: var(--sidebar-width);
    background: var(--panel-bg);
    border-right: 1px solid var(--border-color);
    position: fixed;
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 24px;
    z-index: 100;
  }

  .brand {
    display: flex; align-items: center; gap: 12px;
    font-family: 'Instrument Serif', serif; font-size: 24px;
    color: var(--text-main); margin-bottom: 40px;
  }

  .nav-menu { list-style: none; flex: 1; }
  .nav-menu li { margin-bottom: 8px; }
  .nav-menu a {
    display: flex; align-items: center; gap: 12px;
    color: var(--text-muted); text-decoration: none;
    padding: 12px 16px; border-radius: 8px; font-size: 15px; font-weight: 500;
    transition: all 0.2s;
  }
  .nav-menu a:hover, .nav-menu a.active {
    background: rgba(255, 255, 255, 0.05); color: var(--text-main);
  }
  .nav-menu a.active i { color: var(--primary-green); }

  .logout-btn {
    margin-top: auto; display: flex; align-items: center; gap: 12px;
    color: var(--text-muted); text-decoration: none;
    padding: 12px 16px; border-radius: 8px; font-size: 15px;
    transition: all 0.2s;
  }
  .logout-btn:hover { background: rgba(239, 68, 68, 0.1); color: var(--primary-red); }

  /* Main Content */
  .content {
    flex: 1; margin-left: var(--sidebar-width); padding: 32px 48px 120px 48px;
    max-width: 1400px;
  }

  .page-header { margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;}
  .page-header h1 { font-family: 'Instrument Serif', serif; font-size: 36px; font-weight: 400; }
  .page-header p { color: var(--text-muted); margin-top: 5px; }

  /* Alerts */
  .success-alert { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
  .error-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 20px; }

  /* Seat Layout */
  .layout-container {
    background: var(--panel-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 40px;
    overflow-x: auto;
  }

  .category-section { margin-bottom: 50px; }
  .category-title {
    text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px;
    display: flex; align-items: center; justify-content: center; gap: 15px;
  }
  .category-title::before, .category-title::after {
    content: ''; height: 1px; width: 100px; background: var(--border-color);
  }

  .seat-row {
    display: flex; align-items: center; justify-content: center; margin-bottom: var(--seat-gap); gap: var(--seat-gap);
  }

  .row-label { width: 30px; text-align: right; font-size: 13px; color: var(--text-muted); font-weight: 600; padding-right: 15px;}
  
  .seat-group { display: flex; gap: var(--seat-gap); }
  .seat-gap-large { width: 40px; } /* Aisle */

  .seat {
    width: var(--seat-size); height: var(--seat-size);
    border-radius: 6px; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    user-select: none;
  }

  /* Seat States */
  .seat.available { border: 1px solid var(--primary-green); color: var(--primary-green); background: transparent; }
  .seat.available:hover { background: rgba(34, 197, 94, 0.1); }
  
  .seat.selected { background: var(--primary-green); color: var(--bg-color); border: 1px solid var(--primary-green); box-shadow: 0 0 10px rgba(34,197,94,0.4);}
  
  .seat.occupied { background: #27272a; color: #52525b; border: 1px solid #27272a; cursor: not-allowed; }

  /* Legend */
  .legend-bar {
    display: flex; justify-content: center; gap: 30px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);
  }
  .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); }
  .legend-box { width: 16px; height: 16px; border-radius: 4px; }
  .lb-available { border: 1px solid var(--primary-green); }
  .lb-selected { background: var(--primary-green); }
  .lb-occupied { background: #27272a; }

  /* Bottom Booking Bar */
  .bottom-bar {
    position: fixed; bottom: 0; left: var(--sidebar-width); right: 0;
    background: rgba(24, 24, 27, 0.95); backdrop-filter: blur(10px);
    border-top: 1px solid var(--border-color);
    padding: 20px 48px; display: flex; align-items: center; justify-content: space-between;
    transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 50; box-shadow: 0 -10px 30px rgba(0,0,0,0.5);
  }

  .bottom-bar.active { transform: translateY(0); }

  .booking-info { display: flex; flex-direction: column; gap: 5px; }
  .selected-slots-text { font-size: 14px; color: var(--text-muted); }
  .selected-slots-text span { color: var(--text-main); font-weight: 600; }
  .total-price { font-size: 24px; font-weight: 600; font-family: 'Instrument Serif', serif; color: var(--primary-green); }

  .booking-form { display: flex; align-items: center; gap: 20px; }
  .vehicle-input {
    background: var(--bg-color); border: 1px solid var(--border-color);
    color: var(--text-main); padding: 12px 16px; border-radius: 8px; font-size: 14px; outline: none; width: 200px;
    text-transform: uppercase;
  }
  .vehicle-input:focus { border-color: var(--primary-green); }
  
  .btn-book {
    background: var(--primary-green); color: var(--bg-color); border: none;
    padding: 12px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s;
  }
  .btn-book:hover { background: #1da851; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(34,197,94,0.3); }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="content">
  <div class="page-header">
    <h1>Slot Booking</h1>
    <p>Select your preferred parking slots across different categories.</p>
  </div>

  <?php echo $booking_message; ?>

  <div class="layout-container">
    
    <!-- VIP SECTION -->
    <div class="category-section">
      <div class="category-title">₹250 VIP PLATINUM</div>
      <?php
        // VIP slots 1-30. 3 rows of 10.
        $row_letters = ['A', 'B', 'C'];
        $slot_counter = 1;
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div>";
          echo "<div class='seat-group'>";
          for($c=1; $c<=5; $c++) {
             render_seat($slot_counter, 250); $slot_counter++;
          }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) {
             render_seat($slot_counter, 250); $slot_counter++;
          }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- EV SECTION -->
    <div class="category-section">
      <div class="category-title">₹230 EV CHARGING ZONE</div>
      <?php
        // EV slots 31-60. 3 rows of 10.
        $row_letters = ['D', 'E', 'F'];
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div>";
          echo "<div class='seat-group'>";
          for($c=1; $c<=5; $c++) {
             render_seat($slot_counter, 230); $slot_counter++;
          }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) {
             render_seat($slot_counter, 230); $slot_counter++;
          }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- SUV SECTION -->
    <div class="category-section">
      <div class="category-title">₹200 SUV / HEAVY</div>
      <?php
        // SUV slots 61-90. 3 rows of 10.
        $row_letters = ['G', 'H', 'I'];
        for($r=0; $r<3; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div>";
          echo "<div class='seat-group'>";
          for($c=1; $c<=3; $c++) { render_seat($slot_counter, 200); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=4; $c++) { render_seat($slot_counter, 200); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=3; $c++) { render_seat($slot_counter, 200); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <!-- NORMAL SECTION -->
    <div class="category-section">
      <div class="category-title">₹150 NORMAL</div>
      <?php
        // Normal slots 91-150. 4 rows of 15.
        $row_letters = ['J', 'K', 'L', 'M', 'N', 'O'];
        for($r=0; $r<4; $r++) {
          echo "<div class='seat-row'><div class='row-label'>{$row_letters[$r]}</div>";
          echo "<div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_seat($slot_counter, 150); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_seat($slot_counter, 150); $slot_counter++; }
          echo "</div><div class='seat-gap-large'></div><div class='seat-group'>";
          for($c=1; $c<=5; $c++) { render_seat($slot_counter, 150); $slot_counter++; }
          echo "</div></div>";
        }
      ?>
    </div>

    <div class="legend-bar">
      <div class="legend-item"><div class="legend-box lb-available"></div> Available</div>
      <div class="legend-item"><div class="legend-box lb-selected"></div> Selected</div>
      <div class="legend-item"><div class="legend-box lb-occupied"></div> Occupied</div>
    </div>
  </div>

</main>

<div class="bottom-bar" id="bottomBar">
  <div class="booking-info">
    <div class="selected-slots-text">Selected Slots: <span id="selectedSlotsDisplay">-</span></div>
    <div class="total-price" id="totalPriceDisplay">₹0</div>
  </div>
  
  <form method="POST" class="booking-form">
    <input type="hidden" name="selected_slots" id="selectedSlotsInput">
    <input type="text" name="vehicle_no" class="vehicle-input" value="<?php echo htmlspecialchars($default_vehicle); ?>" placeholder="License Plate (e.g. MH12AB1234)" required>
    <button type="submit" name="book_slots" class="btn-book">Book Tickets</button>
  </form>
</div>

<script>
  const seats = document.querySelectorAll('.seat.available');
  const bottomBar = document.getElementById('bottomBar');
  const selectedSlotsDisplay = document.getElementById('selectedSlotsDisplay');
  const totalPriceDisplay = document.getElementById('totalPriceDisplay');
  const selectedSlotsInput = document.getElementById('selectedSlotsInput');

  let selectedSlots = [];
  let totalPrice = 0;

  seats.forEach(seat => {
    seat.addEventListener('click', () => {
      const slotId = seat.getAttribute('data-slot');
      const price = parseInt(seat.getAttribute('data-price'));

      if(seat.classList.contains('selected')) {
        // Deselect
        seat.classList.remove('selected');
        selectedSlots = selectedSlots.filter(id => id !== slotId);
        totalPrice -= price;
      } else {
        // Select
        seat.classList.add('selected');
        selectedSlots.push(slotId);
        totalPrice += price;
      }

      updateBottomBar();
    });
  });

  function updateBottomBar() {
    if(selectedSlots.length > 0) {
      bottomBar.classList.add('active');
      selectedSlotsDisplay.innerText = selectedSlots.join(', ');
      totalPriceDisplay.innerText = '₹' + totalPrice;
      selectedSlotsInput.value = selectedSlots.join(',');
    } else {
      bottomBar.classList.remove('active');
    }
  }
</script>

</body>
</html>

<?php
// Helper function to render a seat cleanly
function render_seat($id, $price) {
  global $slots_db;
  // Fallback to available if id not found in DB
  $status = isset($slots_db[$id]) ? $slots_db[$id] : 'available'; 
  
  // To avoid crowding text, we just print the number within the row.
  // We can calculate local number, or just print $id for simplicity.
  // Using $id is easier to identify in DB. We will just use the last digit or local number to make it look clean.
  $display_num = str_pad($id, 2, '0', STR_PAD_LEFT);
  if (strlen($display_num) > 2) $display_num = substr($display_num, -2);
  
  echo "<div class='seat $status' data-slot='$id' data-price='$price' title='Slot $id | ₹$price'>$display_num</div>";
}
?>
