<?php
include 'db.php';

// Create activity_logs table
$conn->query("CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  detail TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Ensure all 150 slots exist and are bookable
$conn->query("CREATE TABLE IF NOT EXISTS slots (
  slot_id INT PRIMARY KEY,
  status ENUM('available','occupied') DEFAULT 'available',
  category VARCHAR(20) DEFAULT 'normal'
)");

// Seed slot categories
$categories = [];
for($i=1; $i<=30; $i++) $categories[$i]='vip';
for($i=31; $i<=60; $i++) $categories[$i]='ev';
for($i=61; $i<=90; $i++) $categories[$i]='suv';
for($i=91; $i<=150; $i++) $categories[$i]='normal';

// Check if category column exists, add if not
$cols = $conn->query("SHOW COLUMNS FROM slots LIKE 'category'")->num_rows;
if($cols == 0) $conn->query("ALTER TABLE slots ADD COLUMN category VARCHAR(20) DEFAULT 'normal'");

for($i=1; $i<=150; $i++){
    $cat = $categories[$i];
    $exists = $conn->query("SELECT slot_id FROM slots WHERE slot_id=$i")->num_rows;
    if($exists == 0){
        $conn->query("INSERT INTO slots (slot_id, status, category) VALUES ($i, 'available', '$cat')");
    } else {
        $conn->query("UPDATE slots SET category='$cat' WHERE slot_id=$i");
    }
}

echo "All done! 150 slots seeded, activity_logs table created.";
?>
