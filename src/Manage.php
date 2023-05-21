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
use dcCore;
use dcNamespace;
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\HttpClient;
use Exception;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::MANAGE);

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        if (!empty($_POST['saveconfig'])) {
            // Save new configuration
            try {
                $settings = dcCore::app()->blog->settings->get(My::id());

                $map_parts = new ArrayObject([
                    __('Homepage')   => 'home',
                    __('Feeds')      => 'feeds',
                    __('Posts')      => 'posts',
                    __('Pages')      => 'pages',
                    __('Categories') => 'cats',
                    __('Tags')       => 'tags',
                ]);

                # --BEHAVIOR-- sitemapsDefineParts
                dcCore::app()->callBehavior('sitemapsDefineParts', $map_parts);

                $active = (empty($_POST['active'])) ? false : true;

                $settings->put('active', $active, 'boolean');

                foreach ($map_parts as $k => $v) {
                    ${$v . '_url'} = (empty($_POST[$v . '_url'])) ? false : true;
                    ${$v . '_pr'}  = min(abs((float) $_POST[$v . '_pr']), 1);
                    ${$v . '_fq'}  = min(abs(intval($_POST[$v . '_fq'])), 6);

                    $settings->put($v . '_url', ${$v . '_url'}, dcNamespace::NS_BOOL);
                    $settings->put($v . '_pr', ${$v . '_pr'}, dcNamespace::NS_DOUBLE);
                    $settings->put($v . '_fq', ${$v . '_fq'}, dcNamespace::NS_INT);
                }
                dcCore::app()->blog->triggerBlog();
                dcPage::addSuccessNotice(__('Configuration successfully updated.'));
                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        } elseif (!empty($_POST['saveprefs'])) {
            // Save ping preferences
            try {
                $settings  = dcCore::app()->blog->settings->get(My::id());
                $new_prefs = '';
                if (!empty($_POST['pings'])) {
                    $new_prefs = implode(',', $_POST['pings']);
                }
                $settings->put('pings', $new_prefs, 'string');

                dcPage::addSuccessNotice(__('New preferences saved'));
                Http::redirect(dcCore::app()->admin->getPageURL() . '&notifications=1');
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        } elseif (!empty($_POST['ping'])) {
            // Send ping(s)
            $settings      = dcCore::app()->blog->settings->get(My::id());
            $default_pings = explode(',', $settings->pings);
            $pings         = empty($_POST['pings']) ? $default_pings : $_POST['pings'];
            $engines       = @unserialize($settings->engines);
            $sitemap_url   = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('gsitemap');
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
            $msg .= '<br />' . implode("<br />\n", $results);
            dcPage::addSuccessNotice($msg);
            Http::redirect(dcCore::app()->admin->getPageURL() . '&notifications=1');
        }

        if (!empty($msg)) {
            dcPage::success($msg);
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

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
        dcCore::app()->callBehavior('sitemapsDefineParts', $map_parts);

        $default_tab = 'options';
        if (isset($_GET['notifications'])) {
            $default_tab = 'notifications';
        }
        $head = dcPage::jsPageTabs($default_tab);

        dcPage::openModule(__('XML Sitemaps'), $head);

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('XML Sitemaps')                          => '',
            ]
        );
        echo dcPage::notices();

        $active = $settings->active;

        foreach ($map_parts as $v) {
            ${$v . '_url'} = $settings->get($v . '_url');
            ${$v . '_pr'}  = $settings->get($v . '_pr');
            ${$v . '_fq'}  = $settings->get($v . '_fq');
        }

        $engines       = @unserialize($settings->engines);
        $default_pings = explode(',', $settings->pings);
        $sitemap_url   = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('gsitemap');

        // First tab (options)

        $lines = [];
        foreach ($map_parts as $key => $value) {
            $lines[] = (new Para(null, 'tr'))->items([
                (new Para(null, 'td'))->items([
                    (new Checkbox($value . '_url', ${$value . '_url'}))
                        ->value(1)
                        ->label((new Label($key, Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para(null, 'td'))->items([
                    (new Input($value . '_pr', 'number'))
                        ->value((float) ${$value . '_pr'})
                        ->size(4)
                        ->maxlength(4)
                        ->extra('step="0.1"')
                        ->label((new Label(__('Priority'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para(null, 'td'))->items([
                    (new Select($value . '_fq'))
                        ->items($periods)
                        ->default((string) ${$value . '_fq'})
                        ->label((new Label(__('Priority'), Label::INSIDE_TEXT_BEFORE))),
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
                ->action(dcCore::app()->admin->getPageURL())
                ->method('post')
                ->fields([
                    (new Para())->items([
                        (new Checkbox('active', $active))
                            ->value(1)
                            ->label((new Label(__('Enable sitemaps'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                    (new Para())->class('info')->items([
                        (new Text(null, __("This blog's Sitemap URL:") . ' <strong>' . $sitemap_url . '</strong>')),
                    ]),
                    (new Text('h4', __('Elements to integrate'))),
                    (new Para(null, 'table'))->class('maximal')->items([
                        (new Para(null, 'tbody'))->items($lines),
                    ]),
                    (new Para())->items([
                        (new Submit(['saveconfig'], __('Save configuration')))->accesskey('s'),
                        dcCore::app()->formNonce(false),
                    ]),
                ]),
            ])
            ->render();

        // Second tab (notifications)

        $actions   = [];
        $actions[] = (new Para())->items([
            (new Submit(['saveprefs'], __('Save preferences')))->accesskey('s'),
        ]);
        if ($active) {
            $actions[] = (new Para())->items([
                (new Submit(['ping'], __('Ping search engines'))),
            ]);
        }

        $elements = [];
        foreach ($engines as $key => $value) {
            $elements[] = (new Para(null, 'tr'))->items([
                (new Para(null, 'td'))->items([
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
                ->action(dcCore::app()->admin->getPageURL())
                ->method('post')
                ->fields([
                    (new Para(null, 'table'))->class('maximal')->items([
                        (new Para(null, 'tbody'))->items($elements),
                    ]),

                    (new Para())->items([
                        ...$actions,
                        dcCore::app()->formNonce(false),
                    ]),
                ]),
            ])
            ->render();

        dcPage::helpBlock('sitemaps');
        dcPage::closeModule();
    }
}
