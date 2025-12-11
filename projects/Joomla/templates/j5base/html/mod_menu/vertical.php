<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
$app = Factory::getApplication();
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$id = '';
if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}
?>

<nav <?= $id ?> class="navbar bg-primary text-secondary">
    <div class="container">
        <ul class="navbar-nav mb-2 mb-lg-0">

            <?php foreach ($list as $i => &$item):

                $itemParams = $item->getParams();
                $class = 'nav-item fw-bold my-auto';

                // Active & path
                $isActive = ($item->id == $active_id || in_array($item->id, $path));
                if ($isActive)
                    $class .= ' active';

                $hasChildren = $item->deeper;

                echo '<li class="' . $class . '">';

                // indentazione
                $indent = $item->level > 1 ? ' style="padding-left:' . (($item->level - 1) * 1.5) . 'rem;"' : '';

                // toggle se figli
                $toggle = $hasChildren ? '<span class="menu-toggle ms-1">▸</span>' : '';

                // link o span
                if (!empty($item->flink) && $item->flink != '#') {
                    echo '<a class="nav-link d-inline-block"' . $indent . ' href="' . $item->flink . '">' . $item->title . '</a>' . $toggle;
                } else {
                    echo '<span class="nav-link d-inline-block"' . $indent . '>' . $item->title . '</span>' . $toggle;
                }

                // lista figli
                if ($hasChildren) {
                    $isOpen = in_array($item->id, $path) ? ' style="display:block;"' : ' style="display:none;"';
                    echo '<ul class="list-unstyled sub-menu"' . $isOpen . '>';
                }

                // closing
                if ($item->shallower) {
                    echo '</li>';
                    echo str_repeat('</ul></li>', $item->level_diff);
                } else {
                    echo '</li>';
                }

            endforeach; ?>

        </ul>
    </div>
</nav>
<?php
$script = '
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".nav-item").forEach(item => {
        const toggle = item.querySelector(".menu-toggle");
        const submenu = item.querySelector(".sub-menu");

        if (!toggle || !submenu) return;

        // stato iniziale coerente
        const isOpen = submenu.style.display === "block";
        toggle.textContent = isOpen ? "▾" : "▸";

        toggle.addEventListener("click", function(e) {
            e.preventDefault();

            const currentlyOpen = submenu.style.display === "block";
            submenu.style.display = currentlyOpen ? "none" : "block";
            toggle.textContent = currentlyOpen ? "▸" : "▾";
        });
    });

});
';
$wa->addInlineScript($script);
?>
