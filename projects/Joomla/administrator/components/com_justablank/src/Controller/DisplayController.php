<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

namespace Adeta\Component\Justablank\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController {
    protected $default_view = 'jablank';
    
    public function display($cachable = false, $urlparams = array()) {
        return parent::display($cachable, $urlparams);
    }
    
}