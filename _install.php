<?php
/**
 * @brief socialMeta, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Pep
 *
 * @copyright Pep
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    // Default settings
    dcCore::app()->blog->settings->addNameSpace('sitemaps');
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_active', false, 'boolean', 'Sitemaps activation', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_home_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_home_pr', 1, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_home_fq', 3, 'integer', '', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_feeds_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_feeds_pr', 1, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_feeds_fq', 2, 'integer', '', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_posts_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_posts_pr', 1, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_posts_fq', 3, 'integer', '', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_pages_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_pages_pr', 1, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_pages_fq', 0, 'integer', '', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_cats_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_cats_pr', 0.6, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_cats_fq', 4, 'integer', '', false, true);

    dcCore::app()->blog->settings->sitemaps->put('sitemaps_tags_url', true, 'boolean', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_tags_pr', 0.6, 'double', '', false, true);
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_tags_fq', 4, 'integer', '', false, true);

    // Search engines notification
    // Services endpoints
    $search_engines = [
        'google' => [
            'name' => 'Google',
            'url'  => 'http://www.google.com/webmasters/tools/ping',
        ],
        'bing' => [
            'name' => 'MS Bing',
            'url'  => 'http://www.bing.com/webmaster/ping.aspx',
        ],
    ];
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_engines', @serialize($search_engines), 'string', '', true, true);

    // Preferences
    dcCore::app()->blog->settings->sitemaps->put('sitemaps_pings', 'google', 'string', '', false, true);

    // Remove yahoo and mslive search engines from current blog settings
    $pings   = explode(',', dcCore::app()->blog->settings->sitemaps->sitemaps_pings);
    $removed = false;
    if ($k = array_search('yahoo', $pings)) {
        unset($pings[$k]);
        $removed = true;
    }
    if ($k = array_search('mslive', $pings)) {
        unset($pings[$k]);
        $removed = true;
    }
    if ($removed) {
        dcCore::app()->blog->settings->sitemaps->put('sitemaps_pings', implode(',', $pings), 'string', '', true, false);
    }

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());

    return false;
}
