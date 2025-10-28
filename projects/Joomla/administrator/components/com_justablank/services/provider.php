<?php
/* @package    Just a Blank FREE Component
 * @author     ADETA, info@adeta.lv
 * @copyright  (C) 2024 dev.adeta.lv 
 * @license    GNU General Public License version 2 or later; see LICENSE.txt */

\defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Adeta\Component\Justablank\Administrator\Extension\JustablankComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
    
    public function register(Container $container) {
        $container->registerServiceProvider(new MVCFactory('\\Adeta\\Component\\Justablank'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Adeta\\Component\\Justablank'));
        $container->registerServiceProvider(new RouterFactory('\\Adeta\\Component\\Justablank'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new JustablankComponent($container->get(ComponentDispatcherFactoryInterface::class));

                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));

                return $component;
            }
        );
    }
};