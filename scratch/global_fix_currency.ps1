$views = Get-ChildItem -Path resources\views -Filter *.blade.php -Recurse
foreach ($file in $views) {
    $content = Get-Content $file.FullName
    $newContent = $content -replace '\?\{\{', '&#8377;{{'
    if ($content -ne $newContent) {
        $newContent | Set-Content $file.FullName
        Write-Host "Fixed: $($file.FullName)"
    }
}
