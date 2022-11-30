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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Sitemaps'),
    'plugin.php?p=sitemaps',
    urldecode(dcPage::getPF('sitemaps/icon.svg')),
    preg_match('/plugin.php\?p=sitemaps(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
        dcAuth::PERMISSION_CONTENT_ADMIN,
    ]), dcCore::app()->blog->id)
);

dcCore::app()->addBehavior('adminDashboardFavoritesV2', 'sitemapsDashboardFavorites');

function sitemapsDashboardFavorites($favs)
{
    $favs->register('sitemaps', [
        'title'       => __('Sitemaps'),
        'url'         => 'plugin.php?p=sitemaps',
        'small-icon'  => urldecode(dcPage::getPF('sitemaps/icon.svg')),
        'large-icon'  => urldecode(dcPage::getPF('sitemaps/icon.svg')),
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
    ]);
}
