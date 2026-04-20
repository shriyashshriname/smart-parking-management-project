<?php
$lines = file('index.php');
foreach(array_slice($lines, 0, 70) as $i => $line) {
    echo ($i+1) . ': ' . $line;
}
?>
