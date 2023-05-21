<?php
/**
 * @brief sitemaps, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\sitemaps;

use dcCore;
use dcNamespace;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Update
            $old_version = dcCore::app()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.0', '<')) {
                // Rename old settings
                // Change settings names (remove sitemaps_ prefix in them)
                $rename = function (string $name, dcNamespace $settings): void {
                    if ($settings->settingExists('sitemaps_' . $name, true)) {
                        $settings->rename('sitemaps_' . $name, $name);
                    }
                };
                $settings = dcCore::app()->blog->settings->get(My::id());
                foreach ([
                    'active',
                    'home_url', 'home_pr', 'home_fq',
                    'feeds_url', 'feeds_pr', 'feeds_fq',
                    'posts_url', 'posts_pr', 'posts_fq',
                    'pages_url', 'pages_pr', 'pages_fq',
                    'cats_url', 'cats_pr', 'cats_fq',
                    'tags_url', 'tags_pr', 'tags_fq',
                ] as $name) {
                    $rename($name, $settings);
                }
            }

            // Init
            $settings = dcCore::app()->blog->settings->get(My::id());

            // Default settings
            $settings->put('active', false, dcNamespace::NS_BOOL, 'Sitemaps activation', false, true);

            $settings->put('home_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('home_pr', 1, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('home_fq', 3, dcNamespace::NS_INT, '', false, true);

            $settings->put('feeds_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('feeds_pr', 1, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('feeds_fq', 2, dcNamespace::NS_INT, '', false, true);

            $settings->put('posts_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('posts_pr', 1, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('posts_fq', 3, dcNamespace::NS_INT, '', false, true);

            $settings->put('pages_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('pages_pr', 1, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('pages_fq', 0, dcNamespace::NS_INT, '', false, true);

            $settings->put('cats_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('cats_pr', 0.6, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('cats_fq', 4, dcNamespace::NS_INT, '', false, true);

            $settings->put('tags_url', true, dcNamespace::NS_BOOL, '', false, true);
            $settings->put('tags_pr', 0.6, dcNamespace::NS_DOUBLE, '', false, true);
            $settings->put('tags_fq', 4, dcNamespace::NS_INT, '', false, true);

            // Search engines notification
            // Services endpoints
            $search_engines = [
                'google' => [
                    'name' => 'Google',
                    'url'  => 'https://www.google.com/webmasters/tools/ping',
                ],
                'bing' => [
                    'name' => 'MS Bing',
                    'url'  => 'https://www.bing.com/webmaster/ping.aspx',
                ],
            ];
            $settings->put('engines', @serialize($search_engines), dcNamespace::NS_STRING, '', false, true);  // Force update

            // Preferences
            $settings->put('pings', 'google', dcNamespace::NS_STRING, '', false, true);

            // Remove yahoo and mslive search engines from current blog settings
            $pings   = explode(',', (string) $settings->pings);
            $removed = false;
            if ($k = array_search('yahoo', $pings)) {
                unset($pings[$k]);
                $removed = true;
            }
            if ($k = array_search('mslive', $pings)) {
                unset($pings[$k]);
                $removed = true;
            }
            if ($removed) {
                $settings->put('pings', implode(',', $pings), dcNamespace::NS_STRING, '', true, false);
            }
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
