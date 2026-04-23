<?php
$source = 'C:\\Users\\SANFOR\\.gemini\\antigravity\\brain\\9886a9ba-6e43-4b73-afd2-23775d15362f\\.tempmediaStorage\\media_9886a9ba-6e43-4b73-afd2-23775d15362f_1776886820382.png';
$dest = 'c:\\xampp\\htdocs\\parking\\assets\\img\\bg.jpg';

if (!is_dir('c:\\xampp\\htdocs\\parking\\assets\\img')) {
    mkdir('c:\\xampp\\htdocs\\parking\\assets\\img', 0777, true);
}

if (file_exists($source)) {
    copy($source, $dest);
    echo "Copied successfully!";
} else {
    echo "Source file not found.";
}
?>
