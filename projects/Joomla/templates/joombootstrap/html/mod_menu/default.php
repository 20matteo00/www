<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory; // Factory class: Contains static methods to get global objects from the Joomla framework. Very important!
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;
$base = Uri::base(true); // base del sito senza slash finale
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('mod_menu', 'mod_menu/menu.min.js', [], ['type' => 'module']);

$app = Factory::getApplication();
$siteName = $app->get('sitename');
$templateParams = $app->getTemplate(true)->params;
$logo = $templateParams->get('logo', '');
if ($logo == '') {
}
$id = '';

if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}

// The menu class is deprecated. Use mod-menu instead
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <?php if ($logo == ''): ?>
            <a class="navbar-brand" href="<?= $base ?>"><?= $siteName ?></a>
        <?php else: ?>
            <a class="navbar-logo" href="<?= $base ?>"><img src="<?= $logo ?>" alt="<?= $siteName ?>"></a>
        <?php endif; ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul <?php echo $id; ?> class="mod-menu mod-list nav <?php echo $class_sfx; ?>">
                <?php
                foreach ($list as $i => &$item) {
                    $itemParams = $item->getParams();
                    $class = 'nav-item item-' . $item->id;

                    if ($item->id == $default_id) {
                        $class .= ' default';
                    }

                    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
                        $class .= ' current';
                    }

                    if (in_array($item->id, $path)) {
                        $class .= ' active';
                    } elseif ($item->type === 'alias') {
                        $aliasToId = $itemParams->get('aliasoptions');

                        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                            $class .= ' active';
                        } elseif (in_array($aliasToId, $path)) {
                            $class .= ' alias-parent-active';
                        }
                    }

                    if ($item->type === 'separator') {
                        $class .= ' divider';
                    }

                    if ($item->deeper) {
                        $class .= ' deeper';
                    }

                    if ($item->parent) {
                        $class .= ' parent';
                    }

                    echo '<li class="' . $class . '">';

                    switch ($item->type):
                        case 'separator':
                        case 'component':
                        case 'heading':
                        case 'url':
                            require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
                            break;

                        default:
                            require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
                            break;
                    endswitch;

                    // The next item is deeper.
                    if ($item->deeper) {
                        echo '<ul class="mod-menu__sub list-unstyled small">';
                    } elseif ($item->shallower) {
                        // The next item is shallower.
                        echo '</li>';
                        echo str_repeat('</ul></li>', $item->level_diff);
                    } else {
                        // The next item is on the same level.
                        echo '</li>';
                    }
                }
                ?>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>