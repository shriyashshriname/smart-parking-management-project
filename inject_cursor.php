<?php
// Car Cursor SVG (Top-down view, gold colored, angled top-left)
$cursor_css = "
<style>
  body, a, button, input, select {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"%23eab308\" stroke=\"black\" stroke-width=\"1\"><path d=\"M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z\" transform=\"rotate(-45 12 12)\"/></svg>') 4 4, auto !important;
  }
  /* Optional hover effect cursor (slightly larger or different color) */
  a:hover, button:hover {
    cursor: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"%2322c55e\" stroke=\"black\" stroke-width=\"1\"><path d=\"M17 21H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zM8 4v2h8V4H8zm0 14h8v-2H8v2z\" transform=\"rotate(-45 12 12)\"/></svg>') 4 4, pointer !important;
  }
</style>
";

$files = ['sidebar.php', 'index.php', 'login.php', 'register.php'];

foreach($files as $file){
    $content = file_get_contents($file);
    if(strpos($content, 'cursor: url') === false){
        // Inject right before </head> if it exists
        if(strpos($content, '</head>') !== false){
            $content = str_replace('</head>', $cursor_css . "\n</head>", $content);
        } else {
            // If no </head> (like sidebar.php), append to top
            $content = $cursor_css . "\n" . $content;
        }
        file_put_contents($file, $content);
        echo "Injected cursor into $file\n";
    } else {
        echo "Cursor already in $file\n";
    }
}
?>
