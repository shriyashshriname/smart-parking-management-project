<?php
if(!isset($_SESSION)) session_start();
?>
<style>
  .floating-chat-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: var(--primary-gold, #eab308);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(234, 179, 8, 0.4);
    cursor: pointer;
    z-index: 9999;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    color: #000;
    font-size: 24px;
  }
  .floating-chat-btn:hover {
    transform: scale(1.1);
  }

  .floating-chat-box {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    height: 450px;
    background: #18181b;
    border: 1px solid #27272a;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.6);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(20px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
  }
  .floating-chat-box.active {
    transform: translateY(0);
    opacity: 1;
    pointer-events: all;
  }

  .fc-header {
    background: rgba(255,255,255,0.05);
    padding: 15px 20px;
    border-bottom: 1px solid #27272a;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .fc-header h3 {
    margin: 0; font-size: 16px; color: #fff; font-weight: 500;
  }
  .fc-close {
    background: transparent; border: none; color: #a1a1aa; cursor: pointer; font-size: 16px; transition: 0.2s;
  }
  .fc-close:hover { color: #fff; }

  .fc-body {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  
  .fc-msg { max-width: 80%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.4; }
  .fc-bot { background: #27272a; color: #f4f4f5; align-self: flex-start; border-bottom-left-radius: 2px; }
  .fc-user { background: #eab308; color: #000; align-self: flex-end; border-bottom-right-radius: 2px; }

  .fc-footer {
    padding: 15px;
    border-top: 1px solid #27272a;
    display: flex;
    gap: 10px;
    background: #18181b;
  }
  .fc-input {
    flex: 1; background: #27272a; border: 1px solid transparent;
    color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; outline: none;
  }
  .fc-input:focus { border-color: #eab308; }
  .fc-send {
    background: #eab308; color: #000; border: none; width: 40px; border-radius: 8px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center;
  }
  .fc-send:hover { transform: translateY(-2px); }
</style>

<div class="floating-chat-btn" id="fcBtn" onclick="toggleChat()">
  <i class="fa fa-comment-dots"></i>
</div>

<div class="floating-chat-box" id="fcBox">
  <div class="fc-header">
    <h3>Valetra AI Support</h3>
    <button class="fc-close" onclick="toggleChat()"><i class="fa fa-times"></i></button>
  </div>
  <div class="fc-body" id="fcBody">
    <div class="fc-msg fc-bot">Hello! I'm the Valetra AI. How can I help you command your space today?</div>
  </div>
  <div class="fc-footer">
    <input type="text" class="fc-input" id="fcInput" placeholder="Type your message..." onkeypress="if(event.key === 'Enter') sendChat()">
    <button class="fc-send" onclick="sendChat()"><i class="fa fa-paper-plane"></i></button>
  </div>
</div>

<script>
  function toggleChat() {
    document.getElementById('fcBox').classList.toggle('active');
  }

  async function sendChat() {
    const input = document.getElementById('fcInput');
    const msg = input.value.trim();
    if(!msg) return;

    // Add user message
    const body = document.getElementById('fcBody');
    body.innerHTML += `<div class="fc-msg fc-user">${msg}</div>`;
    input.value = '';
    body.scrollTop = body.scrollHeight;

    // Simulate thinking
    const botId = 'msg-' + Date.now();
    body.innerHTML += `<div class="fc-msg fc-bot" id="${botId}"><i class="fa fa-circle-notch fa-spin"></i> Typing...</div>`;
    body.scrollTop = body.scrollHeight;

    // Call API
    try {
      const response = await fetch('chat_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(msg)
      });
      const data = await response.json();
      document.getElementById(botId).innerHTML = data.reply;
    } catch(err) {
      document.getElementById(botId).innerHTML = "Sorry, I am currently offline. Please try again later.";
    }
    body.scrollTop = body.scrollHeight;
  }
</script>
