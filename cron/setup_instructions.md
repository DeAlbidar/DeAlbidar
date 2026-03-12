# Facebook Content Posting Cron Job Setup Guide

## Overview

This cron job automatically generates and posts trending content to your Facebook page every 3 hours from the following categories:

- Tech & AI updates
- Funny videos/memes
- Relationship stories
- Motivational content
- News & trending issues
- Football highlights
- Health tips

## Prerequisites

- PHP CLI installed on your server
- cURL extension enabled in PHP
- MySQL database
- Facebook Page and Access Token

## Installation Steps

### Step 1: Get Facebook Credentials

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create an app or use an existing one
3. Get your **Page Access Token** and **Page ID**
4. Navigate to your app's settings and ensure the following permissions are enabled:
   - `pages_manage_posts`
   - `pages_read_engagement`

### Step 2: Update Configuration

Edit `/var/www/html/DeAlbidar/cron/config.cron.php` and update:

```php
define('FACEBOOK_PAGE_ID', 'YOUR_FACEBOOK_PAGE_ID');
define('FACEBOOK_ACCESS_TOKEN', 'YOUR_FACEBOOK_PAGE_ACCESS_TOKEN');
```

### Step 3: Create Database Tables

Run the installation script to create the required database table:

```bash
php /var/www/html/DeAlbidar/cron/install_database.php
```

This will create the `facebook_posts` table to track posted content.

### Step 4: Set Up Cron Job

Add the following line to your crontab to run the job every 3 hours:

```bash
# Edit crontab
crontab -e

# Add this line (posts every 3 hours starting at midnight)
0 */3 * * * /usr/bin/php /var/www/html/DeAlbidar/cron/post_content.php >> /var/log/facebook_poster.log 2>&1
```

**Cron Schedule Breakdown:**

- `0` = Minute (0)
- `*/3` = Every 3rd hour (0, 3, 6, 9, 12, 15, 18, 21)
- `*` = Every day of month
- `*` = Every month
- `*` = Every day of week

### Step 5: Test the Installation

Run the cron job manually to test:

```bash
php /var/www/html/DeAlbidar/cron/post_content.php
```

You should see output indicating success or any errors.

## File Structure

```
/var/www/html/DeAlbidar/
├── cron/
│   ├── config.cron.php                 # Configuration
│   ├── post_content.php                # Main cron job script
│   ├── install_database.php            # Database setup script
│   ├── setup_instructions.md           # This file
│   └── logs/                           # Daily log files
├── libs/
│   ├── FacebookAPI.php                 # Facebook API wrapper
│   └── ContentGenerator.php            # Content generation logic
```

## How It Works

1. **Content Generation**: The cron job randomly selects one of 7 content categories
2. **Duplication Check**: Verifies the content hasn't been posted in the last 24 hours
3. **Facebook Posting**: Posts to your Facebook page using the Graph API
4. **Database Logging**: Saves post details to track history
5. **Error Handling**: Logs all errors for debugging

## Monitoring and Logs

Check the log files in the cron/logs/ directory:

```bash
tail -f /var/www/html/DeAlbidar/cron/logs/$(date +%Y-%m-%d).log
```

Or check the system log:

```bash
tail -f /var/log/facebook_poster.log
```

## Customization

### Add Custom Content

Edit `libs/ContentGenerator.php` to:

1. Add new categories
2. Customize messages and content
3. Add external API integrations (News APIs, Sports APIs, etc.)

### Change Post Interval

Edit `cron/config.cron.php`:

```php
define('POST_INTERVAL_HOURS', 6); // Change to 6, 12, 24, etc.
```

### Use External APIs

Extend `ContentGenerator.php` to fetch from:

- **Tech News**: NewsAPI, HackerNews API
- **Sports**: ESPN API, SportRadar
- **Motivational Quotes**: Quotable API, Zenquotes API
- **Memes**: Imgflip API, Reddit API

Example integration:

```php
private function getTechAiUpdates() {
    // Fetch from NewsAPI
    $response = file_get_contents('https://newsapi.org/v2/everything?q=AI&apiKey=YOUR_KEY');
    $articles = json_decode($response, true)['articles'];

    // Process and return content...
}
```

## Troubleshooting

### Posts not appearing

- Check Facebook Page Access Token validity
- Verify token has `pages_manage_posts` permission
- Check logs in `/var/www/html/DeAlbidar/cron/logs/`

### Cron job not running

```bash
# Check if cron service is running
sudo service cron status

# View your cron jobs
crontab -l

# Check system cron logs
tail -f /var/log/syslog | grep CRON
```

### Database connection errors

- Verify database credentials in `config.phpcron/config.cron.php`
- Ensure MySQL server is running
- Check user permissions

### PHP CLI issues

```bash
# Verify PHP CLI path
which php

# Check PHP extensions
php -m | grep curl
```

## API Response Handling

The system handles various Facebook API responses:

- **Success**: Post ID is saved to database
- **Error**: Detailed error logged, job continues with next category
- **Rate Limit**: Implement exponential backoff (customize as needed)

## Security Best Practices

1. **Store credentials securely**:

   ```php
   // Better approach: use environment variables
   define('FACEBOOK_ACCESS_TOKEN', getenv('FB_ACCESS_TOKEN'));
   ```

2. **Rotate access tokens regularly**
3. **Use long-lived page access tokens**
4. **Restrict file permissions**:

   ```bash
   chmod 600 /var/www/html/DeAlbidar/cron/config.cron.php
   ```

5. **Monitor for unauthorized access**:
   ```bash
   tail -f /var/www/html/DeAlbidar/cron/logs/*.log
   ```

## Advanced Features

### Database Query Examples

Get all posted content:

```sql
SELECT * FROM facebook_posts ORDER BY posted_date DESC;
```

Get stats by category:

```sql
SELECT category, COUNT(*) as total
FROM facebook_posts
GROUP BY category;
```

Get posts from last 7 days:

```sql
SELECT * FROM facebook_posts
WHERE posted_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY posted_date DESC;
```

### Adding Analytics

Extend the system to track engagement:

```php
$response = $this->facebookAPI->getPostStats($facebookPostId);
// Store engagement metrics (likes, comments, shares)
```

## Support and Updates

For issues or improvements:

1. Check the logs
2. Test manually: `php /var/www/html/DeAlbidar/cron/post_content.php`
3. Update Facebook API version in `FacebookAPI.php` if needed
4. Keep PHP and dependencies updated

---

**Created**: March 2026
**Version**: 1.0
**Framework**: Custom PHP MVC
