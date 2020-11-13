<?php

$addonJson = json_decode(file_get_contents(__DIR__ . '/addon.json'));


if (!defined('MX_MOBILE_DETECT_NAME')) {
    define('MX_MOBILE_DETECT_NAME', $addonJson->name);
    define('MX_MOBILE_DETECT_VERSION', $addonJson->version);
    define('MX_MOBILE_DETECT_DOCS', '');
    define('MX_MOBILE_DETECT_DESCRIPTION', $addonJson->description);
    define('MX_MOBILE_DETECT_AUTHOR', 'Max Lazar');
    define('MX_MOBILE_DETECT_DEBUG', false);
}

//$config['MX_MOBILE_DETECT_tab_title'] = MX_MOBILE_DETECT_NAME;

return [
    'name' => $addonJson->name,
    'description' => $addonJson->description,
    'version' => $addonJson->version,
    'namespace' => $addonJson->namespace,
    'author' => 'Max Lazar',
    'author_url' => 'https://eecms.dev',
    'settings_exist' => false,
    // Advanced settings
    'services' => [],
];
