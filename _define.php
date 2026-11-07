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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'Sitemaps',
        'Add XML Sitemaps',
        'Pep and contributors',
        '10.1',
        [
            'date'     => '2026-11-07T17:06:11+0100',
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
}
