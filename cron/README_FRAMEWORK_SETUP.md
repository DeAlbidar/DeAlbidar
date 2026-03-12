# Facebook Cron Job Setup - Framework Integrated

This cron job system integrates with the DeAlbidar framework using the controller-model pattern.

## Architecture

The system uses:

- **Controller**: `controllers/facebookposter.php` - Handles HTTP requests
- **Model**: `models/facebookposter_model.php` - Contains business logic
- **Cron Script**: `cron/facebook_poster_cron.sh` - Calls the controller via HTTP

## Setup Instructions

### Step 1: Run Setup Script

```bash
php /var/www/html/DeAlbidar/cron/setup.php
```

This will:

- Create the `logs/facebook_posts/` directory
- Create the `facebook_posts` database table
- Verify Facebook credentials

### Step 2: Make the Cron Script Executable

```bash
chmod +x /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

### Step 3: Add to Crontab

Edit your crontab:

```bash
crontab -e
```

Add this line to post every 3 hours:

```
0 */3 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh >> /var/log/facebook_poster.log 2>&1
```

**Cron Schedule Explanation:**

- `0` = At minute 0
- `*/3` = Every 3 hours (0:00, 3:00, 6:00, 9:00, 12:00, 15:00, 18:00, 21:00)
- `*` = Every day of month
- `*` = Every month
- `*` = Every day of week

### Step 4: Test the Cron Job

Test manually:

```bash
/var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

Or via direct URL:

```bash
curl "https://dealbidar.com/?url=facebookposter/post"
```

## Controller Methods

### POST Content (Main Job)

**Endpoint:** `?url=facebookposter/post`

Posts new trending content to Facebook.

```bash
curl "https://dealbidar.com/?url=facebookposter/post"
```

Response:

```json
{
  "status": "success",
  "message": "Content posted successfully",
  "post_id": "123456789",
  "category": "tech_ai",
  "timestamp": "2026-03-12 18:56:00"
}
```

### Get Last Posted

**Endpoint:** `?url=facebookposter/last`

Gets the last posted content.

```bash
curl "https://dealbidar.com/?url=facebookposter/last"
```

### Get Status

**Endpoint:** `?url=facebookposter/status`

Gets posting statistics by category.

```bash
curl "https://dealbidar.com/?url=facebookposter/status"
```

### Test Content

**Endpoint:** `?url=facebookposter/test`

Generates content without posting (for testing).

```bash
curl "https://dealbidar.com/?url=facebookposter/test"
```

## File Structure

```
/var/www/html/DeAlbidar/
├── config.php                          # Main config (includes FB credentials)
├── controllers/
│   └── facebookposter.php              # Controller with HTTP handlers
├── models/
│   └── facebookposter_model.php        # Business logic
├── libs/
│   ├── FacebookAPI.php                 # Facebook API wrapper
│   ├── ContentGenerator.php            # Content generation
│   └── Bootstrap.php                   # Framework router
├── cron/
│   ├── setup.php                       # Setup script
│   ├── facebook_poster_cron.sh          # Shell script (called by cron)
│   └── logs/                           # Log output
│       └── facebook_posts/
│           ├── 2026-03-12.log
│           ├── 2026-03-13.log
│           └── ...
└── logs/
    └── facebook_posts/                 # Daily logs
```

## Monitoring

### View Cron Logs

```bash
tail -f /var/log/facebook_poster.log
```

### View Application Logs

```bash
tail -f /var/www/html/DeAlbidar/logs/facebook_posts/$(date +%Y-%m-%d).log
```

### Check Crontab

```bash
crontab -l
```

### Verify Cron Execution

```bash
grep CRON /var/log/syslog | tail -20
```

## Content Categories

The cron job automatically rotates through these categories:

1. **Tech & AI Updates** (`tech_ai`)
2. **Funny Videos/Memes** (`funny_memes`)
3. **Relationship Stories** (`relationship_stories`)
4. **Motivational Content** (`motivational_content`)
5. **News & Trending Issues** (`news_trending`)
6. **Football Highlights** (`football_highlights`)
7. **Health Tips** (`health_tips`)

## Database

The system automatically saves all posted content to the `facebook_posts` table:

```sql
SELECT * FROM facebook_posts ORDER BY posted_date DESC LIMIT 10;
```

View posts by category:

```sql
SELECT category, COUNT(*) as total FROM facebook_posts GROUP BY category;
```

## Troubleshooting

### Cron job not running

1. Check if cron service is running:

   ```bash
   sudo service cron status
   ```

2. Check crontab syntax:

   ```bash
   crontab -l
   ```

3. Check system logs:
   ```bash
   grep CRON /var/log/syslog | tail -20
   ```

### Posts not appearing on Facebook

1. Verify Facebook credentials in `config.php`
2. Test the controller endpoint:
   ```bash
   curl "https://dealbidar.com/?url=facebookposter/test"
   ```
3. Check application logs:
   ```bash
   tail -f /var/www/html/DeAlbidar/logs/facebook_posts/$(date +%Y-%m-%d).log
   ```

### Permission issues

Make the script executable:

```bash
chmod +x /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

Ensure the web server can write to logs:

```bash
chmod -R 755 /var/www/html/DeAlbidar/logs/
chown -R www-data:www-data /var/www/html/DeAlbidar/logs/
```

## Advanced Customization

### Change Post Interval

Edit crontab and change the interval:

**Every hour:**

```
0 * * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

**Every 6 hours:**

```
0 */6 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

**Specific times (8am, 12pm, 4pm, 8pm):**

```
0 8,12,16,20 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh
```

### Customize Content

Edit `libs/ContentGenerator.php` to:

- Add custom messages
- Integrate external APIs (NewsAPI, Reddit, etc.)
- Modify image sources
- Add new categories

### Monitor with Email Alerts

Add to crontab to get email on errors:

```bash
0 */3 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh || mail -s "Facebook Cron Job Failed" admin@dealbidar.com
```

## Security Notes

1. Keep Facebook token secure - never expose in logs
2. Use environment variables for sensitive data
3. Restrict access to admin-only endpoints if needed
4. Monitor logs regularly for errors
5. Keep backup of database with `facebook_posts` table

## Support & Issues

For issues, check:

1. Application logs: `/var/www/html/DeAlbidar/logs/facebook_posts/`
2. System logs: `/var/log/facebook_poster.log`
3. Cron logs: `/var/log/syslog`
4. Facebook Graph API errors (in application logs)

---

**Created:** March 2026  
**Framework:** DeAlbidar MVC  
**Status:** Production Ready ✓
