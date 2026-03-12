#!/bin/bash
# Facebook Poster Cron Job
# 
# This script posts trending content to Facebook every 3 hours
# 
# Setup:
# 1. Make this file executable: chmod +x /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
# 2. Add to crontab:
#    0 */3 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh >> /var/log/facebook_poster.log 2>&1
#

WEBSITE_URL="https://dealbidar.com/?url=facebookposter/post"
LOG_FILE="/var/log/facebook_poster.log"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

echo "[$TIMESTAMP] Starting Facebook content posting..." >> "$LOG_FILE"

# Make request to the controller
RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" "$WEBSITE_URL")
HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE:" | cut -d: -f2)

if [ "$HTTP_CODE" = "200" ]; then
    echo "[$TIMESTAMP] SUCCESS - Content posted" >> "$LOG_FILE"
    echo "$RESPONSE" | head -n -1 >> "$LOG_FILE"
else
    echo "[$TIMESTAMP] ERROR - HTTP Code: $HTTP_CODE" >> "$LOG_FILE"
    echo "$RESPONSE" >> "$LOG_FILE"
fi

echo "" >> "$LOG_FILE"
