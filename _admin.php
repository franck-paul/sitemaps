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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$_menu['Blog']->addItem(__('Sitemaps'),
    'plugin.php?p=sitemaps',
    urldecode(dcPage::getPF('sitemaps/icon.png')),
    preg_match('/plugin.php\?p=sitemaps(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('contentadmin', $core->blog->id));

$core->addBehavior('adminDashboardFavorites', 'sitemapsDashboardFavorites');

function sitemapsDashboardFavorites($core, $favs)
{
    $favs->register('sitemaps', array(
        'title'       => __('Sitemaps'),
        'url'         => 'plugin.php?p=sitemaps',
        'small-icon'  => urldecode(dcPage::getPF('sitemaps/icon.png')),
        'large-icon'  => urldecode(dcPage::getPF('sitemaps/icon-big.png')),
        'permissions' => 'usage,contentadmin'
    ));
}
