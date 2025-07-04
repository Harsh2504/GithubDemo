#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.

# Absolute path to PHP
PHP_PATH=$(which php)

# Absolute path to cron.php
CRON_FILE_PATH=$(realpath "$(dirname "$0")/cron.php")

# # Cron expression: every 24 hours at 9:00 AM
# CRON_JOB="0 9 * * * $PHP_PATH $CRON_FILE_PATH"

# Cron expression: every 1 minute (for testing)
CRON_JOB="* * * * * $PHP_PATH $CRON_FILE_PATH"

# Add to crontab if not already present
(crontab -l 2>/dev/null; echo "$CRON_JOB") | sort -u | crontab -
# echo "✅ CRON job scheduled to run daily at 9:00 AM"
echo "✅ CRON job scheduled to run every 1 minute (for testing)"

