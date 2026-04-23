<?php
$output = [];
exec('git status 2>&1', $output);
echo "STATUS:\n" . implode("\n", $output) . "\n\n";

$output = [];
exec('git log -1 2>&1', $output);
echo "LOG:\n" . implode("\n", $output) . "\n\n";

$output = [];
exec('git remote -v 2>&1', $output);
echo "REMOTE:\n" . implode("\n", $output);
?>
