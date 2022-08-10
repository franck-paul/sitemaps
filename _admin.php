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

$_menu['Blog']->addItem(
    __('Sitemaps'),
    'plugin.php?p=sitemaps',
    urldecode(dcPage::getPF('sitemaps/icon.svg')),
    preg_match('/plugin.php\?p=sitemaps(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check('contentadmin', dcCore::app()->blog->id)
);

dcCore::app()->addBehavior('adminDashboardFavorites', 'sitemapsDashboardFavorites');

function sitemapsDashboardFavorites($core = null, $favs)
{
    $favs->register('sitemaps', [
        'title'       => __('Sitemaps'),
        'url'         => 'plugin.php?p=sitemaps',
        'small-icon'  => urldecode(dcPage::getPF('sitemaps/icon.svg')),
        'large-icon'  => urldecode(dcPage::getPF('sitemaps/icon.svg')),
        'permissions' => 'usage,contentadmin',
    ]);
}
