<?php
namespace WfkLocale\Mvc\Route;

use RuntimeException;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\RouteMatch as HttpRouteMatch;
use WfkLocale\Options\ModuleOptions;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Http\Request as HttpRequest;

class LocaleRouteStack extends TreeRouteStack implements TranslatorAwareInterface
{
    /**
     * @var RouteStackInterface
     */
    protected $originalRouteStack;

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @param RouteStackInterface $originalRouteStack
     * @param ModuleOptions $options
     */
    public function __construct(RouteStackInterface $originalRouteStack, ModuleOptions $options)
    {
        $this->originalRouteStack = $originalRouteStack;
        $this->options = $options;
    }

    /**
     * Create a new route with given options.
     *
     * @param  array|\Traversable $options
     * @return void
     */
    public static function factory($options = array())
    {
        throw new \Zend\Stdlib\Exception\BadMethodCallException('Not implemented');
    }

    /**
     * Match a given request.
     *
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request)
    {
        // Extract last locale from request
        if($request instanceof HttpRequest)
        {
            $possibleLocales = $this->options->getEnabled();
            $possibleLocales = array_unique(array_values($possibleLocales));

            /** @var $request HttpRequest */
            if (preg_match('/^\/(?<locale>(' . implode('|', $possibleLocales) . '))/i', $request->getUri()->getPath(), $matches))
            {
                $this->currentLocale = $matches['locale'];
            }
        }
        // Return regular route match
        $match = $this->originalRouteStack->match($request);

        if($match instanceof HttpRouteMatch)
        {
            if(substr($match->getMatchedRouteName(), 0, 15) == 'wfklocale-root/')
            {
                $newMatch = new HttpRouteMatch(array());
                $newMatch->setMatchedRouteName(substr($match->getMatchedRouteName(), 15));
                $match->merge($newMatch);
            }
        }

        return $match;
    }

    /**
     * Assemble the route.
     *
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
		try
		{
			return $this->originalRouteStack->assemble($params, $options);
		}
		catch(\Zend\Mvc\Router\Exception\RuntimeException $e)
		{ }

        if (isset($options['name']) && substr($options['name'], 0, 14) !== 'wfklocale-root')
        {
            $options['name'] = 'wfklocale-root/' . $options['name'];
        }

        if (!isset($params['locale']))
        {
            $params['locale'] = $this->getCurrentLocale();
        }
		return $this->originalRouteStack->assemble($params, $options);
    }

    /**
     * Add a route to the stack.
     *
     * @param  string  $name
     * @param  mixed   $route
     * @param  integer $priority
     * @return RouteStackInterface
     */
    public function addRoute($name, $route, $priority = null)
    {
        $this->originalRouteStack->addRoute($name, $route, $priority);
        return $this;
    }

    /**
     * Add multiple routes to the stack.
     *
     * @param  array|\Traversable $routes
     * @return RouteStackInterface
     */
    public function addRoutes($routes)
    {
        $this->originalRouteStack->addRoutes($routes);
        return $this;
    }

    /**
     * Remove a route from the stack.
     *
     * @param  string $name
     * @return RouteStackInterface
     */
    public function removeRoute($name)
    {
        $this->originalRouteStack->removeRoute($name);
        return $this;
    }

    /**
     * Remove all routes from the stack and set new ones.
     *
     * @param  array|\Traversable $routes
     * @return RouteStackInterface
     */
    public function setRoutes($routes)
    {
        $this->originalRouteStack->setRoutes($routes);
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        if($this->currentLocale === null)
        {
            return $this->options->getDefaultLocale();
        }
        return $this->currentLocale;
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslator(Translator $translator = null, $textDomain = null)
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            $original->setTranslator($translator, $textDomain);
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslator()
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            return $original->getTranslator();
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasTranslator()
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            return $original->hasTranslator();
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslatorEnabled($enabled = true)
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            $original->setTranslatorEnabled($enabled);
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatorEnabled()
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            return $original->isTranslatorEnabled();
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            $original->setTranslatorTextDomain($textDomain);
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatorTextDomain()
    {
        $original = $this->originalRouteStack;
        if($original instanceof TranslatorAwareInterface)
        {
            return $original->getTranslatorTextDomain();
        }
        else
        {
            throw new RuntimeException('Method not implemented.');
        }
    }
}
