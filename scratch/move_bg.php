<?php
$source = __DIR__ . '/../WhatsApp Image 2026-04-23 at 7.03.59 PM.jpeg';
$destDir = __DIR__ . '/../assets/img';
$destFile = $destDir . '/bg.jpg';

if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

if (file_exists($source)) {
    copy($source, $destFile);
    echo "Image successfully moved to assets/img/bg.jpg";
} else {
    echo "Image not found at $source";
}
?>
