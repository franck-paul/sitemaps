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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Sitemaps",             // Name
    "Add XML Sitemaps",     // Description
    "Pep and contributors", // Author
    '1.4',                  // Version
                            // Properties
    [
        'requires'    => [['core', '2.13']],
        'permissions' => 'contentadmin',
        'type'        => 'plugin',
        'support'     => 'http://forum.dotclear.org/viewtopic.php?id=48307',
        'details'     => 'http://plugins.dotaddict.org/dc2/details/sitemaps'
    ]
);
