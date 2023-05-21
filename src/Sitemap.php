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

use dcBlog;
use dcCore;
use dcMeta;
use Dotclear\Database\Statement\JoinStatement;
use Dotclear\Database\Statement\SelectStatement;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;

class Sitemap
{
    protected $blog;
    protected $urls;
    protected $freqs;
    protected $post_types;
    protected $settings;

    public function __construct()
    {
        $this->blog = dcCore::app()->blog;

        $this->settings = dcCore::app()->blog->settings->get(My::id());

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

    public function getURLs()
    {
        if ($this->settings->active && empty($this->urls)) {
            $this->collectURLs();
        }

        return $this->urls;
    }

    public function addPostType($type, $base_url, $freq = 0, $priority = 0.3)
    {
        if (preg_match('!^([a-z_-]+)$!', (string) $type)) {
            $this->post_types[$type]['base_url']  = $base_url;
            $this->post_types[$type]['frequency'] = $this->getFrequency($freq);
            $this->post_types[$type]['priority']  = $this->getPriority($priority);

            return true;
        }

        return false;
    }

    public function addEntry($loc, $priority, $frequency, $lastmod = '')
    {
        $this->urls[] = [
            'loc'       => $loc,
            'priority'  => $priority,
            'frequency' => ($frequency == '') ? null : $frequency,
            'lastmod'   => ($lastmod == '') ? null : $lastmod,
        ];
    }

    public function getPriority($value)
    {
        return (sprintf('%.1f', min(abs((float) $value), 1)));
    }

    public function getFrequency($value)
    {
        return $this->freqs[min(abs(intval($value)), 6)];
    }

    public function collectEntriesURLs($type = 'post')
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
            ->from($sql->as($this->blog->prefix . dcBlog::POST_TABLE_NAME, 'p'))
            ->join(
                (new JoinStatement())
                    ->left()
                    ->from($sql->as($this->blog->prefix . dcBlog::COMMENT_TABLE_NAME, 'c'))
                    ->on('c.post_id = p.post_id')
                    ->statement()
            )
            ->where('p.blog_id = ' . $sql->quote($this->blog->id))
            ->and('p.post_type = ' . $sql->quote($type))
            ->and('p.post_status = ' . dcBlog::POST_PUBLISHED)
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

        while ($rs->fetch()) {
            if ($rs->comments_dt !== null) {
                $last_ts = max(strtotime($rs->post_upddt), strtotime($rs->comments_dt));
            } else {
                $last_ts = strtotime($rs->post_upddt);
            }
            $last_dt = Date::iso8601($last_ts, $rs->post_tz);
            $url     = $this->blog->url . dcCore::app()->url->getURLFor($base_url, Html::sanitizeURL($rs->post_url));
            $this->addEntry($url, $prio, $freq, $last_dt);
        }
    }

    protected function collectURLs()
    {
        // Homepage URL
        if ($this->settings->home_url) {
            $freq = $this->getFrequency($this->settings->home_fq);
            $prio = $this->getPriority($this->settings->home_pr);

            $this->addEntry($this->blog->url, $prio, $freq);
        }

        // Main syndication feeds URLs
        if ($this->settings->feeds_url) {
            $freq = $this->getFrequency($this->settings->feeds_fq);
            $prio = $this->getPriority($this->settings->feeds_pr);

            $this->addEntry(
                $this->blog->url . dcCore::app()->url->getURLFor('feed', 'rss2'),
                $prio,
                $freq
            );
            $this->addEntry(
                $this->blog->url . dcCore::app()->url->getURLFor('feed', 'atom'),
                $prio,
                $freq
            );
        }

        // Posts entries URLs
        if ($this->settings->posts_url) {
            $this->collectEntriesURLs('post');
        }

        // Pages entries URLs
        if (dcCore::app()->plugins->moduleExists('pages') && $this->settings->pages_url) {
            $this->collectEntriesURLs('page');
        }

        // Categories URLs
        if ($this->settings->cats_url) {
            $freq = $this->getFrequency($this->settings->cats_fq);
            $prio = $this->getPriority($this->settings->cats_pr);

            $cats = $this->blog->getCategories(['post_type' => 'post']);
            while ($cats->fetch()) {
                $this->addEntry(
                    $this->blog->url . dcCore::app()->url->getURLFor('category', $cats->cat_url),
                    $prio,
                    $freq
                );
            }
        }

        if (dcCore::app()->plugins->moduleExists('tags') && $this->settings->tags_url) {
            $freq = $this->getFrequency($this->settings->tags_fq);
            $prio = $this->getPriority($this->settings->tags_pr);

            $meta = new dcMeta();
            $tags = $meta->getMetadata(['meta_type' => 'tag']);
            $tags = $meta->computeMetaStats($tags);
            while ($tags->fetch()) {
                $this->addEntry(
                    $this->blog->url . dcCore::app()->url->getURLFor('tag', rawurlencode($tags->meta_id)),
                    $prio,
                    $freq
                );
            }
        }

        // External parts ?
        # --BEHAVIOR-- sitemapsURLsCollect
        dcCore::app()->callBehavior('sitemapsURLsCollect', $this);
    }
}
