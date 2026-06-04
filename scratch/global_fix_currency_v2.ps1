$views = Get-ChildItem -Path resources\views -Filter *.blade.php -Recurse
foreach ($file in $views) {
    $content = Get-Get-Content $file.FullName
    // Replace broken markers AND literal Rupee characters with the safe HTML entity
    $newContent = $content -replace '\?\{\{', '&#8377;{{'
    $newContent = $newContent -replace '₹\{\{', '&#8377;{{'
    if ($content -ne $newContent) {
        $newContent | Set-Content $file.FullName
        Write-Host "Fixed: $($file.FullName)"
    }
}
