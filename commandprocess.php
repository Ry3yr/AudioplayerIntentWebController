<?php
header('Content-Type: text/plain');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
date_default_timezone_set('Europe/Berlin');
if (isset($_GET['delete']) && $_GET['delete'] === 'ok') {
    $commandFile = 'commands.txt';
    if (file_exists($commandFile)) {
        if (unlink($commandFile)) {
            echo "OK: Command file deleted";
        } else {
            echo "ERROR: Could not delete command file";
        }
    } else {
        echo "OK: Command file does not exist (already deleted)";
    }
    exit;
}
$command = $_GET['command'] ?? '';
if ($command === '') {
    echo "ERROR: No command provided";
    exit;
}
$timestamp = date('YmdHis');
$entry = $timestamp . ' - ' . $command;
$commandFile = 'commands.txt';
$existingCommands = [];
if (file_exists($commandFile)) {
    $existingCommands = file($commandFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
array_unshift($existingCommands, $entry);
$existingCommands = array_slice($existingCommands, 0, 4);
if (file_put_contents($commandFile, implode(PHP_EOL, $existingCommands) . PHP_EOL) === false) {
    echo "ERROR: Could not write to command file";
    exit;
}
?>