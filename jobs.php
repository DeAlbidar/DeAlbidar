<?php

require_once __DIR__ . '/facebook_pages.php';

$baseUrl = 'https://www.dealbidar.com/?url=facebookposter/post';
$requestedPage = $_GET['page'] ?? null;
$requestedCategory = $_GET['category'] ?? null;

function runFacebookPostRequest($baseUrl, $pageKey, $category = null) {
    $requestUrl = $baseUrl . '&page=' . urlencode($pageKey);

    if ($category !== null && $category !== '') {
        $requestUrl .= '&category=' . urlencode($category);
    }

    $response = @file_get_contents($requestUrl);

    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'Failed to connect to posting endpoint',
            'page_key' => $pageKey
        ];
    }

    $decoded = json_decode($response, true);

    if (is_array($decoded)) {
        $decoded['page_key'] = $decoded['page_key'] ?? $pageKey;
        return $decoded;
    }

    return [
        'status' => 'error',
        'message' => 'Invalid JSON response from posting endpoint',
        'page_key' => $pageKey,
        'raw_response' => $response
    ];
}

if ($requestedPage) {
    echo json_encode(runFacebookPostRequest($baseUrl, $requestedPage, $requestedCategory));
    exit;
}

$results = [];
foreach (array_keys(getFacebookPageTargets()) as $pageKey) {
    $results[] = runFacebookPostRequest($baseUrl, $pageKey, $requestedCategory);
}

echo json_encode([
    'status' => 'success',
    'message' => 'Processed all configured Facebook pages',
    'total_pages' => count($results),
    'results' => $results,
    'timestamp' => date('Y-m-d H:i:s')
]);

?>
