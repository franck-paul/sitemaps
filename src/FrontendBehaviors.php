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

use dcCore;
use dcPublic;

// Behavior(s)
class FrontendBehaviors
{
    public static function addTemplatePath()
    {
        dcCore::app()->tpl->setPath(
            dcCore::app()->tpl->getPath(),
            implode(DIRECTORY_SEPARATOR, [My::path(), dcPublic::TPL_ROOT])
        );
    }
}
