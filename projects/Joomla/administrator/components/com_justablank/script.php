<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt  */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

class com_justablankInstallerScript {
    public function __construct() {
        $this->minimumPhp = '7.2.5';
        $this->minimumJoomla = '4.0';
    }
    function preflight(string $type, $parent): bool {
        if (!empty($this->minimumPhp) && version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JUSTABLANK_INSTALLERSCRIPT_PHP_VERSION_ERROR', PHP_VERSION, $parent->getManifest()->name, $this->minimumPhp), 'error');
			return false;
        }
        if (!empty($this->minimumJoomla) && version_compare(JVERSION, $this->minimumJoomla, '<')) {
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JUSTABLANK_INSTALLERSCRIPT_JOOMLA_VERSION_ERROR', JVERSION, $parent->getManifest()->name, $this->minimumJoomla), 'error');
            return false;
        }
        return true;
    }
    function install($parent): bool {
        echo '<div><p>' . Text::sprintf('COM_JUSTABLANK_INSTALLERSCRIPT_INSTALL', $parent->getManifest()->name, $parent->getManifest()->version) . '<a href="https://' . $parent->getManifest()->authorUrl . '" target="blank" rel="noopener">' . $parent->getManifest()->authorUrl . '</a></p></div>';
            return true;
    }
    function update($parent): bool {
        echo '<div><p>' . Text::sprintf('COM_JUSTABLANK_INSTALLERSCRIPT_UPDATE', $parent->getManifest()->name, $parent->getManifest()->version) . '<a href="https://' . $parent->getManifest()->authorUrl . '" target="blank" rel="noopener">' . $parent->getManifest()->authorUrl . '</a></p></div>';
        return true;
    }
    function uninstall($parent): bool {
        echo '<div><p>' . Text::sprintf('COM_JUSTABLANK_INSTALLERSCRIPT_UNINSTALL', $parent->getManifest()->name) . '</p></div>';
        return true;
    }
    function postflight(string $type, $parent): bool {
        echo '<div><p>' . Text::_('COM_JUSTABLANK_INSTALLERSCRIPT_POSTFLIGHT') . '</p></div>';
        return true;
    }
}