<?php
namespace WfkLocale;

use Zend\Loader\AutoloaderFactory;
use Zend\EventManager\EventInterface;
use Zend\Loader\StandardAutoloader;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface,
    BootstrapListenerInterface
{

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'wfklocale-options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    return new Options\ModuleOptions(isset($config['wfklocale']) ? $config['wfklocale'] : array());
                },

                'wfklocale-redirectlistener' => function (ServiceLocatorInterface $sm) {
                    return new Mvc\Route\LocaleListener(
                        $sm->get('wfklocale-options'),
                        $sm->get('Router'),
                        $sm->get('ControllerPluginManager')
                    );
                },
                'Router' => 'WfkLocale\Service\RouterFactory',
                'Config' => 'WfkLocale\Service\ConfigFactory',
            ),
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'wfkLocale' => function (ServiceLocatorInterface $sm) {
                    $locator = $sm->getServiceLocator();
                    return new View\Helper\WfkLocale(
                        $locator->get('wfklocale-options'),
                        $locator->get('MvcTranslator')
                    );
                },
            ),
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getTarget();
        $sm = $app->getServiceManager();
        $redirectListener = $sm->get('wfklocale-redirectlistener');

        $app->getEventManager()->attach($redirectListener);
    }
}