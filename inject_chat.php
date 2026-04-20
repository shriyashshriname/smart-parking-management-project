<?php
// 1. Inject into sidebar.php
$sb = file_get_contents('sidebar.php');
if (strpos($sb, 'chat_widget.php') === false) {
    $sb = str_replace('</aside>', "</aside>\n<?php include 'chat_widget.php'; ?>\n", $sb);
    file_put_contents('sidebar.php', $sb);
    echo "Injected chat into sidebar.php\n";
}

// 2. Expand index.php and inject chat
$idx = file_get_contents('index.php');

$deep_dive_html = '
<section class="section" id="deepdive" style="background:#000;">
  <div style="text-align:center">
    <div class="section-label">Ecosystem</div>
    <div class="section-title">Command Your Network</div>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Drivers</h3>
      <p style="color:var(--muted)">Seamlessly locate, book, and pay for parking spots from your phone. Enjoy VIP zones, EV charging bays, and automated entry without ever rolling down your window.</p>
    </div>
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Businesses</h3>
      <p style="color:var(--muted)">Reserve blocks of spaces for your employees or clients. Utilize Valetra\'s B2B tools to manage corporate accounts and track fleet parking expenses dynamically.</p>
    </div>
    <div class="feat-card" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
      <h3>For Operators</h3>
      <p style="color:var(--muted)">Transform your empty lots into smart hubs. Connect to the Valetra network to maximize utilization, automate billing, and increase your parking revenue instantly.</p>
    </div>
  </div>
</section>

<section class="section" id="testimonials" style="background:var(--bg);">
  <div style="text-align:center">
    <div class="section-label">Trust</div>
    <div class="section-title">Loved by Thousands</div>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"Valetra completely changed my commute. I no longer waste 20 minutes circling the block in Pune. I just book my slot and drive straight in."</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Rajesh K.</h4>
    </div>
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"The Smart Coin cashback is amazing. I use my cashback to buy car gear from the integrated store. Brilliant ecosystem!"</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Priya S.</h4>
    </div>
    <div class="feat-card" style="border: 1px solid var(--border);">
      <p style="color:var(--muted); font-style: italic;">"As a fleet manager, the B2B dashboard gives me total control over where my drivers are parking and exactly how much it costs."</p>
      <h4 style="margin-top: 20px; color: var(--gold);">Amit D.</h4>
    </div>
  </div>
</section>
';

$footer_html = '
<footer style="background: #000; padding: 60px 40px; border-top: 1px solid var(--border); margin-top: auto;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between;">
    <div style="max-width: 300px;">
      <div class="logo" style="margin-bottom: 20px; color: #fff; font-size: 24px; font-family: \'Instrument Serif\', serif;">Valetra</div>
      <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Command Every Space. Valetra is the world\'s leading AI-powered parking network, reducing traffic and optimizing urban spaces.</p>
    </div>
    <div style="display: flex; gap: 60px;">
      <div>
        <h4 style="color: #fff; margin-bottom: 20px; font-size: 15px;">Product</h4>
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:10px;">
          <li><a href="#" style="color: var(--muted); text-decoration: none; font-size: 14px;">Live Map</a></li>
          <li><a href="subscriptions.php" style="color: var(--muted); text-decoration: none; font-size: 14px;">Pricing</a></li>
          <li><a href="#" style="color: var(--muted); text-decoration: none; font-size: 14px;">Valetra Store</a></li>
        </ul>
      </div>
      <div>
        <h4 style="color: #fff; margin-bottom: 20px; font-size: 15px;">Company</h4>
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:10px;">
          <li><a href="#" style="color: var(--muted); text-decoration: none; font-size: 14px;">About Us</a></li>
          <li><a href="#" style="color: var(--muted); text-decoration: none; font-size: 14px;">Careers</a></li>
          <li><a href="#" style="color: var(--muted); text-decoration: none; font-size: 14px;">Contact</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div style="max-width: 1200px; margin: 40px auto 0; padding-top: 20px; border-top: 1px solid var(--border); text-align: center; color: var(--muted); font-size: 13px;">
    &copy; 2026 Valetra Technologies. All rights reserved.
  </div>
</footer>
<?php include \'chat_widget.php\'; ?>
</body>
';

if (strpos($idx, 'id="deepdive"') === false) {
    // Replace the basic <footer> with our new one
    $idx = preg_replace('/<footer.*?<\/footer>\s*<\/body>/s', $footer_html, $idx);
    
    // Insert new sections before footer
    $idx = str_replace('<footer', $deep_dive_html . "\n<footer", $idx);
    
    file_put_contents('index.php', $idx);
    echo "Expanded index.php\n";
}
?>
