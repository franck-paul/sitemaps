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
    '10.0',
    [
        'date'     => '2026-08-03T10:10:27+0200',
        'requires' => [
            ['core', '2.39'],
            ['TemplateHelper'],
        ],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://dotclear.org/plugin/detail/sitemaps',
        'support'    => 'https://github.com/franck-paul/sitemaps',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/sitemaps/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
