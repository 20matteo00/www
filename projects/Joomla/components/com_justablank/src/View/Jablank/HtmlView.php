<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

 namespace Adeta\Component\Justablank\Site\View\Jablank;

 \defined('_JEXEC') or die;
 
 use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 use Joomla\CMS\Factory;
 
 class HtmlView extends BaseHtmlView
 {
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $document = $app->getDocument();
        $menu = $app->getMenu()->getActive();

        if ($menu)
        {
            // Get menu params
            $menuParams = $menu->getParams();
            
            // Set meta description
            $metaDesc = $menuParams->get('menu-meta_description');
            if ($metaDesc)
            {
                $document->setDescription($metaDesc);
            }

            // Set page title
            $pageTitle = $menuParams->get('page_title', $menu->title);
            if ($pageTitle)
            {
                $document->setTitle($pageTitle);
                $document->setMetaData('title', $pageTitle);
            }
        }

        return parent::display($tpl);
    }
 }