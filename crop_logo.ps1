Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile("d:\SEM 4\LARAVEL\ZYRA\public\images\logo.png")
$bmp = new-object System.Drawing.Bitmap($img)

$minX = $bmp.Width
$minY = $bmp.Height
$maxX = 0
$maxY = 0

# Sample the background color from a few corners and take average
$bg1 = $bmp.GetPixel(0, 0)
$bg2 = $bmp.GetPixel($bmp.Width - 1, 0)
$bg3 = $bmp.GetPixel(0, $bmp.Height - 1)
$avgR = ($bg1.R + $bg2.R + $bg3.R) / 3
$avgG = ($bg1.G + $bg2.G + $bg3.G) / 3
$avgB = ($bg1.B + $bg2.B + $bg3.B) / 3

for ($y = 0; $y -lt $bmp.Height; $y++) {
    for ($x = 0; $x -lt $bmp.Width; $x++) {
        $color = $bmp.GetPixel($x, $y)
        if ([Math]::Abs($color.R - $avgR) -gt 15 -or [Math]::Abs($color.G - $avgG) -gt 15 -or [Math]::Abs($color.B - $avgB) -gt 15) {
            if ($x -lt $minX) { $minX = $x }
            if ($x -gt $maxX) { $maxX = $x }
            if ($y -lt $minY) { $minY = $y }
            if ($y -gt $maxY) { $maxY = $y }
        }
    }
}

# Add some padding
$pad = 20
$minX = [Math]::Max(0, $minX - $pad)
$minY = [Math]::Max(0, $minY - $pad)
$maxX = [Math]::Min($bmp.Width - 1, $maxX + $pad)
$maxY = [Math]::Min($bmp.Height - 1, $maxY + $pad)

$width = $maxX - $minX + 1
$height = $maxY - $minY + 1

Write-Host "Crop Rect: $minX, $minY, $width, $height"

$rect = New-Object System.Drawing.Rectangle($minX, $minY, $width, $height)
$cropped = $bmp.Clone($rect, $bmp.PixelFormat)
$cropped.Save("d:\SEM 4\LARAVEL\ZYRA\public\images\logo_cropped.png")

$cropped.Dispose()
$bmp.Dispose()
$img.Dispose()

