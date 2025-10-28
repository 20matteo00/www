<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Layout\LayoutHelper;

/** @var \Joomla\Component\Content\Site\View\Category\HtmlView $this */

$jcfields = $this->item->jcfields;

// Mappiamo i campi
$campi = [];
foreach ($jcfields as $field) {
    $campi[$field->id] = [
        'value' => $field->value,
        'label' => $field->title,
        'raw' => $field->rawvalue
    ];
}


// Recuperiamo i valori
$compagno = $campi[1]['value'] ?? $campi[2]['value'] ?? null;
$libro = $campi[3]['value'] ?? null;
$potere = $campi[4]['value'] ?? null;

$compagno = extractACFArticles($compagno);
$img = json_decode($this->item->images)->image_intro ?? '';
$intro = $this->item->introtext ?? '';
?>

<div class="card h-100">
    <div class="card-header text-center">
        <?= LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
    </div>
    <div class="card-body d-flex flex-column">
        <?php if ($img != ''): ?>
            <div class="image">
                <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
            </div>
        <?php endif; ?>
        <?php if ($intro != ''): ?>
            <div class="intro">
                <?= $this->item->introtext; ?>
            </div>
        <?php endif; ?>
        <div class="campi mt-auto">
            <?php if ($compagno): ?>
                <div class="compagno">
                    <strong>Compagno: </strong>
                    <?php foreach ($compagno as $c): ?>
                        <span><a href="<?= $c['href'] ?? '#' ?>"><?= $c['name'] ?? '' ?></a></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($potere): ?>
                <div class="potere">
                    <strong>Poteri: </strong>
                    <span><?= $potere ?></span>
                </div>
            <?php endif; ?>
            <?php if ($libro): ?>
                <div class="libro">
                    <strong>Colore Libro: </strong>
                    <span class="colore-libro"
                        style="background-color: <?= htmlspecialchars($libro, ENT_QUOTES); ?>;"></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer text-center">
    </div>
</div>