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
if (!defined('DC_RC_PATH')) {
    return;
}

$__autoload['dcSitemaps'] = __DIR__ . '/inc/class.dc.sitemaps.php';

// Behavior(s)
class sitemapsBehaviors
{
    public static function addTemplatePath()
    {
        dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/default-templates');
    }
}

dcCore::app()->addBehavior('publicBeforeDocument', ['sitemapsBehaviors', 'addTemplatePath']);

// URL Handler(s)
class sitemapsUrlHandlers extends dcUrlHandlers
{
    public static function sitemap()
    {
        if (!dcCore::app()->blog->settings->sitemaps->sitemaps_active) {
            self::p404();

            return;
        }

        $sitemap                         = new dcSitemaps();
        dcCore::app()->ctx->sitemap_urls = staticRecord::newFromArray($sitemap->getURLs());
        if (dcCore::app()->ctx->sitemap_urls->isEmpty()) {
            self::p404();
        } else {
            http::$cache_max_age = 60 * 60; // 1 hour cache for feed
            self::serveDocument('sitemap.xml', 'text/xml');
        }
    }
}

dcCore::app()->url->register('gsitemap', 'sitemap.xml', '^sitemap[_\.]xml$', ['sitemapsUrlHandlers', 'sitemap']);
