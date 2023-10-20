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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Interface\Core\BlogWorkspaceInterface;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Update
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.0', '<')) {
                // Rename old settings
                // Change settings names (remove sitemaps_ prefix in them)
                $rename = function (string $name, BlogWorkspaceInterface $settings): void {
                    if ($settings->settingExists('sitemaps_' . $name, true)) {
                        $settings->rename('sitemaps_' . $name, $name);
                    }
                };
                $settings = My::settings();
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
            $settings = My::settings();

            // Default settings
            $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Sitemaps activation', false, true);

            $settings->put('home_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('home_pr', 1, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('home_fq', 3, App::blogWorkspace()::NS_INT, '', false, true);

            $settings->put('feeds_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('feeds_pr', 1, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('feeds_fq', 2, App::blogWorkspace()::NS_INT, '', false, true);

            $settings->put('posts_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('posts_pr', 1, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('posts_fq', 3, App::blogWorkspace()::NS_INT, '', false, true);

            $settings->put('pages_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('pages_pr', 1, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('pages_fq', 0, App::blogWorkspace()::NS_INT, '', false, true);

            $settings->put('cats_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('cats_pr', 0.6, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('cats_fq', 4, App::blogWorkspace()::NS_INT, '', false, true);

            $settings->put('tags_url', true, App::blogWorkspace()::NS_BOOL, '', false, true);
            $settings->put('tags_pr', 0.6, App::blogWorkspace()::NS_DOUBLE, '', false, true);
            $settings->put('tags_fq', 4, App::blogWorkspace()::NS_INT, '', false, true);

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
            $settings->put('engines', @serialize($search_engines), App::blogWorkspace()::NS_STRING, '', false, true);  // Force update

            // Preferences
            $settings->put('pings', 'google', App::blogWorkspace()::NS_STRING, '', false, true);

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
                $settings->put('pings', implode(',', $pings), App::blogWorkspace()::NS_STRING, '', true, false);
            }
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
