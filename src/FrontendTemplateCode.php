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

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:SitemapEntries block
     */
    public static function SitemapEntries(
        string $_content_HTML,
    ): void {
        if (App::frontend()->context()->exists('sitemap_urls')) :
            while (App::frontend()->context()->sitemap_urls->fetch()) : ?>
                $_content_HTML
            <?php endwhile;
        endif;
    }

    /**
     * PHP code for tpl:SitemapEntryIf block
     */
    public static function SitemapEntryIf(
        string $_has_HTML,
        string $_content_HTML,
    ): void {
        if (!is_null(App::frontend()->context()->sitemap_urls->$_has_HTML)) : ?>
            $_content_HTML
        <?php endif;
    }

    /**
     * PHP code for tpl:SitemapEntryLoc value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function SitemapEntryLoc(
        array $_params_,
        string $_tag_,
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->sitemap_urls->loc,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:SitemapEntryFrequency value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function SitemapEntryFrequency(
        array $_params_,
        string $_tag_,
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->sitemap_urls->frequency,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:SitemapEntryPriority value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function SitemapEntryPriority(
        array $_params_,
        string $_tag_,
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->sitemap_urls->priority,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:SitemapEntryLastmod value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function SitemapEntryLastmod(
        array $_params_,
        string $_tag_,
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->sitemap_urls->lastmod,
            $_params_,
            $_tag_
        );
    }
}
