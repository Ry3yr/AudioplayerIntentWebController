#!/bin/bash
URL="https://alcea-wisteria.de/PHP/0demo/2025-12-22-DeviceNAppCtrl/%5BAndroid%5D_Poweramp%E2%80%90%28Tasker%29%2820251222%29/commands.txt"
echo "Fetching commands from: $URL"
first_line=$(curl -s "$URL" | head -n 1)
if [ -z "$first_line" ]; then
    echo "Error: Could not fetch or file is empty"
    exit 1
fi
echo "First line: $first_line"
command="${first_line:17}"
echo "Executing command: $command"
eval "$command"
exit_code=$?
if [ $exit_code -eq 0 ]; then
    echo "Command executed successfully"
    delete_url="https://alcea-wisteria.de/PHP/0demo/2025-12-22-DeviceNAppCtrl/%5BAndroid%5D_Poweramp%E2%80%90%28Tasker%29%2820251222%29/commandprocess.php?delete=ok"
    echo "Making GET request to: $delete_url"
    delete_response=$(curl -s "$delete_url")
    curl_exit_code=$?
    if [ $curl_exit_code -eq 0 ]; then
        echo "Delete request sent successfully"
        echo "Response: $delete_response"
    else
        echo "Warning: Failed to send delete request (curl exit code: $curl_exit_code)"
    fi
else
    echo "Error executing command"
    exit 1
fi