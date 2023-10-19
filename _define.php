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
$this->registerModule(
    'Sitemaps',
    'Add XML Sitemaps',
    'Pep and contributors',
    '4.0',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type' => 'plugin',

        'details'    => 'https://plugins.dotaddict.org/dc2/details/sitemaps',
        'support'    => 'https://github.com/franck-paul/sitemaps',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/sitemaps/master/dcstore.xml',
    ]
);
