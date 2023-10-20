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

use ArrayObject;
use Dotclear\App;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     *
     * @return     string
     */
    public static function SitemapEntries(array|ArrayObject $attr, string $content): string
    {
        return
            '<?php if (App::frontend()->context()->exists("sitemap_urls")) : ?>' . "\n" .
            '<?php while (App::frontend()->context()->sitemap_urls->fetch()) : ?>' . $content . '<?php endwhile; ?>' .
            '<?php endif; ?>' . "\n";
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     *
     * @return     string
     */
    public static function SitemapEntryIf(array|ArrayObject $attr, string $content): string
    {
        $if = '';
        if (isset($attr['has_attr'])) {
            switch ($attr['has_attr']) {
                case 'frequency':
                    $if = '!is_null(App::frontend()->context()->sitemap_urls->frequency)';

                    break;
                case 'priority':
                    $if = '!is_null(App::frontend()->context()->sitemap_urls->priority)';

                    break;
                case 'lastmod':
                    $if = '!is_null(App::frontend()->context()->sitemap_urls->lastmod)';

                    break;
            }
        }
        if (!empty($if)) {
            return '<?php if (' . $if . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     *
     * @return     string
     */
    public static function SitemapEntryLoc(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->sitemap_urls->loc') . '; ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     *
     * @return     string
     */
    public static function SitemapEntryFrequency(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->sitemap_urls->frequency') . '; ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     *
     * @return     string
     */
    public static function SitemapEntryPriority(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->sitemap_urls->priority') . '; ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     *
     * @return     string
     */
    public static function SitemapEntryLastmod(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'App::frontend()->context()->sitemap_urls->lastmod') . '; ?>';
    }
}
