<?php
defined('_JEXEC') or die;


$id = '';
if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}

$templateParams = $app->getTemplate(true)->params;
$logo = $templateParams->get('logo', '');
$mobileMenu = $templateParams->get('mobile_menu', 'collapse');
?>
<nav <?= $id ?> class="navbar navbar-expand-lg bg-primary text-secondary">
    <div class="container">
        <a class="navbar-brand" href="<?= JUri::base() ?>">
            <img src="<?= $logo ?>" alt="Logo">
        </a>
        <?php if ($mobileMenu == 'collapse'): ?>
            <button class="navbar-toggler bg-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#modMenuNavbar" aria-controls="modMenuNavbar" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php else: ?>
            <button class="navbar-toggler bg-secondary" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mainOffcanvas" aria-controls="mainOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php endif; ?>
        <div class="collapse navbar-collapse" id="modMenuNavbar">
            <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                <?php
                foreach ($list as $i => &$item):
                    $itemParams = $item->getParams();
                    $class = 'nav-item fw-bold my-auto';

                    if ($item->level == 1) $class .= ' px-3 py-1';
                    if ($item->id == $default_id) $class .= ' default';
                    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) $class .= ' current';
                    if (in_array($item->id, $path)) $class .= ' active';
                    elseif ($item->type === 'alias') {
                        $aliasToId = $itemParams->get('aliasoptions');
                        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) $class .= ' active';
                        elseif (in_array($aliasToId, $path)) $class .= ' alias-parent-active';
                    }

                    $hasChildren = $item->deeper;
                    if ($hasChildren && $item->level == 1) $class .= ' dropdown d-inline-flex';

                    echo '<li class="' . $class . '">';

                    $linkClass = $item->level == 1 ? 'nav-link' : 'dropdown-item';
                    $indent = $item->level > 1 ? ' style="padding-left: ' . (($item->level - 1) * 1.5) . 'rem;"' : '';
                    $hasUrl = !empty($item->flink) && $item->flink != '#';

                    if ($hasChildren && $item->level == 1):
                        if ($hasUrl):
                            echo '<a class="' . $linkClass . '" href="' . $item->flink . '">' . $item->title . '</a>';
                            echo '<a class="' . $linkClass . ' dropdown-toggle dropdown-toggle-split px-2" href="#" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle</span></a>';
                        else:
                            echo '<a class="' . $linkClass . ' dropdown-toggle" href="#" data-bs-toggle="dropdown">' . $item->title . '</a>';
                        endif;
                    elseif ($hasUrl):
                        echo '<a class="' . $linkClass . '"' . $indent . ' href="' . $item->flink . '">' . $item->title . '</a>';
                    else:
                        echo '<span class="' . $linkClass . '"' . $indent . '>' . $item->title . '</span>';
                    endif;

                    if ($item->deeper) {
                        echo $item->level == 1 ? '<ul class="dropdown-menu bg-quaternary">' : '<ul class="list-unstyled bg-quaternary">';
                    }
                    if ($item->shallower) {
                        echo '</li>';
                        if ($item->level_diff > 0) echo str_repeat('</ul></li>', $item->level_diff);
                    } else {
                        echo '</li>';
                    }
                endforeach;
                ?>
            </ul>
        </div>
    </div>
</nav>
