<?php
/**
 * Refresh Facebook page tokens from a short-lived user token.
 *
 * Usage:
 * php /var/www/html/DeAlbidar/cron/refresh_facebook_tokens.php SHORT_USER_TOKEN [--write]
 * php /var/www/html/DeAlbidar/cron/refresh_facebook_tokens.php APP_ID APP_SECRET SHORT_USER_TOKEN [--write]
 *
 * Notes:
 * - This exchanges a short-lived USER token for a long-lived USER token.
 * - It then fetches page tokens for the pages the user manages.
 * - With --write, matching configured page tokens in facebook_pages.php are updated.
 */

date_default_timezone_set('UTC');

require_once dirname(__DIR__) . '/facebook_pages.php';

class FacebookTokenRefresher {
    private $appId;
    private $appSecret;
    private $shortUserToken;
    private $graphBaseUrl = 'https://graph.facebook.com/v25.0';

    public function __construct($appId, $appSecret, $shortUserToken) {
        $this->appId = trim($appId);
        $this->appSecret = trim($appSecret);
        $this->shortUserToken = trim($shortUserToken);
    }

    public function refresh() {
        $userTokenResponse = $this->exchangeForLongLivedUserToken();
        $longLivedUserToken = $userTokenResponse['access_token'];
        $pages = $this->fetchManagedPages($longLivedUserToken);

        return [
            'generated_at' => date('Y-m-d H:i:s'),
            'long_lived_user_token' => $longLivedUserToken,
            'user_token_expires_in' => $userTokenResponse['expires_in'] ?? null,
            'pages' => $pages
        ];
    }

    private function exchangeForLongLivedUserToken() {
        $query = http_build_query([
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'grant_type' => 'fb_exchange_token',
            'fb_exchange_token' => $this->shortUserToken
        ]);

        $response = $this->request('GET', $this->graphBaseUrl . '/oauth/access_token?' . $query);

        if (!isset($response['access_token'])) {
            throw new RuntimeException('Could not get long-lived user token.');
        }

        return $response;
    }

    private function fetchManagedPages($userToken) {
        $query = http_build_query([
            'fields' => 'id,name,access_token',
            'access_token' => $userToken
        ]);

        $response = $this->request('GET', $this->graphBaseUrl . '/me/accounts?' . $query);

        if (!isset($response['data']) || !is_array($response['data'])) {
            throw new RuntimeException('Could not fetch managed pages.');
        }

        return $response['data'];
    }

    private function request($method, $url) {
        if (!function_exists('curl_init')) {
            $response = @file_get_contents($url);
            if ($response === false) {
                throw new RuntimeException('Request failed: ' . $url);
            }

            $decoded = json_decode($response, true);
            if (isset($decoded['error'])) {
                throw new RuntimeException($decoded['error']['message'] ?? 'Facebook API error');
            }

            return $decoded;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($rawResponse === false) {
            throw new RuntimeException('cURL request failed: ' . $curlError);
        }

        $decoded = json_decode($rawResponse, true);

        if ($httpCode >= 400 || isset($decoded['error'])) {
            $message = $decoded['error']['message'] ?? ('Facebook API HTTP ' . $httpCode);
            throw new RuntimeException($message);
        }

        return $decoded;
    }
}

function updateConfiguredPageTokens($pages) {
    $configFile = dirname(__DIR__) . '/facebook_pages.php';
    $contents = file_get_contents($configFile);

    if ($contents === false) {
        throw new RuntimeException('Could not read facebook_pages.php');
    }

    foreach (getFacebookPageTargets() as $pageKey => $config) {
        foreach ($pages as $page) {
            if (($page['id'] ?? '') !== ($config['page_id'] ?? '')) {
                continue;
            }

            $currentToken = $config['access_token'] ?? '';
            $newToken = $page['access_token'] ?? '';

            if ($currentToken && $newToken) {
                $contents = str_replace("'" . $currentToken . "'", "'" . $newToken . "'", $contents);
            }
        }
    }

    if (file_put_contents($configFile, $contents) === false) {
        throw new RuntimeException('Could not write updated tokens to facebook_pages.php');
    }
}

function printUsageAndExit() {
    echo "Usage:\n";
    echo "  php /var/www/html/DeAlbidar/cron/refresh_facebook_tokens.php SHORT_USER_TOKEN [--write]\n";
    echo "  php /var/www/html/DeAlbidar/cron/refresh_facebook_tokens.php APP_ID APP_SECRET SHORT_USER_TOKEN [--write]\n";
    exit(1);
}

$shouldWrite = in_array('--write', $argv, true);
$args = array_values(array_filter(array_slice($argv, 1), function ($arg) {
    return $arg !== '--write';
}));

$configuredAppId = defined('FACEBOOK_APP_ID') ? trim(FACEBOOK_APP_ID) : '';
$configuredAppSecret = defined('FACEBOOK_APP_SECRET') ? trim(FACEBOOK_APP_SECRET) : '';

$appId = $configuredAppId;
$appSecret = $configuredAppSecret;
$shortUserToken = null;

if (count($args) === 1) {
    $shortUserToken = $args[0];
} elseif (count($args) >= 3) {
    $appId = $args[0];
    $appSecret = $args[1];
    $shortUserToken = $args[2];
}

if (!$appId || !$appSecret || !$shortUserToken) {
    printUsageAndExit();
}

try {
    $refresher = new FacebookTokenRefresher($appId, $appSecret, $shortUserToken);
    $result = $refresher->refresh();

    if ($shouldWrite) {
        updateConfiguredPageTokens($result['pages']);
        $result['updated_config'] = true;
    }

    echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT) . PHP_EOL;
    exit(1);
}

?>
