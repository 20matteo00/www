<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

namespace Adeta\Component\Justablank\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController {
    
    public function display($cachable = false, $urlparams = array()) {        
        $document = Factory::getDocument();
        $viewName = $this->input->getCmd('view', 'jablank');
        $viewFormat = $document->getType();
        
        $view = $this->getView($viewName, $viewFormat);
        
        $view->document = $document;
        $view->display();
    }
    
}