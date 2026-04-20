<?php
$c = file_get_contents('index.php');

// Replace the fc-slots and fc-footer with the video image
$c = preg_replace(
    '/<div class="fc-slots">.*?<\/div>.*?<div class="fc-footer">.*?<\/div>/s',
    '<div style="padding: 15px; display:flex; justify-content:center;"><img src="parking_loop.webp" style="width: 100%; max-width: 400px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 5px 15px rgba(0,0,0,0.5);" alt="Live Parking Video Loop"></div>',
    $c
);

// Add Social Impact Section before <section class="section" id="how"
$impact_html = '
<section class="section" id="impact" style="background:var(--bg);border-top:1px solid var(--border)">
  <div style="text-align:center">
    <div class="section-label">Social & Environmental Impact</div>
    <div class="section-title">Driving a better tomorrow</div>
    <p class="section-sub">Smart Parking isn\'t just about convenience. It\'s about creating smarter, greener, and safer cities.</p>
  </div>
  <div class="features-grid" style="margin-top: 40px;">
    <div class="feat-card reveal" style="--accent:var(--green)">
      <div class="feat-icon" style="background:rgba(34,197,94,.1);color:var(--green)"><i class="fa fa-leaf"></i></div>
      <h3>Reducing Traffic & Emissions</h3>
      <p>By eliminating the time spent circling for parking, we drastically reduce city congestion and cut down harmful vehicle emissions.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--blue)">
      <div class="feat-icon" style="background:rgba(59,130,246,.1);color:var(--blue)"><i class="fa fa-piggy-bank"></i></div>
      <h3>Economical Savings</h3>
      <p>Less idle driving means significantly lower fuel consumption. Our affordable, competitive pricing makes parking accessible to everyone.</p>
    </div>
    <div class="feat-card reveal" style="--accent:var(--gold)">
      <div class="feat-icon" style="background:rgba(234,179,8,.1);color:var(--gold)"><i class="fa fa-shield-halved"></i></div>
      <h3>Enhanced Security</h3>
      <p>With precise digital tracking, dedicated VIP zones, and automated entry/exit logging, your vehicle is secured 24/7 in our monitored hubs.</p>
    </div>
  </div>
</section>
';

$c = str_replace('<section class="section" id="how"', $impact_html . "\n" . '<section class="section" id="how"', $c);

file_put_contents('index.php', $c);
echo "Updated index.php successfully.\n";
?>
