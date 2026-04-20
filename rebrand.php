<?php
$valetra_logo = '
<svg width="32" height="32" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="valetraGold" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fef08a"/>
      <stop offset="40%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#713f12"/>
    </linearGradient>
  </defs>
  <path d="M50 90 L25 30 C35 30 45 50 50 70 C55 50 65 30 75 30 Z" fill="url(#valetraGold)"/>
  <path d="M15 35 C30 40 35 55 40 65 C30 55 20 45 10 35 Z" fill="url(#valetraGold)"/>
  <path d="M85 35 C70 40 65 55 60 65 C70 55 80 45 90 35 Z" fill="url(#valetraGold)"/>
</svg>
';

$files = ['sidebar.php', 'login.php', 'register.php', 'index.php'];

foreach ($files as $file) {
    if(!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace SVG
    $content = preg_replace('/<svg.*?<\/svg>/s', $valetra_logo, $content, 1);
    
    // Replace names
    $content = str_replace('Smart Parking', 'Valetra', $content);
    $content = str_replace('Smart Parking - Login', 'Valetra - Login', $content);
    $content = str_replace('Smart Parking - Register', 'Valetra - Register', $content);
    $content = str_replace('Smart Parking - Maps', 'Valetra - Maps', $content);
    $content = str_replace('Park smarter,<br>live better', 'Command Every Space', $content); // In login.php
    $content = str_replace('Park Smarter,<br><em>Live Better</em>', 'Command<br><em>Every Space</em>', $content); // In index.php
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
?>
