<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

namespace Adeta\Component\Justablank\Administrator\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\MVCComponent;

class JustablankComponent extends MVCComponent implements RouterServiceInterface {
    use RouterServiceTrait;
}