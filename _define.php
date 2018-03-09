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
    '1.3.3',                // Version
                            // Properties
    array(
        'requires'    => array(array('core', '2.11')),
        'permissions' => 'contentadmin',
        'type'        => 'plugin',
        'support'     => 'http://forum.dotclear.org/viewtopic.php?id=48307',
        'details'     => 'http://plugins.dotaddict.org/dc2/details/sitemaps'
    )
);
