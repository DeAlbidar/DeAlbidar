<?php
/**
 * Facebook Graph API Handler
 * Handles posting content to Facebook
 */

class FacebookAPI {
    private $accessToken;
    private $pageId;
    private $apiVersion = 'v18.0';
    private $baseUrl = 'https://graph.facebook.com';

    public function __construct($accessToken, $pageId) {
        $this->accessToken = trim($accessToken);
        $this->pageId = trim($pageId);
    }

    /**
     * Post content to Facebook
     * @param string $message - The message to post
     * @param string $link - Optional link to include
     * @param string $picture - Optional picture URL (ignored for external links)
     * @return array - Response from Facebook API
     */
    public function postContent($message, $link = null, $picture = null) {
        $endpoint = "{$this->baseUrl}/{$this->apiVersion}/{$this->pageId}/feed";
        
        $postData = [
            'message' => $message,
            'access_token' => $this->accessToken
        ];

        // Only add link if provided - Facebook will auto-generate preview
        // Note: picture/thumbnail params can only be used for URLs we own
        if ($link) {
            $postData['link'] = $link;
        }

        $response = $this->makeRequest('POST', $endpoint, $postData);
        return $response;
    }

    /**
     * Post a video to Facebook
     * @param string $videoUrl - URL of the video
     * @param string $title - Video title
     * @param string $description - Video description
     * @return array - Response from Facebook API
     */
    public function postVideo($videoUrl, $title, $description) {
        $endpoint = "{$this->baseUrl}/{$this->apiVersion}/{$this->pageId}/feed";
        
        $postData = [
            'message' => $title . "\n\n" . $description,
            'source' => $videoUrl,
            'type' => 'video',
            'access_token' => $this->accessToken
        ];

        $response = $this->makeRequest('POST', $endpoint, $postData);
        return $response;
    }

    /**
     * Post with image
     * @param string $imageUrl - URL of the image
     * @param string $caption - Caption for the image
     * @param string $link - Optional link
     * @return array - Response from Facebook API
     */
    public function postImage($imageUrl, $caption, $link = null) {
        $endpoint = "{$this->baseUrl}/{$this->apiVersion}/{$this->pageId}/photos";
        
        $postData = [
            'url' => $imageUrl,
            'caption' => $caption,
            'access_token' => $this->accessToken
        ];

        if ($link) {
            $postData['description'] = $link;
        }

        $response = $this->makeRequest('POST', $endpoint, $postData);
        return $response;
    }

    /**
     * Make HTTP request to Facebook API
     * @param string $method - HTTP method (GET, POST, etc)
     * @param string $url - API endpoint URL
     * @param array $data - Data to send
     * @return array - Response from API
     */
    private function makeRequest($method, $url, $data = []) {
        // Try cURL first, then fall back to streams
        return $this->makeRequestWithCurl($method, $url, $data) 
            ?? $this->makeRequestWithStreams($method, $url, $data);
    }

    /**
     * Make HTTP request using cURL
     */
    private function makeRequestWithCurl($method, $url, $data = []) {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($ch, CURLOPT_URL, $method === 'POST' ? $url : ($url . '?' . http_build_query($data)));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrorNo = curl_errno($ch);
        curl_close($ch);

        if ($curlErrorNo !== 0) {
            return null; // Fall back to streams
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        return [
            'error' => true,
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    /**
     * Make HTTP request using PHP streams
     */
    private function makeRequestWithStreams($method, $url, $data = []) {
        $context_options = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ],
            'http' => [
                'method' => $method,
                'timeout' => 30
            ]
        ];

        if ($method === 'POST') {
            $context_options['http']['header'] = "Content-Type: application/x-www-form-urlencoded\r\n";
            $context_options['http']['content'] = http_build_query($data);
            $request_url = $url;
        } else {
            $request_url = $url . '?' . http_build_query($data);
        }

        $context = stream_context_create($context_options);
        
        try {
            $response = @file_get_contents($request_url, false, $context);
            
            if ($response === false) {
                // Check for HTTP errors in stream metadata
                $metadata = stream_get_meta_data(GLOBALS['http_response_header'] ?? []);
                return [
                    'error' => true,
                    'http_code' => 0,
                    'response' => ['message' => 'Failed to connect to Facebook API']
                ];
            }

            $decoded = json_decode($response, true);
            return $decoded ? $decoded : ['error' => false, 'raw' => $response];
            
        } catch (Exception $e) {
            return [
                'error' => true,
                'http_code' => 0,
                'response' => ['message' => $e->getMessage()]
            ];
        }
    }

    /**
     * Set access token (useful for refreshing)
     * @param string $token - New access token
     */
    public function setAccessToken($token) {
        $this->accessToken = trim($token);
    }
}
?>
