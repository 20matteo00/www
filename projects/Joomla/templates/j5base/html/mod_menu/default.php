<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

$id = '';
if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}

$templateParams = $app->getTemplate(true)->params;

$logo = $templateParams->get('logo', '');
$mobileMenu = $templateParams->get('mobile_menu', 'collapse');
?>

<nav <?= $id ?> class="navbar navbar-expand-lg navbar-light bg-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= JUri::base() ?>">
            <img src="<?= $logo ?>" alt="Logo">
        </a>

        <?php if ($mobileMenu == 'collapse'): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#modMenuNavbar"
                aria-controls="modMenuNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php else: ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainOffcanvas"
                aria-controls="mainOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php endif; ?>

        <div class="collapse navbar-collapse" id="modMenuNavbar">
            <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                <?php foreach ($list as $i => &$item):
                    $itemParams = $item->getParams();
                    $class = 'nav-item fw-bold px-3 py-1';

                    if ($item->id == $default_id) $class .= ' default';
                    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) $class .= ' current';
                    if (in_array($item->id, $path)) $class .= ' active';
                    elseif ($item->type === 'alias') {
                        $aliasToId = $itemParams->get('aliasoptions');
                        if (count($path) > 0 && $aliasToId == $path[count($path)-1]) $class .= ' active';
                        elseif (in_array($aliasToId, $path)) $class .= ' alias-parent-active';
                    }

                    $hasChildren = $item->deeper;

                    echo '<li class="' . $class . ($hasChildren ? ' dropdown' : '') . '">';

                    switch ($item->type):
                        case 'separator':
                        case 'component':
                        case 'heading':
                        case 'url':
                            require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
                            break;
                        default:
                            if ($hasChildren):
                                echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $item->title . '</a>';
                            else:
                                require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
                            endif;
                            break;
                    endswitch;

                    if ($item->deeper):
                        echo '<ul class="dropdown-menu">';
                    elseif ($item->shallower):
                        echo '</li>';
                        echo str_repeat('</ul></li>', $item->level_diff);
                    else:
                        echo '</li>';
                    endif;
                endforeach; ?>
            </ul>
        </div>
    </div>
</nav>


