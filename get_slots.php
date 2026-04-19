<?php
include 'db.php';

$result = $conn->query("SELECT * FROM slots");

$slots = [];

while($row = $result->fetch_assoc()){
  $slots[] = $row;
}

echo json_encode($slots);
?>