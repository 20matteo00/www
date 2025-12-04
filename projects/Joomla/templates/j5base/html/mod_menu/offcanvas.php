<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

$id = '';
if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}
?>

<!-- Menu verticale per offcanvas -->
<ul <?php echo $id; ?> class="nav flex-column">
    <?php foreach ($list as $i => &$item):
        $itemParams = $item->getParams();
        $class = 'nav-item';

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

        if ($item->deeper) {
            $class .= ' dropdown';
        }

        echo '<li class="' . $class . '">';

        // Link o separator
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

        // Figli / dropdown
        if ($item->deeper):
            echo '<ul class="dropdown-menu ps-3">';
        elseif ($item->shallower):
            echo '</li>';
            echo str_repeat('</ul></li>', $item->level_diff);
        else:
            echo '</li>';
        endif;
    endforeach; ?>
</ul>
