#!/bin/sh

while true; do
    echo Sending...
    curl http://localhost/cronme.php?job=move_reports_to_s3
    sleep 1s
done
