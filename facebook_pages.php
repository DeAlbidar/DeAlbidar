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
            'access_token' => 'EAANU5a3nV4MBQxt8FuJFhC5qizrgk5wfjhsWTxsV5ZCSb6UOUClAZBERHLRkbEXgHnLWaarC1hfwscAxjZBmMHwof2mAVxZBFfHVSGdheaRDjB9voRxC4ubZBQv4uz1TUEepcsVRckRZC6U7i3253D3siIhTHUE4SfECyCSQTggzdiiRWUvJ7v3C8W19u5CKdoVCpiu8s0V4rdBkfpJWo0tTvKzCX29wfkJ9bQv8QhgwZDZD',
            'default_category' => 'tech_ai',
            'posts_per_day' => 1
        ],
        'ebenezer_albidar_narh' => [
            'label' => 'Ebenezer Albidar Narh',
            'page_id' => '101849324975567',
            'access_token' => 'EAANU5a3nV4MBQ27ZAWQ2rZCgBqP6TQZBTw9uJ9BcYoP789bcn9ZBNgZBOBx3AnJvm1BlYL4UygqXnd1atSgZCbkQyOLQwG6GTSXbUW8xZCEP8cVkPoM5ZCCwXFRYlJYPMXHpWBaG7qxxuYrapIP5ORZCfmOXFaHTYUe43i5fz03IukmIPMr1EjQQ7EZCwgk5XVpEEzZAgGIBuDpYqvcKEF5AaG6xlZCavXh28a1pNbkoUoDSKgZDZD',
            'default_category' => 'tech_ai',
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
