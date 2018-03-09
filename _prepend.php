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

if (!defined('DC_RC_PATH')) {return;}

global $core, $__autoload;

$__autoload['dcSitemaps'] = dirname(__FILE__) . '/inc/class.dc.sitemaps.php';

// Behavior(s)
class sitemapsBehaviors
{
    public static function addTemplatePath($core)
    {
        $core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__) . '/default-templates');
    }

}

$core->addBehavior('publicBeforeDocument', array('sitemapsBehaviors', 'addTemplatePath'));

// URL Handler(s)
class sitemapsUrlHandlers extends dcUrlHandlers
{
    public static function sitemap($args)
    {
        global $core, $_ctx;

        if (!$core->blog->settings->sitemaps->sitemaps_active) {
            self::p404();
            return;
        }

        $sitemap            = new dcSitemaps($core);
        $_ctx->sitemap_urls = staticRecord::newFromArray($sitemap->getURLs());
        if ($_ctx->sitemap_urls->isEmpty()) {
            self::p404();
        } else {
            self::serveDocument('sitemap.xml', 'text/xml');
        }
    }
}

$core->url->register('gsitemap', 'sitemap.xml', '^sitemap[_\.]xml$', array('sitemapsUrlHandlers', 'sitemap'));
