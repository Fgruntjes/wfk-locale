<?php
namespace WfkLocale\Mvc\Route;

use Zend\EventManager\ListenerAggregateInterface;
use Locale;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use WfkLocale\Options\ModuleOptions;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Mvc\Router\RouteInterface;

class LocaleListener implements ListenerAggregateInterface
{
	/**
	 * @var ModuleOptions
	 */
	protected $options;

	/**
	 * @var ControllerPluginManager
	 */
	protected $pluginManager;

	/**
	 * @var RouteInterface
	 */
	protected $router;

	/**
	 * @var \Zend\Stdlib\CallbackHandler[]
	 */
	protected $listeners = array();

	/**
	 * @param ModuleOptions $options
	 * @param ControllerPluginManager $pluginManager
	 */
	public function __construct(
		ModuleOptions $options,
		RouteInterface $router,
		ControllerPluginManager $pluginManager)
	{
		$this->options = $options;
		$this->pluginManager = $pluginManager;
		$this->router = $router;
	}

	/**
	 * Attach one or more listeners
	 *
	 * Implementors may add an optional $priority argument; the EventManager
	 * implementation will pass this to the aggregate.
	 *
	 * @param EventManagerInterface $events
	 */
	public function attach(EventManagerInterface $events)
	{
		$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRouteRedirect'), 1000);
		$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRouteLocaleDetect'), -1000);
	}

	/**
	 * Detach all previously attached listeners
	 *
	 * @param EventManagerInterface $events
	 */
	public function detach(EventManagerInterface $events)
	{
		foreach ($this->listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->listeners[$index]);
			}
		}
	}

	/**
	 * @param MvcEvent $e
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function onRouteRedirect(MvcEvent $e)
	{
		/** @var $app \Zend\Mvc\Application */
		$app        = $e->getTarget();
		$match      = $app->getMvcEvent()->getRouteMatch();

		if($match !== null)
		{
			return null;
		}

		/** @var $request HttpRequest */
		$request = $e->getRequest();
		if (!($request instanceof HttpRequest))
		{
			return null;
		}

		$path = $request->getUri()->getPath();
		if($path !== '' && $path !== '/')
		{
			return null;
		}

		$e->stopPropagation();
		$redirectUrl = $this->router->assemble(array('locale' => $this->options->getDefaultLocaleKey()), array('name' => $this->options->getDefaultRoute()));
		/** @var $response HttpResponse */
		$response = $e->getResponse();
		$response->getHeaders()->addHeaderLine('Location', $redirectUrl);
		$response->setStatusCode(302);
		return $response;
	}

	/**
	 * @param MvcEvent $e
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function onRouteLocaleDetect(MvcEvent $e)
	{
		/** @var $app \Zend\Mvc\Application */
		$app        = $e->getTarget();
		$match      = $app->getMvcEvent()->getRouteMatch();

		if($match === null)
		{
			return null;
		}

		$localeMatch = $e->getRouteMatch()->getParam('locale');
		if($localeMatch === null)
		{
			return null;
		}
		$locale = $this->options->getLocale($localeMatch);

		Locale::setDefault($locale);

		return null;
	}
}