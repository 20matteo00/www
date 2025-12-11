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
use Joomla\CMS\Uri\Uri; // Uri class: Contains static methods to manipulate URIs.


$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();
$menu = $app->getMenu()->getActive();

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$hasClass = '';

$bodyClass = [
    'site',
    $option,
    'view-' . $view,
    $layout ? 'layout-' . $layout : 'no-layout',
    $task ? 'task-' . $task : 'no-task',
    $itemid ? 'itemid-' . $itemid : '',
    $pageclass ?: '',
    $hasClass,
    $this->direction === 'rtl' ? 'rtl' : ''
];

$bodyClass = implode(' ', array_filter($bodyClass));


$templateParams = $app->getTemplate(true)->params;
// Retrieve the logo file and secondary color from the params
$favicon = $templateParams->get('favicon', '');
$logo = $templateParams->get('logo', '');
$colors = $templateParams->get('colors', []);
$mobileMenu = $templateParams->get('mobile_menu', 'collapse');
$lato_menu = $templateParams->get('lato_menu', 'left');
$container = $templateParams->get('container', 'container');

$wa->usePreset('core');
$wa->usePreset('bootstrap');
$wa->usePreset('user');

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
    <?php
    $css = ":root {";

    $i = 1;
    foreach ($colors as $color) {
        $value = $color->colors->value;
        $css .= "--c{$i}: {$value};";
        $i++;
    }

    $css .= "}";

    $wa->addInlineStyle($css);
    ?>


</head>

<body class="<?= $bodyClass ?>" data-bs-theme="light">

    <?php // HEADER ?>
    <?php if ($this->countModules('header-top')): ?>
        <div class="header-top">
            <jdoc:include type="modules" name="header-top" style="none" />
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('header-sticky')): ?>
        <div class="position-sticky sticky-top">
            <jdoc:include type="modules" name="header-sticky" style="none" />
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('header-content')): ?>
        <div class="header-content">
            <jdoc:include type="modules" name="header-content" style="none" />
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('header-bottom')): ?>
        <div class="header-bottom">
            <jdoc:include type="modules" name="header-bottom" style="none" />
        </div>
    <?php endif; ?>

    <?php // OFFCANVAS (mobile menu) ?>
    <?php if ($this->countModules('offcanvas') && $mobileMenu == 'offcanvas'): ?>
        <div class="offcanvas offcanvas-<?= $lato_menu ?>" tabindex="-1" id="mainOffcanvas"
            aria-labelledby="mainOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mainOffcanvasLabel">
                    <a class="navbar-brand" href="<?= Uri::root() ?>">
                        <img src="<?= $logo ?? '' ?>" alt="Logo">
                    </a>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <jdoc:include type="modules" name="offcanvas" style="none" />
            </div>
        </div>
    <?php endif; ?>

    <?php // MAIN CONTENT ?>
    <main class="main" id="main" role="main">
        <div class="<?= $container ?>">
            <?php // BREADCRUMB ?>
            <?php if ($this->countModules('breadcrumbs')): ?>
                <div class="breadcrumbs">
                    <jdoc:include type="modules" name="breadcrumbs" style="none" />
                </div>
            <?php endif; ?>

            <jdoc:include type="message" />

            <?php if ($this->countModules('content-top')): ?>
                <div class="content-top">
                    <jdoc:include type="modules" name="content-top" style="none" />
                </div>
            <?php endif; ?>
            <section class="content">
                <div class="row">
                    <?php if ($this->countModules('sidebar-left')): ?>
                        <aside class="col-auto">
                            <jdoc:include type="modules" name="sidebar-left" style="none" />
                        </aside>
                    <?php endif; ?>

                    <div class="col">
                        <jdoc:include type="modules" name="component-top" style="none" />
                        <jdoc:include type="component" />
                        <jdoc:include type="modules" name="component-bottom" style="none" />
                    </div>

                    <?php if ($this->countModules('sidebar-right')): ?>
                        <aside class="col-auto">
                            <jdoc:include type="modules" name="sidebar-right" style="none" />
                        </aside>
                    <?php endif; ?>
                </div>
            </section>

            <?php if ($this->countModules('content-bottom')): ?>
                <div class="content-bottom">
                    <jdoc:include type="modules" name="content-bottom" style="none" />
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php // FOOTER ?>
    <footer class="footer" id="footer" role="contentinfo">

        <?php if ($this->countModules('footer-top')): ?>
            <div class="footer-top">
                <jdoc:include type="modules" name="footer-top" style="none" />
            </div>
        <?php endif; ?>

        <?php if ($this->countModules('footer-content')): ?>
            <div class="footer-content">
                <jdoc:include type="modules" name="footer-content" style="none" />
            </div>
        <?php endif; ?>

        <?php if ($this->countModules('footer-bottom')): ?>
            <div class="footer-bottom">
                <jdoc:include type="modules" name="footer-bottom" style="none" />
            </div>
        <?php endif; ?>

    </footer>
    <jdoc:include type="modules" name="debug" style="none" />
</body>

</html>