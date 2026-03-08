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
        if (App::frontend()->context()->sitemap_urls instanceof \Dotclear\Database\MetaRecord) :
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
        $sitemaps_loc = App::frontend()->context()->sitemap_urls instanceof \Dotclear\Database\MetaRecord && is_string($sitemaps_loc = App::frontend()->context()->sitemap_urls->loc) ? $sitemaps_loc : '';
        echo App::frontend()->context()::global_filters(
            $sitemaps_loc,
            $_params_,
            $_tag_
        );
        unset($sitemaps_loc);
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
        $sitemaps_fq = App::frontend()->context()->sitemap_urls instanceof \Dotclear\Database\MetaRecord && is_string($sitemaps_fq = App::frontend()->context()->sitemap_urls->frequency) ? $sitemaps_fq : '';
        echo App::frontend()->context()::global_filters(
            $sitemaps_fq,
            $_params_,
            $_tag_
        );
        unset($sitemaps_fq);
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
        $sitemaps_pr = App::frontend()->context()->sitemap_urls instanceof \Dotclear\Database\MetaRecord && is_string($sitemaps_pr = App::frontend()->context()->sitemap_urls->priority) ? $sitemaps_pr : '';
        echo App::frontend()->context()::global_filters(
            $sitemaps_pr,
            $_params_,
            $_tag_
        );
        unset($sitemaps_pr);
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
        $sitemaps_lastmod = App::frontend()->context()->sitemap_urls instanceof \Dotclear\Database\MetaRecord && is_string($sitemaps_lastmod = App::frontend()->context()->sitemap_urls->lastmod) ? $sitemaps_lastmod : '';
        echo App::frontend()->context()::global_filters(
            $sitemaps_lastmod,
            $_params_,
            $_tag_
        );
        unset($sitemaps_lastmod);
    }
}
