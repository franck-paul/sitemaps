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

use dcAdmin;
use dcCore;
use dcFavorites;
use dcNsProcess;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        // dead but useful code, in order to have translations
        __('sitemaps') . __('sitemaps');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
            __('Sitemaps'),
            My::makeUrl(),
            My::icons(),
            preg_match(My::urlScheme(), $_SERVER['REQUEST_URI']),
            My::checkContext(My::MENU)
        );

        dcCore::app()->addBehavior('adminDashboardFavoritesV2', function (dcFavorites $favs) {
            $favs->register('sitemaps', [
                'title'      => __('Sitemaps'),
                'url'        => My::makeUrl(),
                'small-icon' => My::icons(),
                'large-icon' => My::icons(),
                My::checkContext(My::MENU),
            ]);
        });

        return true;
    }
}
