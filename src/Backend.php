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
use Dotclear\Core\Backend\Favorites;
use Dotclear\Core\Backend\Menus;
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // dead but useful code, in order to have translations
        __('sitemaps') . __('sitemaps');

        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        My::addBackendMenuItem(Menus::MENU_BLOG);

        App::behavior()->addBehavior('adminDashboardFavoritesV2', function (Favorites $favs) {
            $favs->register('sitemaps', [
                'title'       => __('Sitemaps'),
                'url'         => My::manageUrl(),
                'small-icon'  => My::icons(),
                'large-icon'  => My::icons(),
                'permissions' => My::checkContext(My::MENU),
            ]);
        });

        return true;
    }
}
