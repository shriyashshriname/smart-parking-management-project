<?php
include 'db.php';

// 1. Reset all slots to available
$conn->query("UPDATE slots SET status='available'");

// 2. Mark all currently occupied vehicles as exited
$conn->query("UPDATE vehicles SET exit_time=NOW() WHERE exit_time IS NULL");

// 3. Create Store Products Table
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) DEFAULT 'store_placeholder.png',
    stock INT DEFAULT 10,
    category VARCHAR(50) DEFAULT 'Gear'
)");

// 4. Create Store Orders Table
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Processing', 'Shipped', 'Delivered') DEFAULT 'Processing',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 5. Seed Products if empty
$prod_check = $conn->query("SELECT * FROM products");
if($prod_check->num_rows == 0) {
    $products = [
        ['name' => 'Premium 4K Dashcam', 'price' => 2999.00, 'desc' => 'Dual lens front and rear camera with night vision and 24/7 parking monitor.'],
        ['name' => 'Portable Tyre Inflator', 'price' => 1499.00, 'desc' => 'Fast digital air compressor with auto shut-off and emergency LED light.'],
        ['name' => 'Smart Car Vacuum', 'price' => 999.00, 'desc' => 'High power cordless vacuum cleaner for deep cleaning your car interior.'],
        ['name' => 'Car Fire Extinguisher', 'price' => 599.00, 'desc' => 'Compact and lightweight fire extinguisher designed specifically for automotive use.'],
        ['name' => 'Memory Foam Neck Pillow', 'price' => 399.00, 'desc' => 'Ergonomic car seat headrest cushion for long comfortable drives.'],
        ['name' => 'Emergency Rescue Kit', 'price' => 1299.00, 'desc' => 'Complete roadside assistance kit including jumper cables, warning triangle, and first aid.']
    ];
    
    foreach($products as $p) {
        $name = $conn->real_escape_string($p['name']);
        $desc = $conn->real_escape_string($p['desc']);
        $price = $p['price'];
        $conn->query("INSERT INTO products (name, description, price, stock) VALUES ('$name', '$desc', $price, 20)");
    }
}

echo "Slots reset, vehicles cleared, and Store tables created successfully!";
?>
