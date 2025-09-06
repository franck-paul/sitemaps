<?php

/**
 * @brief sitemaps, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\sitemaps;

use Dotclear\App;
use Dotclear\Core\Url;
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Network\Http;

// URL Handler(s)
class FrontendUrl extends Url
{
    public static function sitemap(): void
    {
        $settings = My::settings();
        if (!$settings->active) {
            self::p404();
        }

        $sitemap = new Sitemap();

        App::frontend()->context()->sitemap_urls = MetaRecord::newFromArray($sitemap->getURLs());
        if (App::frontend()->context()->sitemap_urls->isEmpty()) {
            self::p404();
        } else {
            Http::$cache_max_age = 60 * 60; // 1 hour cache for feed
            self::serveDocument('sitemap.xml', 'text/xml');
        }
    }
}
