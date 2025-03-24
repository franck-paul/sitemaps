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
use Dotclear\Plugin\TemplateHelper\Code;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function SitemapEntries(array|ArrayObject $attr, string $content): string
    {
        return Code::getPHPTemplateBlockCode(
            FrontendTemplateCode::SitemapEntries(...),
            content: $content,
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function SitemapEntryIf(array|ArrayObject $attr, string $content): string
    {
        if (isset($attr['has_attr']) && in_array($attr['has_attr'], ['frequency', 'priority', 'lastmod'])) {
            return Code::getPHPTemplateBlockCode(
                FrontendTemplateCode::SitemapEntryIf(...),
                [
                    $attr['has_attr'],
                ],
                content: $content,
                attr: $attr,
            );
        }

        return $content;
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function SitemapEntryLoc(array|ArrayObject $attr): string
    {
        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::SitemapEntryLoc(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function SitemapEntryFrequency(array|ArrayObject $attr): string
    {
        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::SitemapEntryFrequency(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function SitemapEntryPriority(array|ArrayObject $attr): string
    {
        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::SitemapEntryPriority(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function SitemapEntryLastmod(array|ArrayObject $attr): string
    {
        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::SitemapEntryLastmod(...),
            attr: $attr,
        );
    }
}
