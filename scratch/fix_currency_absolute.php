<?php
$viewsPath = 'c:/Users/meet/OneDrive/Desktop/ZYRA/resources/views';
$it = new RecursiveDirectoryIterator($viewsPath);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->isFile() && $file->getExtension() === 'blade') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $replaced = false;
        if (str_contains($content, '?{{')) {
            $content = str_replace('?{{', '&#8377;{{', $content);
            $replaced = true;
        }
        
        if ($replaced) {
            file_put_contents($path, $content);
            echo "Fixed: $path\n";
        }
    }
}
echo "Global fix complete.\n";
