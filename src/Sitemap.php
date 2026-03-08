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
use Dotclear\Database\Statement\JoinStatement;
use Dotclear\Database\Statement\SelectStatement;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;
use Dotclear\Interface\Core\BlogInterface;

class Sitemap
{
    protected BlogInterface $blog;

    /**
     * @var array<int, array{loc: string, priority: string, frequency: ?string, lastmod: ?string}>
     */
    protected array $urls = [];

    /**
     * @var array<string>
     */
    protected array $freqs = ['', 'always', 'hourly', 'daily', 'weekly', 'monthly', 'never'];

    /**
     * @var array<string, array{base_url: string, frequency: string, priority: string}>
     */
    protected array $post_types = [];

    /**
     * @var \Dotclear\Interface\Core\BlogWorkspaceInterface
     */
    protected $settings;

    public function __construct()
    {
        $this->settings = My::settings();

        // Default post types
        $posts_fq = is_numeric($posts_fq = $this->settings->posts_fq) ? (int) $posts_fq : 0;
        $posts_pr = is_numeric($posts_pr = $this->settings->posts_pr) ? (float) $posts_pr : 0.3;
        $this->addPostType(
            'post',
            'post',
            $posts_fq,
            $posts_pr
        );

        $pages_fq = is_numeric($pages_fq = $this->settings->pages_fq) ? (int) $pages_fq : 0;
        $pages_pr = is_numeric($pages_pr = $this->settings->pages_pr) ? (float) $pages_pr : 0.3;
        $this->addPostType(
            'page',
            'pages',
            $pages_fq,
            $pages_pr
        );
    }

    /**
     * Gets the urls.
     *
     * @return     array<int, array<string, mixed>>  The urls.
     */
    public function getURLs(): array
    {
        if ($this->settings->active && $this->urls === []) {
            $this->collectURLs();
        }

        return $this->urls;
    }

    public function addPostType(string $type, string $base_url, int $freq = 0, float $priority = 0.3): bool
    {
        if (preg_match('!^([a-z_-]+)$!', $type)) {
            $this->post_types[$type] = [
                'base_url'  => $base_url,
                'frequency' => $this->getFrequency($freq),
                'priority'  => $this->getPriority($priority),
            ];

            return true;
        }

        return false;
    }

    public function addEntry(string $loc, string $priority, string $frequency, string $lastmod = ''): void
    {
        $this->urls[] = [
            'loc'       => $loc,
            'priority'  => $priority,
            'frequency' => ($frequency === '') ? null : $frequency,
            'lastmod'   => ($lastmod === '') ? null : $lastmod,
        ];
    }

    public function getPriority(float $value): string
    {
        return sprintf('%.1f', min(abs($value), 1));
    }

    public function getFrequency(int $value): string
    {
        return $this->freqs[min(abs($value), 6)];
    }

    public function collectEntriesURLs(string $type = 'post'): void
    {
        if (!array_key_exists($type, $this->post_types)) {
            return;
        }

        $freq     = $this->post_types[$type]['frequency'];
        $prio     = $this->post_types[$type]['priority'];
        $base_url = $this->post_types[$type]['base_url'];

        // Let's have fun !

        $sql = new SelectStatement();
        $sql
            ->columns([
                'p.post_id',
                'p.post_url',
                'p.post_tz',
                'p.post_upddt',
                $sql->as($sql->max('c.comment_upddt'), 'comments_dt'),
            ])
            ->from($sql->as(App::db()->con()->prefix() . App::blog()::POST_TABLE_NAME, 'p'))
            ->join(
                (new JoinStatement())
                    ->left()
                    ->from($sql->as(App::db()->con()->prefix() . App::blog()::COMMENT_TABLE_NAME, 'c'))
                    ->on('c.post_id = p.post_id')
                    ->statement()
            )
            ->where('p.blog_id = ' . $sql->quote(App::blog()->id()))
            ->and('p.post_type = ' . $sql->quote($type))
            ->and('p.post_status = ' . App::status()->post()::PUBLISHED)
            ->and($sql->isNull('p.post_password'))
            ->group([
                'p.post_id',
                'p.post_url',
                'p.post_tz',
                'p.post_upddt',
                'p.post_dt',
            ])
            ->order('p.post_dt ASC')
        ;
        $rs = $sql->select();

        if ($rs) {
            while ($rs->fetch()) {
                $post_upddt  = is_string($post_upddt = $rs->post_upddt) ? $post_upddt : 'now';
                $comments_dt = is_string($comments_dt = $rs->comments_dt) ? $comments_dt : null;
                $last_ts     = $comments_dt !== null ? max(strtotime($post_upddt), strtotime($comments_dt)) : strtotime($post_upddt);
                $post_url    = is_string($post_url = $rs->post_url) ? $post_url : '';
                if ($last_ts !== false && $post_upddt !== '') {
                    $post_tz = is_string($post_tz = $rs->post_tz) ? $post_tz : 'UTC';
                    $last_dt = Date::iso8601($last_ts, $post_tz);
                    $url     = App::blog()->url() . App::url()->getURLFor($base_url, Html::sanitizeURL($post_url));
                    $this->addEntry($url, $prio, $freq, $last_dt);
                }
            }
        }
    }

    protected function collectURLs(): void
    {
        // Homepage URL
        if ($this->settings->home_url) {
            $fq   = is_numeric($fq = $this->settings->home_fq) ? (int) $fq : 0;
            $pr   = is_numeric($pr = $this->settings->home_pr) ? (float) $pr : 0;
            $freq = $this->getFrequency($fq);
            $prio = $this->getPriority($pr);

            $this->addEntry(App::blog()->url(), $prio, $freq);
        }

        // Main syndication feeds URLs
        if ($this->settings->feeds_url) {
            $fq   = is_numeric($fq = $this->settings->feeds_fq) ? (int) $fq : 0;
            $pr   = is_numeric($pr = $this->settings->feeds_pr) ? (float) $pr : 0;
            $freq = $this->getFrequency($fq);
            $prio = $this->getPriority($pr);

            $this->addEntry(
                App::blog()->url() . App::url()->getURLFor('feed', 'rss2'),
                $prio,
                $freq
            );
            $this->addEntry(
                App::blog()->url() . App::url()->getURLFor('feed', 'atom'),
                $prio,
                $freq
            );
        }

        // Posts entries URLs
        if ($this->settings->posts_url) {
            $this->collectEntriesURLs('post');
        }

        // Pages entries URLs
        if (App::plugins()->moduleExists('pages') && $this->settings->pages_url) {
            $this->collectEntriesURLs('page');
        }

        // Categories URLs
        if ($this->settings->cats_url) {
            $fq   = is_numeric($fq = $this->settings->cats_fq) ? (int) $fq : 0;
            $pr   = is_numeric($pr = $this->settings->cats_pr) ? (float) $pr : 0;
            $freq = $this->getFrequency($fq);
            $prio = $this->getPriority($pr);

            $cats = App::blog()->getCategories(['post_type' => 'post']);
            while ($cats->fetch()) {
                $cat_url = is_string($cat_url = $cats->cat_url) ? $cat_url : '';
                $this->addEntry(
                    App::blog()->url() . App::url()->getURLFor('category', $cat_url),
                    $prio,
                    $freq
                );
            }
        }

        if (App::plugins()->moduleExists('tags') && $this->settings->tags_url) {
            $fq   = is_numeric($fq = $this->settings->tags_fq) ? (int) $fq : 0;
            $pr   = is_numeric($pr = $this->settings->tags_pr) ? (float) $pr : 0;
            $freq = $this->getFrequency($fq);
            $prio = $this->getPriority($pr);

            $meta = App::meta();
            $tags = $meta->getMetadata(['meta_type' => 'tag']);
            $tags = $meta->computeMetaStats($tags);
            while ($tags->fetch()) {
                $meta_id = is_string($meta_id = $tags->meta_id) ? $meta_id : '';
                $this->addEntry(
                    App::blog()->url() . App::url()->getURLFor('tag', rawurlencode($meta_id)),
                    $prio,
                    $freq
                );
            }
        }

        // External parts ?
        # --BEHAVIOR-- sitemapsURLsCollect
        App::behavior()->callBehavior('sitemapsURLsCollect', $this);
    }
}
