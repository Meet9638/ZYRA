<?php

$cssFile = __DIR__ . '/public/css/style.css';
$cssFileContent = file_get_contents($cssFile);

$filesToExtractAndAppend = [
    'resources/views/admin/users/index.blade.php',
    'resources/views/admin/users/show.blade.php',
    'resources/views/admin/auth/login.blade.php'
];

$filesJustDelete = [
    'resources/views/welcome.blade.php',
    'resources/views/auth/register.blade.php',
    'resources/views/auth/login.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/admin/products/create.blade.php'
];

$cssToAppend = "";

foreach ($filesToExtractAndAppend as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (preg_match('/<style>(.*?)<\/style>/is', $content, $matches)) {
            $cssToAppend .= "\n/* Appended from $file */\n" . trim($matches[1]) . "\n";
            $content = preg_replace('/<style>.*?<\/style>/is', '', $content);
            file_put_contents($path, $content);
            echo "Extracted & deleted from $file\n";
        }
    }
}

if (!empty($cssToAppend)) {
    // Append before the auto-styles block if it exists
    if (strpos($cssFileContent, '/* ==========================================') !== false) {
        $cssFileContent = str_replace('/* ==========================================', $cssToAppend . "\n/* ==========================================", $cssFileContent);
    } else {
        $cssFileContent .= $cssToAppend;
    }
    file_put_contents($cssFile, $cssFileContent);
    echo "Appended missing CSS to style.css\n";
}

foreach ($filesJustDelete as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (preg_match('/<style>.*?<\/style>/is', $content)) {
            $content = preg_replace('/<style>.*?<\/style>/is', '', $content);
            file_put_contents($path, $content);
            echo "Deleted redundant <style> from $file\n";
        }
    }
}

echo "Done fixing CSS!\n";
