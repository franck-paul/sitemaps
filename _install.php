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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$package_version   = $core->plugins->moduleInfo('sitemaps', 'version');
$installed_version = $core->getVersion('sitemaps');

if (version_compare($installed_version, $package_version, '>=')) {
    return;
}

try {
    // Default settings
    $core->blog->settings->addNameSpace('sitemaps');
    $core->blog->settings->sitemaps->put('sitemaps_active', false, 'boolean', 'Sitemaps activation', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_home_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_home_pr', 1, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_home_fq', 3, 'integer', '', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_feeds_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_feeds_pr', 1, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_feeds_fq', 2, 'integer', '', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_posts_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_posts_pr', 1, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_posts_fq', 3, 'integer', '', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_pages_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_pages_pr', 1, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_pages_fq', 0, 'integer', '', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_cats_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_cats_pr', 0.6, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_cats_fq', 4, 'integer', '', false, true);

    $core->blog->settings->sitemaps->put('sitemaps_tags_url', true, 'boolean', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_tags_pr', 0.6, 'double', '', false, true);
    $core->blog->settings->sitemaps->put('sitemaps_tags_fq', 4, 'integer', '', false, true);

    // Search engines notification
    // Services endpoints
    $search_engines = array(
        'google' => array(
            'name' => 'Google',
            'url'  => 'http://www.google.com/webmasters/tools/ping'
        ),
        'bing'   => array(
            'name' => 'MS Bing',
            'url'  => 'http://www.bing.com/webmaster/ping.aspx'
        )
    );
    $core->blog->settings->sitemaps->put('sitemaps_engines', @serialize($search_engines), 'string', '', true, true);

    // Preferences
    $core->blog->settings->sitemaps->put('sitemaps_pings', 'google', 'string', '', false, true);

    // Remove yahoo and mslive search engines from current blog settings
    $pings   = explode(',', $core->blog->settings->sitemaps->sitemaps_pings);
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
        $core->blog->settings->sitemaps->put('sitemaps_pings', implode(',', $pings), 'string', '', true, false);
    }

    $core->setVersion('sitemaps', $package_version);
    unset($package_version, $installed_version);
    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
    unset($package_version, $installed_version);
    return false;
}
