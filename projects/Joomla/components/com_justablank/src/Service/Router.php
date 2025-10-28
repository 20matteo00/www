<?php 
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

namespace Adeta\Component\Justablank\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;

\defined('_JEXEC') or die;

class Router extends RouterView 
{
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
        $jablank = new RouterViewConfiguration('jablank');
        $this->registerView($jablank);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    public function build(&$query)
    {
        $segments = array();
        
        // Always remove view parameter from URL
        if (isset($query['view']))
        {
            unset($query['view']);
        }
        
        return $segments;
    }

    public function parse(&$segments)
    {
        $vars = array();
        $vars['view'] = 'jablank';
        
        return $vars;
    }
}