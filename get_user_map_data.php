<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$query = "
    SELECT s.slot_id, s.status, v.user_id as owner_id, v.vehicle_no
    FROM slots s
    LEFT JOIN vehicles v ON s.slot_id = v.slot_id AND v.exit_time IS NULL
    ORDER BY s.slot_id ASC
";
$result = $conn->query($query);

$data = [];
while($row = $result->fetch_assoc()) {
    $item = [
        'slot_id' => $row['slot_id'],
        'status' => $row['status'],
        'is_mine' => false,
        'vehicle_no' => null
    ];
    
    if($row['status'] == 'occupied') {
        if($row['owner_id'] == $user_id) {
            $item['is_mine'] = true;
            $item['vehicle_no'] = $row['vehicle_no'];
        }
    }
    
    $data[] = $item;
}

echo json_encode($data);
?>
