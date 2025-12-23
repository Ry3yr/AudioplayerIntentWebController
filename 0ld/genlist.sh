#!/bin/sh

BASE="/mnt/media_rw/4041-393D/Files/mobile"
OUT="list.txt"
TEMP="${OUT}.tmp"

# Find first MP3 file in each directory
find "$BASE" -type f \( -name "*.mp3" -o -name "*.MP3" \) | while read -r file; do
  dir=$(dirname "$file")
  # Only print if this is the first file in this directory
  if [ ! -f "$dir/.processed" ]; then
    echo "$file"
    touch "$dir/.processed"
  fi
done > "$TEMP"

# Clean up the marker files
find "$BASE" -name ".processed" -delete

# Second pass: clean up (remove everything before last "| ")
sed 's/^.*| //' "$TEMP" | sort > "$OUT"

# Clean up temp file
rm "$TEMP"