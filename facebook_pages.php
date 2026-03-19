<?php

require_once __DIR__ . '/libs/FacebookPageConfig.php';

if (!defined('FACEBOOK_DEFAULT_PAGE_KEY')) {
    define('FACEBOOK_DEFAULT_PAGE_KEY', 'innink_limited');
}

if (!defined('FACEBOOK_PAGE_TARGETS')) {
    define('FACEBOOK_PAGE_TARGETS', [
        'innink_limited' => [
            'label' => 'InnInk Limited',
            'page_id' => '101457405937851',
            'access_token' => 'EAANU5a3nV4MBQ9bZCZA1d1n4tqekA0qZAZCWWbirTGXwwCjSoxeTPWZA0ZBMjw8f9QnYeC7AYRcnURVEQRRgiSdgghAFTN7YeWvqOK8byT6vIU2YK636HJ3h4jHKZBbif5Wws6cSMqjLuzNDTat7dkj0nFCQQmjF5QSqYeD5i4iMAEFyuEZCBBsvyVsO2KPOjLAk0GhUDRZAV4XtC5Cauj7ZBO3ZBlaeB4aTT0sUB8kSlDgJAZDZD',
            'default_category' => 'tech_ai',
            'posts_per_day' => 1
        ],
        'ebenezer_albidar_narh' => [
            'label' => 'Ebenezer Albidar Narh',
            'page_id' => '101849324975567',
            'access_token' => 'EAANU5a3nV4MBQw9I7qRV4AutHsAdHo2rIuRuxAKUiw2JgF7b0RIaX41LVZAP54zCR5XNaEKooEXafrlkmwiSjoAoJ9ho8bBLUidlXZBXRqR6AHl21ZC1ZCTZC36OfPO5GRyZB5r0sklaiGI97OdE71qQkDXHPyW0BaFeZCE3ZAabZC4Hi10HJF74YpkEP6g4ZCs8hSExN0Jb5FTgc2TD4YugFMFw30hOPWoxojYRYgLh5XZCAZDZD',
            'default_category' => [
                'tech_ai',
                'motivational_content',
                'health_tips',
                'football_highlights',
                'funny_memes'
            ],
            'posts_per_day' => 1
        ]
    ]);
}

$defaultFacebookPage = getFacebookPageConfig(FACEBOOK_DEFAULT_PAGE_KEY);

if (!defined('FACEBOOK_PAGE_ID')) {
    define('FACEBOOK_PAGE_ID', $defaultFacebookPage['page_id']);
}

if (!defined('FACEBOOK_ACCESS_TOKEN')) {
    define('FACEBOOK_ACCESS_TOKEN', $defaultFacebookPage['access_token']);
}

?>
