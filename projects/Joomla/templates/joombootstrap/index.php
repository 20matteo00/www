<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.templatebaseld
 *
 * @copyright   (C) YEAR Your Name
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * This is a heavily stripped down/modified version of the default Cassiopeia template, designed to build new templates off of.
 */

defined('_JEXEC') or die;  //required for basically ALL php files in Joomla, for security. Prevents direct access to this file by url.

//Imports ("use" statements) - objects from Joomla that we want to use in this file
use Joomla\CMS\Factory; // Factory class: Contains static methods to get global objects from the Joomla framework. Very important!
use Joomla\CMS\HTML\HTMLHelper; // HTMLHelper class: Contains static methods to generate HTML tags.
use Joomla\CMS\Language\Text; // Text class: Contains static methods to get text from language files
use Joomla\CMS\Uri\Uri; // Uri class: Contains static methods to manipulate URIs.

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$user = Factory::getUser();

$wa = $this->getWebAssetManager();  // Get the Web Asset Manager - used to load our CSS and JS files

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');

$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$hasClass = '';
$homeLink = Uri::base();

$templateParams = $app->getTemplate(true)->params;
$favicon = $templateParams->get('favicon', '');
$logo = $templateParams->get('logo', '');
$colors = $templateParams->get('colors', []);
$sticky = $templateParams->get('sticky', 1);

if ($sticky == 1)
    $sticky_class = "sticky-top";
else
    $sticky_class = "";

/* STILI */
$wa->useStyle('bootstrap-css');
$wa->useStyle('bootstrap-ico');
$wa->useStyle('searchtools');
$wa->useStyle('fontawesome');
$wa->useStyle('user-css');

/* SCRIPT */
HTMLHelper::_('jquery.framework');
$wa->useScript('bootstrap-js');
$wa->useScript('user-js');

//Set viewport meta tag for mobile responsiveness -- very important for scaling on mobile devices
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');



?>

<?php // Everything below here is the actual "template" part of the template. Where we put our HTML code for the layout and such. ?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

<head>

    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
    <link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon">
    <style>
        :root {
            <?php foreach ($colors as $i => $color) {
                $value = $color->colors->value;
                echo "--{$i}: {$value};";
            } ?>
        }
    </style>
</head>

<body class="site <?php echo $option
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($pageclass ? ' ' . $pageclass : '')
    . $hasClass
    . ($this->direction == 'rtl' ? ' rtl' : '');
?>" data-bs-theme="light">

    <!-- HEADER -->
    <div class="header-top">
        <jdoc:include type="modules" name="header-top" style="none" />
    </div>
    <div class="header-content <?= $sticky_class ?>">
        <jdoc:include type="modules" name="header-content" style="none" />
    </div>
    <div class="header-bottom">
        <jdoc:include type="modules" name="header-bottom" style="none" />
    </div>

    <!-- MESSAGE -->
    <div class="message">
        <jdoc:include type="message" />
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <jdoc:include type="modules" name="breadcrumb" style="none" />
    </div>

    <!-- MAIN CONTENT -->
    <main class="main" id="main" role="main">
        <div class="content-top">
            <jdoc:include type="modules" name="content-top" style="none" />
        </div>
        <div class="content container">
            <div class="row">
                <?php if ($this->countModules('sidebar-left')): ?>
                    <aside class="col-auto">
                        <jdoc:include type="modules" name="sidebar-left" style="none" />
                    </aside>
                <?php endif; ?>

                <div class="col">
                    <jdoc:include type="component" />
                </div>

                <?php if ($this->countModules('sidebar-right')): ?>
                    <aside class="col-auto">
                        <jdoc:include type="modules" name="sidebar-right" style="none" />
                    </aside>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-bottom">
            <jdoc:include type="modules" name="content-bottom" style="none" />
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer" id="footer" role="contentinfo">
        <div class="footer-top">
            <jdoc:include type="modules" name="footer-top" style="none" />
        </div>
        <div class="footer-content">
            <jdoc:include type="modules" name="footer-content" style="none" />
        </div>
        <div class="footer-bottom">
            <jdoc:include type="modules" name="footer-bottom" style="none" />
        </div>
    </footer>
    <?php if ($user->authorise('core.admin')): ?>
        <jdoc:include type="modules" name="debug" style="none" />
    <?php endif; ?>
</body>

</html>