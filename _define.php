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
    '6.2',
    [
        'date'        => '2025-02-26T16:06:56+0100',
        'requires'    => [['core', '2.33']],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://plugins.dotaddict.org/dc2/details/sitemaps',
        'support'    => 'https://github.com/franck-paul/sitemaps',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/sitemaps/main/dcstore.xml',
    ]
);
