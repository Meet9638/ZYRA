<?php
$file = __DIR__ . '/public/css/style.css';
$css = file_get_contents($file);
$css = preg_replace('/@php.*?@endphp;?/is', '/* BLADE PHP REMOVED */', $css);
file_put_contents($file, $css);
echo "Cleaned \\@php blocks from style.css\n";
