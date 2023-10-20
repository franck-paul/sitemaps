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
     * @var array<int, array<string, mixed>>
     */
    protected array $urls;

    /**
     * @var array<string>
     */
    protected array $freqs;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $post_types;

    /**
     * @var \Dotclear\Interface\Core\BlogWorkspaceInterface
     */
    protected $settings;

    public function __construct()
    {
        $this->blog = App::blog();

        $this->settings = My::settings();

        $this->urls       = [];
        $this->freqs      = ['', 'always', 'hourly', 'daily', 'weekly', 'monthly', 'never'];
        $this->post_types = [];

        // Default post types
        $this->addPostType(
            'post',
            'post',
            $this->settings->posts_fq,
            $this->settings->posts_pr
        );
        $this->addPostType(
            'page',
            'pages',
            $this->settings->pages_fq,
            $this->settings->pages_pr
        );
    }

    /**
     * Gets the urls.
     *
     * @return     array<int, array<string, mixed>>  The urls.
     */
    public function getURLs(): array
    {
        if ($this->settings->active && empty($this->urls)) {
            $this->collectURLs();
        }

        return $this->urls;
    }

    public function addPostType(string $type, string $base_url, int $freq = 0, float $priority = 0.3): bool
    {
        if (preg_match('!^([a-z_-]+)$!', (string) $type)) {
            $this->post_types[$type]['base_url']  = $base_url;
            $this->post_types[$type]['frequency'] = $this->getFrequency($freq);
            $this->post_types[$type]['priority']  = $this->getPriority($priority);

            return true;
        }

        return false;
    }

    public function addEntry(string $loc, string $priority, string $frequency, string $lastmod = ''): void
    {
        $this->urls[] = [
            'loc'       => $loc,
            'priority'  => $priority,
            'frequency' => ($frequency == '') ? null : $frequency,
            'lastmod'   => ($lastmod == '') ? null : $lastmod,
        ];
    }

    public function getPriority(float $value): string
    {
        return sprintf('%.1f', min(abs((float) $value), 1));
    }

    public function getFrequency(int $value): string
    {
        return $this->freqs[min(abs(intval($value)), 6)];
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
            ->from($sql->as($this->blog->prefix . BlogInterface::POST_TABLE_NAME, 'p'))
            ->join(
                (new JoinStatement())
                    ->left()
                    ->from($sql->as($this->blog->prefix . BlogInterface::COMMENT_TABLE_NAME, 'c'))
                    ->on('c.post_id = p.post_id')
                    ->statement()
            )
            ->where('p.blog_id = ' . $sql->quote($this->blog->id))
            ->and('p.post_type = ' . $sql->quote($type))
            ->and('p.post_status = ' . BlogInterface::POST_PUBLISHED)
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
                if ($rs->comments_dt !== null) {
                    $last_ts = max(strtotime($rs->post_upddt), strtotime($rs->comments_dt));
                } else {
                    $last_ts = strtotime($rs->post_upddt);
                }
                $last_dt = Date::iso8601($last_ts, $rs->post_tz);
                $url     = $this->blog->url . App::url()->getURLFor($base_url, Html::sanitizeURL($rs->post_url));
                $this->addEntry($url, $prio, $freq, $last_dt);
            }
        }
    }

    protected function collectURLs(): void
    {
        // Homepage URL
        if ($this->settings->home_url) {
            $freq = $this->getFrequency((int) $this->settings->home_fq);
            $prio = $this->getPriority($this->settings->home_pr);

            $this->addEntry($this->blog->url, $prio, $freq);
        }

        // Main syndication feeds URLs
        if ($this->settings->feeds_url) {
            $freq = $this->getFrequency((int) $this->settings->feeds_fq);
            $prio = $this->getPriority($this->settings->feeds_pr);

            $this->addEntry(
                $this->blog->url . App::url()->getURLFor('feed', 'rss2'),
                $prio,
                $freq
            );
            $this->addEntry(
                $this->blog->url . App::url()->getURLFor('feed', 'atom'),
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
            $freq = $this->getFrequency($this->settings->cats_fq);
            $prio = $this->getPriority($this->settings->cats_pr);

            $cats = $this->blog->getCategories(['post_type' => 'post']);
            while ($cats->fetch()) {
                $this->addEntry(
                    $this->blog->url . App::url()->getURLFor('category', $cats->cat_url),
                    $prio,
                    $freq
                );
            }
        }

        if (App::plugins()->moduleExists('tags') && $this->settings->tags_url) {
            $freq = $this->getFrequency($this->settings->tags_fq);
            $prio = $this->getPriority($this->settings->tags_pr);

            $meta = App::meta();
            $tags = $meta->getMetadata(['meta_type' => 'tag']);
            $tags = $meta->computeMetaStats($tags);
            while ($tags->fetch()) {
                $this->addEntry(
                    $this->blog->url . App::url()->getURLFor('tag', rawurlencode($tags->meta_id)),
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
