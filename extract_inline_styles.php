<?php

// Script to extract all inline styles from Blade templates into CSS

$viewsDir = __DIR__ . '/resources/views';
$cssFile = __DIR__ . '/public/css/style.css';

echo "Scanning $viewsDir for inline styles...\n";

// Recursive directory iterator to get all .blade.php files
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $files[] = $file->getPathname();
    }
}

echo "Found " . count($files) . " Blade files.\n";

$existingCss = file_get_contents($cssFile);
$newCssBlocks = [];
$totalReplacements = 0;

$styleHashToClassMap = []; // Maps md5(style_content) to class name
$classCounter = 1;

foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;

    // Regex to match style="..." attributes, being careful with quotes
    // Matches style="something" or style='something'
    $modifiedContent = preg_replace_callback('/style\s*=\s*(["\'])(.*?)\1/is', function ($matches) use (&$newCssBlocks, &$totalReplacements, &$styleHashToClassMap, &$classCounter) {
        $quoteChar = $matches[1];
        $styleContent = trim($matches[2]);
        
        // Skip empty styles or ones that look like Blade expressions (which we might not want to perfectly parse via regex)
        if (empty($styleContent) || str_contains($styleContent, '{{') || str_contains($styleContent, '!!')) {
            return $matches[0];
        }

        // Normalize the style content slightly to group identical styles
        $normalizedStyle = preg_replace('/\s+/', ' ', $styleContent);
        // Ensure it ends with a semicolon for clean CSS output if it doesn't have one
        if (!str_ends_with($normalizedStyle, ';')) {
            $normalizedStyle .= ';';
        }

        $hash = md5($normalizedStyle);

        if (!isset($styleHashToClassMap[$hash])) {
            $className = "auto-style-" . str_pad($classCounter++, 4, '0', STR_PAD_LEFT);
            $styleHashToClassMap[$hash] = $className;
            
            // Format nice CSS
            $formattedStyle = "\n." . $className . " {\n    " . str_replace(';', ";\n    ", trim($normalizedStyle, ';')) . ";\n}";
            $formattedStyle = str_replace("    \n", "", $formattedStyle); // Clean up empty indented lines
            $newCssBlocks[] = $formattedStyle;
        }

        $className = $styleHashToClassMap[$hash];
        $totalReplacements++;

        return 'class="' . $className . '"';
    }, $content);


    // Now, doing a second pass because elements might ALREADY have a class attribute.
    // If they do, we need to merge our new class with their existing class.
    // The previous pass just blindly replaced `style="..."` with `class="auto-style-X"`.
    // Let's refine the approach. It's actually safer to do this with DOM parser, but since there's blade syntax, DOM parser will fail.
    // A robust regex approach: 
    // Find <tag ... style="..." ... >
    
    // We'll revert the previous simple regex and use a tag-level regex.
    $improvedContent = preg_replace_callback('/<([a-zA-Z0-9\-]+)([^>]*?)style\s*=\s*(["\'])(.*?)\3([^>]*?)>/is', function ($matches) use (&$newCssBlocks, &$totalReplacements, &$styleHashToClassMap, &$classCounter) {
        $tag = $matches[1];
        $beforeStyle = $matches[2];
        $quoteChar = $matches[3];
        $styleContent = trim($matches[4]);
        $afterStyle = $matches[5];

        if (empty($styleContent) || str_contains($styleContent, '{{') || str_contains($styleContent, '!!')) {
            return $matches[0];
        }

        $normalizedStyle = preg_replace('/\s+/', ' ', $styleContent);
        if (!str_ends_with($normalizedStyle, ';')) {
            $normalizedStyle .= ';';
        }

        $hash = md5($normalizedStyle);

        if (!isset($styleHashToClassMap[$hash])) {
            $className = "auto-style-" . str_pad($classCounter++, 4, '0', STR_PAD_LEFT);
            $styleHashToClassMap[$hash] = $className;
            
            $formattedStyle = "\n." . $className . " {\n    " . str_replace(';', ";\n    ", trim($normalizedStyle, ';')) . ";\n}";
            $formattedStyle = str_replace("    \n", "", $formattedStyle); 
            $newCssBlocks[] = $formattedStyle;
        }

        $className = $styleHashToClassMap[$hash];
        $totalReplacements++;

        $restOfAttributes = $beforeStyle . ' ' . $afterStyle;
        
        // Check if there is already a class attribute
        if (preg_match('/class\s*=\s*(["\'])(.*?)\1/is', $restOfAttributes, $classMatches)) {
            // Append our class to existing classes
            $existingClasses = $classMatches[2];
            $newClasses = trim($existingClasses) . ' ' . $className;
            
            // Replace the old class attribute with the new one
            $restOfAttributes = preg_replace('/class\s*=\s*["\'].*?["\']/is', 'class="' . $newClasses . '"', $restOfAttributes, 1);
            
            return '<' . $tag . $restOfAttributes . '>';
        } else {
            // No existing class attribute, just add ours
            return '<' . $tag . $restOfAttributes . ' class="' . $className . '">';
        }

    }, $originalContent);

    // Save if modified
    if ($originalContent !== $improvedContent) {
        file_put_contents($filePath, $improvedContent);
    }
}

if (!empty($newCssBlocks)) {
    echo "Appending " . count($newCssBlocks) . " unique auto-generated CSS classes to style.css...\n";
    file_put_contents($cssFile, "\n\n/* ==========================================\n   AUTO-EXTRACTED INLINE STYLES\n   ========================================== */\n" . implode("\n", $newCssBlocks), FILE_APPEND);
}

echo "Done! Extracted and replaced $totalReplacements inline styles.\n";

?>
