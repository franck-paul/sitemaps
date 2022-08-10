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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$page_title = __('XML Sitemaps');

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

$msg         = '';
$default_tab = 'sitemaps_options';
$active      = dcCore::app()->blog->settings->sitemaps->sitemaps_active;

foreach ($map_parts as $k => $v) {
    ${$v . '_url'} = dcCore::app()->blog->settings->sitemaps->get('sitemaps_' . $v . '_url');
    ${$v . '_pr'}  = dcCore::app()->blog->settings->sitemaps->get('sitemaps_' . $v . '_pr');
    ${$v . '_fq'}  = dcCore::app()->blog->settings->sitemaps->get('sitemaps_' . $v . '_fq');
}

$engines       = @unserialize(dcCore::app()->blog->settings->sitemaps->sitemaps_engines);
$default_pings = explode(',', dcCore::app()->blog->settings->sitemaps->sitemaps_pings);

// Save new configuration
if (!empty($_POST['saveconfig'])) {
    try {
        dcCore::app()->blog->settings->addNameSpace('sitemaps');

        $active = (empty($_POST['active'])) ? false : true;
        dcCore::app()->blog->settings->sitemaps->put('sitemaps_active', $active, 'boolean');

        foreach ($map_parts as $k => $v) {
            ${$v . '_url'} = (empty($_POST[$v . '_url'])) ? false : true;
            ${$v . '_pr'}  = min(abs((float) $_POST[$v . '_pr']), 1);
            ${$v . '_fq'}  = min(abs(intval($_POST[$v . '_fq'])), 6);

            dcCore::app()->blog->settings->sitemaps->put('sitemaps_' . $v . '_url', ${$v . '_url'}, 'boolean');
            dcCore::app()->blog->settings->sitemaps->put('sitemaps_' . $v . '_pr', ${$v . '_pr'}, 'double');
            dcCore::app()->blog->settings->sitemaps->put('sitemaps_' . $v . '_fq', ${$v . '_fq'}, 'integer');
        }
        dcCore::app()->blog->triggerBlog();
        http::redirect('plugin.php?p=' . $p . '&conf=1');   // @phpstan-ignore-line
        exit;
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

// Save ping preferences
elseif (!empty($_POST['saveprefs'])) {
    try {
        $new_prefs = '';
        if (!empty($_POST['pings'])) {
            $new_prefs = implode(',', $_POST['pings']);
        }
        dcCore::app()->blog->settings->addNamespace('sitemaps');
        dcCore::app()->blog->settings->sitemaps->put('sitemaps_pings', $new_prefs, 'string');
        http::redirect('plugin.php?p=' . $p . '&prefs=1');  // @phpstan-ignore-line
        exit;
    } catch (Exception $e) {
        $default_tab = 'sitemaps_notifications';
        dcCore::app()->error->add($e->getMessage());
    }
}

// Send ping(s)
elseif (!empty($_POST['ping'])) {
    $pings       = empty($_POST['pings']) ? $default_pings : $_POST['pings'];
    $sitemap_url = dcCore::app()->blog->url . dcCore::app()->url->getURLFor('gsitemap');
    $results     = [];
    foreach ($pings as $service) {
        try {
            if (!array_key_exists($service, $engines)) {
                continue;
            }

            if (false === netHttp::quickGet($engines[$service]['url'] . '?sitemap=' . urlencode($sitemap_url))) {
                throw new Exception(__('Response does not seem OK'));
            }
            $results[] = sprintf('%s : %s', __('success'), $engines[$service]['name']);
        } catch (Exception $e) {
            $results[] = sprintf('%s : %s : %s', __('Failure'), $engines[$service]['name'], $e->getMessage());
        }
    }
    $msg = __('Ping(s) sent');
    $msg .= '<br />' . implode("<br />\n", $results);
} else {
    if (isset($_GET['prefs'])) {
        $msg         = __('New preferences saved');
        $default_tab = 'sitemaps_notifications';
    } elseif (isset($_GET['conf'])) {
        $msg         = __('Configuration successfully updated.');
        $default_tab = 'sitemaps_options';
    }
}
?>
<html>
<head>
  <title><?php echo $page_title; ?></title>
  <?php echo dcPage::jsPageTabs($default_tab); ?>
</head>
<body>
<?php
echo dcPage::breadcrumb(
    [
        html::escapeHTML(dcCore::app()->blog->name)           => '',
        '<span class="page-title">' . $page_title . '</span>' => '',
    ]
);

if (!empty($msg)) {
    dcPage::success($msg);
}
?>
<!-- Configuration panel -->
<div class="multi-part" id="sitemaps_options" title="<?php echo __('Configuration'); ?>">
  <form method="post" action="<?php echo $p_url; ?>">
  <div class="fieldset">
    <h4><?php echo __('Plugin activation'); ?></h4>
    <p class="field">
    <label class=" classic"><?php echo form::checkbox('active', 1, $active); ?>&nbsp;
    <?php echo __('Enable sitemaps'); ?>
    </label>
    </p>
    <p class="info">
    <?php echo __("This blog's Sitemap URL:"); ?>&nbsp;
    <strong><?php echo dcCore::app()->blog->url . dcCore::app()->url->getURLFor('gsitemap'); ?></strong>
    </p>
  </div>

  <div class="fieldset">
    <h4><?php echo __('Elements to integrate'); ?></h4>
    <table class="maximal">
    <tbody>
<?php foreach ($map_parts as $k => $v): ?>
    <tr>
    <td>
      <label class="classic">
      <?php echo form::checkbox($v . '_url', 1, ${$v . '_url'}); ?>
      &nbsp;<?php echo $k; ?>
      </label>
    </td>
    <td>
      <label class="classic"><?php echo __('Priority'); ?>&nbsp;
      <?php echo form::field($v . '_pr', 4, 4, ${$v . '_pr'}); ?>
      </label>
    </td>
    <td>
      <label class="classic"><?php echo __('Periodicity'); ?>&nbsp;
      <?php echo form::combo($v . '_fq', $periods, ${$v . '_fq'}); ?>
      </label>
    </td>
  </tr>
<?php endforeach;?>
  </tbody>
  </table>
  </div>

  <p><input type="hidden" name="p" value="sitemaps" />
  <?php echo dcCore::app()->formNonce(); ?>
  <input type="submit" name="saveconfig" value="<?php echo __('Save configuration'); ?>" />
<?php if ($active): ?>
  &nbsp;<input class="submit" type="submit" name="ping" value="<?php echo __('Ping search engines'); ?>" />
<?php endif;?>
  </p>
  </form>
</div>

<!-- Notifications panel -->
<div class="multi-part" id="sitemaps_notifications" title="<?php echo __('Search engines notification'); ?>">
  <form method="post" action="<?php echo $p_url; ?>">
  <div class="fieldset">
    <h4><?php echo __('Available search engines'); ?></h4>
    <table class="maximal">
      <tbody>
<?php foreach ($engines as $eng => $eng_infos): ?>
      <tr>
      <td>
        <label class="classic">
        <?php echo form::checkbox('pings[]', $eng, in_array($eng, $default_pings)); ?>
        &nbsp;<?php echo $eng_infos['name']; ?>
        </label>
      </td>
      </tr>
<?php endforeach;?>
      </tbody>
    </table>
  </div>
  <p><input type="hidden" name="p" value="sitemaps" />
  <?php echo dcCore::app()->formNonce(); ?>
  <input type="submit" name="saveprefs" value="<?php echo __('Save preferences'); ?>" />
<?php if ($active): ?>
  &nbsp;<input class="submit" type="submit" name="ping" value="<?php echo __('Ping search engines'); ?>" />
<?php endif;?>
  </p>
  </form>
</div>
<?php dcPage::helpBlock('sitemaps');?>
</body>
</html>
