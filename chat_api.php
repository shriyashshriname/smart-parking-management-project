<?php
header('Content-Type: application/json');

if (!isset($_POST['message'])) {
    echo json_encode(['reply' => 'No message received.']);
    exit;
}

$msg = strtolower($_POST['message']);
$reply = "I'm sorry, I don't understand that. You can ask me about Booking, Subscriptions, the Valetra Store, or VIP parking.";

if (strpos($msg, 'book') !== false || strpos($msg, 'park') !== false) {
    $reply = "To command a space, navigate to the **Book a Slot** section. You can choose from VIP, EV, SUV, or Standard zones.";
} elseif (strpos($msg, 'price') !== false || strpos($msg, 'cost') !== false || strpos($msg, 'plan') !== false) {
    $reply = "Valetra offers Free, Basic (₹499/mo), Premium (₹999/mo), and Ultimate (₹1999/mo) plans. Upgrading gives you Smart Coins and VIP access.";
} elseif (strpos($msg, 'store') !== false || strpos($msg, 'buy') !== false) {
    $reply = "Check out the Valetra Store! You can purchase car gear using Card/UPI or redeem your Smart Coins directly.";
} elseif (strpos($msg, 'coin') !== false || strpos($msg, 'cashback') !== false) {
    $reply = "You earn 3% Smart Coin cashback on every UPI/Card transaction. 1 Coin = 1 Rupee. Use them for free parking or store purchases!";
} elseif (strpos($msg, 'hello') !== false || strpos($msg, 'hi') !== false) {
    $reply = "Hello! Welcome to Valetra. How can I help you command your space today?";
}

echo json_encode(['reply' => $reply]);
?>
