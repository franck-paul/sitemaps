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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Sitemaps',             // Name
    'Add XML Sitemaps',     // Description
    'Pep and contributors', // Author
    '1.5',                // Version
                            // Properties
    [
        'requires'    => [['core', '2.23']],
        'permissions' => 'contentadmin',
        'type'        => 'plugin',

        'details'    => 'https://plugins.dotaddict.org/dc2/details/sitemaps',       // Details URL
        'support'    => 'https://github.com/franck-paul/sitemaps',                  // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/sitemaps/master/dcstore.xml',
    ]
);
