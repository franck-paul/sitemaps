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

        $sitemap_url = App::blog()->url() . App::url()->getURLFor('gsitemap');

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

        // Second tab (help on search engines console)

        $elements = function () {
            $engines = [
                __('Google') => [
                    'console' => true,
                    'url'     => 'https://search.google.com/search-console',
                    'note'    => __('Will also use the robots.txt file from your server'),
                ],
                __('MS Bing') => [
                    'console' => true,
                    'url'     => 'https://www.bing.com/webmasters/sitemaps',
                ],
                __('Yandex') => [
                    'console' => true,
                    'url'     => 'https://webmaster.yandex.com/site/indexing/sitemap/',
                ],
                __('Baidu') => [
                    'console' => true,
                    'url'     => 'https://ziyuan.baidu.com/linksubmit/index',
                ],
                __('DuckDuckGo') => [
                    'console' => false,
                    'note'    => __('Use the robots.txt file from your server'),
                ],
                __('Qwant') => [
                    'console' => false,
                    'note'    => __('No console nor using robots.txt file'),
                ],
                __('Yahoo Search') => [
                    'console' => false,
                    'note'    => __('Relies on MS Bing’s search results'),
                ],
                __('Ecosia') => [
                    'console' => false,
                    'note'    => __('Relies on Google and MS Bing’s search results'),
                ],
                __('Startpage') => [
                    'console' => false,
                    'note'    => __('Relies on Google’s search results'),
                ],
            ];
            App::lexical()->lexicalKeySort($engines, App::lexical()::ADMIN_LOCALE);

            foreach ($engines as $name => $url) {
                $info = $url['console'] ? sprintf(
                    __('<a href="%s" target="_blank" rel="noopener noreferrer">%s console</a>'),
                    $url['url'],
                    $name,
                ) : '';
                $note = isset($url['note']) ? '<em>' . $url['note'] . '</em>' : '';
                yield (new Tr())
                    ->items([
                        (new Td())
                            ->text($name),
                        (new Td())
                            ->text($info),
                        (new Td())
                            ->text($note),
                    ]);
            }
        };

        echo (new Div('notifications'))
            ->class('multi-part')
            ->title(__('Search engines notification'))
            ->items([
                (new Note())
                    ->text(__('Search engine APIs are no longer available for free, so you must register your blog’s sitemap on each of them in their management console. Below are the URLs for the consoles of some search engines.')),
                (new Note())
                    ->class('info')
                    ->text(__("This blog's Sitemap URL:") . ' <strong>' . $sitemap_url . '</strong>'),
                (new Table())
                    ->class('maximal')
                     ->thead((new Thead())
                        ->items([
                            (new Tr())
                                ->items([
                                    (new Th())
                                        ->scope('col')
                                        ->text(__('Search engine')),
                                    (new Th())
                                        ->scope('col')
                                        ->text(__('Console URL to register your sitemap')),
                                    (new Th())
                                        ->scope('col')
                                        ->text(__('Note')),
                                ]),
                        ]))
                   ->tbody((new Tbody())
                        ->items([
                            ...$elements(),
                        ])),
                (new Note())
                    ->class('info')
                    ->text(sprintf(__('If possible, you can also add a new line to your server’s robots.txt file with <code>Sitemap: %s</code>.<br> Note that you may have multiple lines, one for each blog you have. Some of search engines will use this file for their indexing process.'), $sitemap_url)),
            ])
            ->render();

        Page::helpBlock('sitemaps');
        Page::closeModule();
    }
}
