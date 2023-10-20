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
use Dotclear\Core\Frontend\Utility;

// Behavior(s)
class FrontendBehaviors
{
    public static function addTemplatePath(): string
    {
        App::frontend()->template()->setPath(
            App::frontend()->template()->getPath(),
            implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT])
        );

        return '';
    }
}
