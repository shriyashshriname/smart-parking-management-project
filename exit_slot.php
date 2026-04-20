<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';
include 'log_activity.php';

$user_id = $_SESSION['user_id'];

if(isset($_POST['exit_booking']) && isset($_POST['vehicle_id']) && isset($_POST['slot_id'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $slot_id    = intval($_POST['slot_id']);

    // Verify this booking belongs to this user
    $check = $conn->query("SELECT id, UNIX_TIMESTAMP(entry_time) as entry_ts FROM vehicles WHERE id=$vehicle_id AND user_id=$user_id AND exit_time IS NULL");
    if($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        
        // Set exit time on vehicle record
        $conn->query("UPDATE vehicles SET exit_time=NOW() WHERE id=$vehicle_id");
        // Free up the slot
        $conn->query("UPDATE slots SET status='available' WHERE slot_id=$slot_id");
        
        // Overtime check for Free Plan
        $user = $conn->query("SELECT plan, wallet_balance FROM users WHERE id=$user_id")->fetch_assoc();
        if($user['plan'] == 'free') {
            $duration_seconds = time() - $row['entry_ts'];
            $duration_minutes = $duration_seconds / 60;
            $max_minutes = 8 * 60; // 8 hours
            
            if($duration_minutes > $max_minutes) {
                $extra_minutes = ceil($duration_minutes - $max_minutes);
                $penalty = $extra_minutes * 1; // 1 rs per minute
                
                $new_balance = $user['wallet_balance'] - $penalty;
                $conn->query("UPDATE users SET wallet_balance=$new_balance WHERE id=$user_id");
                log_activity($conn, $user_id, 'OVERTIME_PENALTY', "Charged ₹$penalty for $extra_minutes min overtime on free plan.");
            }
        }
        
        log_activity($conn, $user_id, 'EXIT_SLOT', "Exited slot $slot_id for vehicle ID $vehicle_id");
    }
}

header("Location: user_dashboard.php");
exit();
?>
