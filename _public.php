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

$core->tpl->addBlock('SitemapEntries', ['sitemapsTemplates', 'SitemapEntries']);
$core->tpl->addBlock('SitemapEntryIf', ['sitemapsTemplates', 'SitemapEntryIf']);
$core->tpl->addValue('SitemapEntryLoc', ['sitemapsTemplates', 'SitemapEntryLoc']);
$core->tpl->addValue('SitemapEntryFrequency', ['sitemapsTemplates', 'SitemapEntryFrequency']);
$core->tpl->addValue('SitemapEntryPriority', ['sitemapsTemplates', 'SitemapEntryPriority']);
$core->tpl->addValue('SitemapEntryLastmod', ['sitemapsTemplates', 'SitemapEntryLastmod']);

class sitemapsTemplates
{
    public static function SitemapEntries($attr, $content)
    {
        return
            '<?php if ($_ctx->exists("sitemap_urls")) : ?>' . "\n" .
            '<?php while ($_ctx->sitemap_urls->fetch()) : ?>' . $content . '<?php endwhile; ?>' .
            '<?php endif; ?>' . "\n";
    }

    public static function SitemapEntryIf($attr, $content)
    {
        $if = '';
        if (isset($attr['has_attr'])) {
            switch ($attr['has_attr']) {
                case 'frequency':$if = '!is_null($_ctx->sitemap_urls->frequency)';

                    break;
                case 'priority':$if = '!is_null($_ctx->sitemap_urls->priority)';

                    break;
                case 'lastmod':$if = '!is_null($_ctx->sitemap_urls->lastmod)';

                    break;
            }
        }
        if (!empty($if)) {
            return '<?php if (' . $if . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    public static function SitemapEntryLoc($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, '$_ctx->sitemap_urls->loc') . '; ?>';
    }

    public static function SitemapEntryFrequency($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, '$_ctx->sitemap_urls->frequency') . '; ?>';
    }

    public static function SitemapEntryPriority($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, '$_ctx->sitemap_urls->priority') . '; ?>';
    }

    public static function SitemapEntryLastmod($attr)
    {
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, '$_ctx->sitemap_urls->lastmod') . '; ?>';
    }
}
