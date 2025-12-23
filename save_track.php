<?php
// commandprocess.php - Updated to handle both command execution and track logging

// Configuration
$TRACK_FILE = 'currently_playing.txt';
$MAX_FILE_LINES = 50; // Keep only last 50 tracks to prevent file from growing too large

// Handle track logging via GET parameter
if (isset($_GET['track'])) {
    $track = $_GET['track'];
    
    // Clean the input (remove any trailing/leading whitespace)
    $track = trim($track);
    
    if (!empty($track)) {
        // Generate timestamp in format YYYYMMDDHHMMSS
        $timestamp = date('YmdHis');
        
        // Format: "20250209143022"_"Song Title" - "Album Name"
        // The incoming track should already be in the format: "Song Title" - "Album Name"
        // or just "Song Title" if no album
        
        // Parse the incoming track string
        $formattedTrack = "\"{$timestamp}\"_";
        
        // Check if track contains album separator " - "
        if (strpos($track, ' - ') !== false) {
            // Split into title and album
            $parts = explode(' - ', $track, 2);
            $title = trim($parts[0]);
            $album = isset($parts[1]) ? trim($parts[1]) : '';
            
            // Ensure both parts are quoted
            $title = trim($title, '"\'');
            $album = trim($album, '"\'');
            
            $formattedTrack .= "\"{$title}\" - \"{$album}\"";
        } else {
            // No album, just title
            $title = trim($track, '"\'');
            $formattedTrack .= "\"{$title}\" - \"\"";
        }
        
        // Read existing content
        $lines = [];
        if (file_exists($TRACK_FILE)) {
            $lines = file($TRACK_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        
        // Prepend new track
        array_unshift($lines, $formattedTrack);
        if (count($lines) > $MAX_FILE_LINES) {
            $lines = array_slice($lines, 0, $MAX_FILE_LINES);
        }
        if (file_put_contents($TRACK_FILE, implode(PHP_EOL, $lines) . PHP_EOL)) {
            echo "Track logged: {$formattedTrack}";
        } else {
            http_response_code(500);
            echo "Error writing to file";
        }
        exit;
    }
}

// Original command execution logic from your bash script
if (isset($_GET['command'])) {
    $command = $_GET['command'];
    
    // Execute the command (assuming this is running in an environment with ADB/shell access)
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo "Command executed successfully";
    } else {
        http_response_code(500);
        echo "Error executing command";
    }
    exit;
}

// Optional: Handle delete request if needed
if (isset($_GET['delete']) && $_GET['delete'] === 'ok') {
    // Your original delete logic would go here
    echo "Delete request received";
    exit;
}

// If no parameters provided, show usage
echo "Usage: 
- Log track: ?track=\"Song Title\" - \"Album Name\"
- Send command: ?command=am+broadcast+...
- Delete: ?delete=ok";
?>