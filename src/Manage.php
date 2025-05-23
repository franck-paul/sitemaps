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

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Decimal;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Table;
use Dotclear\Helper\Html\Form\Tbody;
use Dotclear\Helper\Html\Form\Td;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Th;
use Dotclear\Helper\Html\Form\Thead;
use Dotclear\Helper\Html\Form\Tr;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\HttpClient;
use Exception;

class Manage extends Process
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $msg = '';

        if (!empty($_POST['saveconfig'])) {
            // Save new configuration
            try {
                $settings = My::settings();

                $map_parts = new ArrayObject([
                    __('Homepage')   => 'home',
                    __('Feeds')      => 'feeds',
                    __('Posts')      => 'posts',
                    __('Pages')      => 'pages',
                    __('Categories') => 'cats',
                    __('Tags')       => 'tags',
                ]);

                # --BEHAVIOR-- sitemapsDefineParts
                App::behavior()->callBehavior('sitemapsDefineParts', $map_parts);

                $active = !empty($_POST['active']);

                $settings->put('active', $active, 'boolean');

                foreach ($map_parts as $v) {
                    ${$v . '_url'} = !empty($_POST[$v . '_url']);
                    ${$v . '_pr'}  = min(abs((float) $_POST[$v . '_pr']), 1);
                    ${$v . '_fq'}  = min(abs((int) $_POST[$v . '_fq']), 6);

                    $settings->put($v . '_url', ${$v . '_url'}, App::blogWorkspace()::NS_BOOL);
                    $settings->put($v . '_pr', ${$v . '_pr'}, App::blogWorkspace()::NS_DOUBLE);
                    $settings->put($v . '_fq', ${$v . '_fq'}, App::blogWorkspace()::NS_INT);
                }

                App::blog()->triggerBlog();
                Notices::addSuccessNotice(__('Configuration successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        } elseif (!empty($_POST['saveprefs'])) {
            // Save ping preferences
            try {
                $settings  = My::settings();
                $new_prefs = '';
                if (!empty($_POST['pings'])) {
                    $new_prefs = implode(',', $_POST['pings']);
                }

                $settings->put('pings', $new_prefs, 'string');

                Notices::addSuccessNotice(__('New preferences saved'));
                My::redirect([
                    'notifications' => 1,
                ]);
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        } elseif (!empty($_POST['ping'])) {
            // Send ping(s)
            $settings      = My::settings();
            $default_pings = explode(',', (string) $settings->pings);
            $pings         = empty($_POST['pings']) ? $default_pings : $_POST['pings'];
            $engines       = @unserialize($settings->engines);
            $sitemap_url   = App::blog()->url() . App::url()->getURLFor('gsitemap');
            $results       = [];
            foreach ($pings as $service) {
                try {
                    if (!array_key_exists($service, $engines)) {
                        continue;
                    }

                    if (false === HttpClient::quickGet($engines[$service]['url'] . '?sitemap=' . urlencode($sitemap_url))) {
                        throw new Exception(__('Response does not seem OK'));
                    }

                    $results[] = sprintf('%s : %s', __('success'), $engines[$service]['name']);
                } catch (Exception $e) {
                    $results[] = sprintf('%s : %s : %s', __('Failure'), $engines[$service]['name'], $e->getMessage());
                }
            }

            $msg = __('Ping(s) sent');
            $msg .= '<br>' . implode("<br>\n", $results);
            Notices::addSuccessNotice($msg);
            My::redirect([
                'notifications' => 1,
            ]);
        }

        if ($msg !== '') {
            Notices::success($msg);
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $settings = My::settings();

        $periods = [
            __('undefined') => 0,
            __('always')    => 1,
            __('hourly')    => 2,
            __('daily')     => 3,
            __('weekly')    => 4,
            __('monthly')   => 5,
            __('never')     => 6,
        ];

        $map_parts = new ArrayObject([
            __('Homepage')   => 'home',
            __('Feeds')      => 'feeds',
            __('Posts')      => 'posts',
            __('Pages')      => 'pages',
            __('Categories') => 'cats',
            __('Tags')       => 'tags',
        ]);

        # --BEHAVIOR-- sitemapsDefineParts
        App::behavior()->callBehavior('sitemapsDefineParts', $map_parts);

        $default_tab = 'options';
        if (isset($_GET['notifications'])) {
            $default_tab = 'notifications';
        }

        $head = Page::jsPageTabs($default_tab);

        Page::openModule(My::name(), $head);

        echo Page::breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('XML Sitemaps')                    => '',
            ]
        );
        echo Notices::getNotices();

        $active = $settings->active;

        foreach ($map_parts as $v) {
            ${$v . '_url'} = $settings->get($v . '_url');
            ${$v . '_pr'}  = $settings->get($v . '_pr');
            ${$v . '_fq'}  = $settings->get($v . '_fq');
        }

        $engines       = @unserialize($settings->engines);
        $default_pings = explode(',', (string) $settings->pings);
        $sitemap_url   = App::blog()->url() . App::url()->getURLFor('gsitemap');

        // First tab (options)

        $lines = [];
        foreach ($map_parts as $key => $value) {
            $lines[] = (new Tr())
                ->items([
                    (new Td())
                        ->items([
                            (new Checkbox($value . '_url', ${$value . '_url'}))
                                ->value(1)
                                ->label((new Label($key, Label::INSIDE_TEXT_AFTER))),
                        ]),
                    (new Td())
                        ->items([
                            (new Decimal($value . '_pr'))
                                ->value((float) ${$value . '_pr'})
                                ->size(4)
                                ->maxlength(4)
                                ->step('0.1'),
                        ]),
                    (new Td())
                        ->items([
                            (new Select($value . '_fq'))
                                ->items($periods)   // @phpstan-ignore-line
                                ->default((string) ${$value . '_fq'}),
                        ]),
                ]);
        }

        echo (new Div('options'))
            ->class('multi-part')
            ->title(__('Configuration'))
            ->items([
                (new Text('h3', __('Options')))
                    ->class('out-of-screen-if-js'),
                (new Form('options-form'))
                    ->action(App::backend()->getPageURL())
                    ->method('post')
                    ->fields([
                        (new Para())
                            ->items([
                                (new Checkbox('active', $active))
                                    ->value(1)
                                    ->label((new Label(__('Enable sitemaps'), Label::INSIDE_TEXT_AFTER))),
                            ]),
                        (new Note())
                            ->class('info')
                            ->text(__("This blog's Sitemap URL:") . ' <strong>' . $sitemap_url . '</strong>'),
                        (new Text('h4', __('Elements to integrate'))),
                        (new Table())
                            ->class('maximal')
                            ->thead((new Thead())
                                ->items([
                                    (new Tr())
                                        ->items([
                                            (new Th())
                                                ->scope('col')
                                                ->text(__('URL')),
                                            (new Th())
                                                ->scope('col')
                                                ->text(__('Priority')),
                                            (new Th())
                                                ->scope('col')
                                                ->text(__('Periodicity')),
                                        ]),
                                ]))
                            ->tbody((new Tbody())
                                ->items($lines)),
                        (new Para())->items([
                            (new Submit(['saveconfig'], __('Save configuration')))
                                ->accesskey('s'),
                            ...My::hiddenFields(),
                        ]),
                    ]),
            ])
            ->render();

        // Second tab (notifications)

        $actions   = [];
        $actions[] = (new Para())
            ->items([
                (new Submit(['saveprefs'], __('Save preferences')))->accesskey('s'),
            ]);
        if ($active) {
            $actions[] = (new Para())
                ->items([
                    (new Submit(['ping'], __('Ping search engines'))),
                ]);
        }

        $elements = [];
        foreach ($engines as $key => $value) {
            $elements[] = (new Tr())
                ->items([
                    (new Td())
                        ->items([
                            (new Checkbox('pings[]', in_array($key, $default_pings)))
                                ->value($key)
                                ->label((new Label($value['name'], Label::INSIDE_TEXT_AFTER))),
                        ]),
                ]);
        }

        echo (new Div('notifications'))
            ->class('multi-part')
            ->title(__('Search engines notification'))
            ->items([
                (new Text('h3', __('Available search engines')))
                    ->class('out-of-screen-if-js'),
                (new Form('prefs-form'))
                    ->action(App::backend()->getPageURL())
                    ->method('post')
                    ->fields([
                        (new Table())
                            ->class('maximal')
                             ->thead((new Thead())
                                ->items([
                                    (new Tr())
                                        ->items([
                                            (new Th())
                                                ->scope('col')
                                                ->text(__('Search engine')),
                                        ]),
                                ]))
                           ->tbody((new Tbody())
                                ->items($elements)),
                        (new Para())->items([
                            ...$actions,
                            ...My::hiddenFields(),
                        ]),
                    ]),
            ])
            ->render();

        Page::helpBlock('sitemaps');
        Page::closeModule();
    }
}
