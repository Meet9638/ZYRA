<?php
$viewsPath = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'blade') {
        $content = file_get_contents($file->getPathname());
        if (str_contains($content, '?{{')) {
            $newContent = str_replace('?{{', '&#8377;{{', $content);
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
