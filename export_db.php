<?php
// Database Export
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'parkingapp';
$port = 3305;

$command = "c:\\xampp\\mysql\\bin\\mysqldump.exe -u {$db_user} -P {$port} -h {$db_host} {$db_name} > parking_db.sql";
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "<h3>✅ تم تصدير قاعدة البيانات بنجاح في ملف parking_db.sql</h3>";
} else {
    echo "<h3>❌ فشل تصدير قاعدة البيانات. يرجى تصديرها يدوياً من phpMyAdmin.</h3>";
}

// Zip Project
$rootPath = realpath(__DIR__);
$zipName = 'parking_project.zip';
if (file_exists($zipName)) {
    unlink($zipName);
}

$zip = new ZipArchive();
if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
            
            // Exclude git folder and the zip itself
            if (strpos($relativePath, '.git') === false && $relativePath !== $zipName) {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    $zip->close();
    echo "<h3>✅ تم ضغط المشروع بالكامل في ملف parking_project.zip</h3>";
} else {
    echo "<h3>❌ فشل ضغط المشروع.</h3>";
}

echo "<br><a href='dashboard.php' style='display:inline-block; padding:10px 20px; background:#1a73e8; color:white; text-decoration:none; border-radius:5px;'>العودة للمشروع</a>";
?>
