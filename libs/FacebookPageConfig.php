<?php

if (!function_exists('getFacebookPageTargets')) {
    /**
     * Return all configured Facebook page targets.
     *
     * @return array<string, array<string, mixed>>
     */
    function getFacebookPageTargets() {
        if (defined('FACEBOOK_PAGE_TARGETS') && is_array(FACEBOOK_PAGE_TARGETS)) {
            return FACEBOOK_PAGE_TARGETS;
        }

        if (defined('FACEBOOK_PAGE_ID') && defined('FACEBOOK_ACCESS_TOKEN')) {
            return [
                'default' => [
                    'label' => 'Default Page',
                    'page_id' => FACEBOOK_PAGE_ID,
                    'access_token' => FACEBOOK_ACCESS_TOKEN,
                    'default_category' => 'tech_ai',
                    'posts_per_day' => 1
                ]
            ];
        }

        return [];
    }

    /**
     * Get the configured default page key.
     */
    function getDefaultFacebookPageKey() {
        if (defined('FACEBOOK_DEFAULT_PAGE_KEY')) {
            return FACEBOOK_DEFAULT_PAGE_KEY;
        }

        foreach (getFacebookPageTargets() as $pageKey => $config) {
            return $pageKey;
        }

        return 'default';
    }

    /**
     * Resolve a single Facebook page config by key.
     *
     * @param string|null $pageKey
     * @return array<string, mixed>
     */
    function getFacebookPageConfig($pageKey = null) {
        $targets = getFacebookPageTargets();

        if (empty($targets)) {
            throw new Exception('Facebook page configuration is missing.');
        }

        $resolvedKey = $pageKey ?: getDefaultFacebookPageKey();

        if (!isset($targets[$resolvedKey])) {
            throw new InvalidArgumentException(
                'Unknown Facebook page key: ' . $resolvedKey
            );
        }

        $config = $targets[$resolvedKey];
        $config['key'] = $resolvedKey;
        $config['label'] = $config['label'] ?? $resolvedKey;
        $config['default_category'] = $config['default_category'] ?? null;
        $config['posts_per_day'] = (int) ($config['posts_per_day'] ?? 1);

        return $config;
    }
}

?>
