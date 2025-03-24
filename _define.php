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
    '7.0',
    [
        'date'     => '2025-03-24T17:11:01+0100',
        'requires' => [
            ['core', '2.33'],
            ['TemplateHelper'],
        ],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://plugins.dotaddict.org/dc2/details/sitemaps',
        'support'    => 'https://github.com/franck-paul/sitemaps',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/sitemaps/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
