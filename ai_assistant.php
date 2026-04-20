<?php
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Parking - AI Assistant</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --bg-color: #09090b; --panel-bg: #18181b; --border-color: #27272a;
    --text-main: #f4f4f5; --text-muted: #a1a1aa; --primary-green: #22c55e;
    --primary-red: #ef4444; --primary-gold: #eab308; --primary-blue: #3b82f6; --sidebar-width: 260px;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--bg-color); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }

  .sidebar { width: var(--sidebar-width); background: var(--panel-bg); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 24px; z-index: 10; }
  .brand { display: flex; align-items: center; gap: 12px; font-family: 'Instrument Serif', serif; font-size: 24px; margin-bottom: 40px; }
  .nav-menu { list-style: none; flex: 1; }
  .nav-menu a { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s; }
  .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.05); color: var(--text-main); }
  .nav-menu a.active i { color: var(--primary-green); }
  .logout-btn { display: flex; align-items: center; gap: 12px; color: var(--text-muted); text-decoration: none; padding: 12px 16px; transition: 0.2s; margin-top: auto; }
  .logout-btn:hover { background: rgba(239,68,68,0.1); color: var(--primary-red); }

  .chat-container { flex: 1; display: flex; flex-direction: column; background: var(--bg-color); }
  
  .chat-header { padding: 20px 40px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 15px; }
  .ai-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(34,197,94,0.1); color: var(--primary-green); display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid rgba(34,197,94,0.3); }
  .chat-header h2 { font-family: 'Instrument Serif', serif; font-size: 24px; font-weight: 400; }
  .chat-header p { font-size: 13px; color: var(--text-muted); }

  .chat-messages { flex: 1; padding: 40px; overflow-y: auto; display: flex; flex-direction: column; gap: 24px; scroll-behavior: smooth; }
  
  .message { display: flex; gap: 16px; max-width: 800px; margin: 0 auto; width: 100%; }
  .message.user { flex-direction: row-reverse; }
  
  .msg-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .message.ai .msg-avatar { background: rgba(34,197,94,0.1); color: var(--primary-green); border: 1px solid rgba(34,197,94,0.3); }
  .message.user .msg-avatar { background: var(--panel-bg); border: 1px solid var(--border-color); color: var(--text-main); }
  
  .msg-bubble { padding: 16px 20px; border-radius: 16px; font-size: 15px; line-height: 1.6; }
  .message.ai .msg-bubble { background: var(--panel-bg); border: 1px solid var(--border-color); color: var(--text-main); border-top-left-radius: 4px; }
  .message.user .msg-bubble { background: var(--primary-green); color: #000; border-top-right-radius: 4px; }

  .chat-input-wrapper { padding: 30px 40px; background: var(--bg-color); border-top: 1px solid var(--border-color); }
  .chat-input-container { max-width: 800px; margin: 0 auto; position: relative; display: flex; align-items: center; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 24px; padding: 8px 16px; }
  .chat-input-container input { flex: 1; background: transparent; border: none; color: var(--text-main); font-size: 15px; padding: 12px; outline: none; font-family: 'Inter', sans-serif; }
  .send-btn { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-green); color: #000; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; flex-shrink: 0; }
  .send-btn:hover { transform: scale(1.05); }

  /* Typing indicator */
  .typing-indicator { display: none; gap: 4px; padding: 16px 20px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; border-top-left-radius: 4px; width: fit-content; }
  .typing-indicator span { width: 6px; height: 6px; background: var(--text-muted); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both; }
  .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
  .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
  @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="chat-container">
  <div class="chat-header">
    <div class="ai-avatar"><i class="fa fa-robot"></i></div>
    <div>
      <h2>Parking AI Assistant</h2>
      <p>Ask me anything about your parking slots, EV charging, or subscriptions.</p>
    </div>
  </div>

  <div class="chat-messages" id="chatBox">
    <div class="message ai">
      <div class="msg-avatar"><i class="fa fa-robot"></i></div>
      <div class="msg-bubble">Hello! I am your Smart Parking AI Assistant. How can I help you today? You can ask me about finding your car, booking a VIP slot, Smart Coins, or our Car Gear Store.</div>
    </div>
  </div>

  <div class="chat-input-wrapper">
    <form class="chat-input-container" id="chatForm">
      <input type="text" id="userInput" placeholder="Ask a question..." autocomplete="off" required>
      <button type="submit" class="send-btn"><i class="fa fa-paper-plane"></i></button>
    </form>
  </div>
</main>

<script>
  const chatForm = document.getElementById('chatForm');
  const chatBox = document.getElementById('chatBox');
  const userInput = document.getElementById('userInput');

  // Simple hardcoded AI logic for simulation
  const knowledgeBase = [
    { keys: ["book", "reserve", "slot"], ans: "You can easily book a slot by navigating to the 'Book a Slot' page from your sidebar. We offer Normal, SUV, EV, and VIP Platinum slots!" },
    { keys: ["ev", "charging", "electric"], ans: "We have dedicated EV Charging Zones! When booking, select the green EV slots. Our EV chargers support fast charging up to 50kW." },
    { keys: ["vip", "platinum"], ans: "VIP Platinum slots are located closest to the exit and come with priority valet service. They are perfect for saving time." },
    { keys: ["coin", "smart coin", "cashback"], ans: "You earn 3% Smart Coins on every UPI or Card payment! You can use these coins to buy subscriptions or car gear from our Store. 1 Coin = ₹1." },
    { keys: ["store", "buy", "gear", "dashcam", "safety"], ans: "We just launched our Car Gear Store! You can buy premium safety equipment like 4K Dashcams, Tyre Inflators, and Fire Extinguishers directly from the 'Car Gear Store' tab." },
    { keys: ["find", "where", "my car", "lost"], ans: "Can't find your car? Go to the 'Find My Car' page. Your parked slot will be glowing in blue so you can easily locate it!" },
    { keys: ["price", "cost", "plan", "subscription"], ans: "We offer Free, Basic (₹499/mo), Premium (₹999/mo), Ultimate (₹1999/mo), and B2B Enterprise plans. Check the Pricing page for full details!" },
    { keys: ["exit", "leave", "done"], ans: "When you're ready to leave, go to 'My Dashboard' and click the red 'Exit Slot' button on your active parking ticket. Your slot will be released instantly." },
  ];

  chatForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = userInput.value.trim();
    if(!text) return;

    // Add user message
    appendMessage('user', text);
    userInput.value = '';

    // Show typing indicator
    const typingId = showTyping();

    // Simulate network delay
    setTimeout(() => {
      document.getElementById(typingId).remove();
      
      let response = "I'm sorry, I don't have an answer for that right now. Please try asking about booking slots, finding your car, EV charging, Smart Coins, or the Car Gear Store!";
      const lowerText = text.toLowerCase();
      
      for(let kb of knowledgeBase) {
        if(kb.keys.some(k => lowerText.includes(k))) {
          response = kb.ans;
          break;
        }
      }

      appendMessage('ai', response);
    }, 1200);
  });

  function appendMessage(sender, text) {
    const div = document.createElement('div');
    div.className = `message ${sender}`;
    div.innerHTML = `
      <div class="msg-avatar"><i class="fa ${sender === 'ai' ? 'fa-robot' : 'fa-user'}"></i></div>
      <div class="msg-bubble">${text}</div>
    `;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  function showTyping() {
    const id = 'typing-' + Date.now();
    const div = document.createElement('div');
    div.className = `message ai`;
    div.id = id;
    div.innerHTML = `
      <div class="msg-avatar"><i class="fa fa-robot"></i></div>
      <div class="typing-indicator" style="display:flex;"><span></span><span></span><span></span></div>
    `;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
    return id;
  }
</script>
</body>
</html>
