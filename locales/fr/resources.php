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
if (!isset(dcCore::app()->resources['help']['sitemaps'])) {
    dcCore::app()->resources['help']['sitemaps'] = __DIR__ . '/help/sitemaps.html';
}
