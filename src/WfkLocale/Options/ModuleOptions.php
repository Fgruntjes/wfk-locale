<?php

namespace WfkLocale\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * Possible locale modes
     */
    const MODE_URLKEY = 'urlkey';
    const MODE_COOKIE = 'cookie';

    /**
     * @var string
     */
    private $mode = self::MODE_URLKEY;

    /**
     * @var array
     */
    private $enabled = array();

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $defaultRoute = 'home';

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return array
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param array $enabled
     */
    public function setEnabled(array $enabled)
    {
        $this->enabled = array();
        foreach ($enabled as $key => $locale)
        {
            $this->addLocale($key, $locale);
        }
    }

    /**
     * @param string $key
     * @param string $locale
     */
    public function addLocale($key, $locale)
    {
        $this->enabled[(string) $key] = (string) $locale;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $default
     */
    public function setDefaultLocale($default)
    {
        $this->defaultLocale = (string) $default;
    }

    /**
     * @return string
     */
    public function getDefaultRoute()
    {
        return $this->defaultRoute;
    }

    /**
     * @param string $defaultRoute
     */
    public function setDefaultRoute($defaultRoute)
    {
        $this->defaultRoute = $defaultRoute;
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getLocaleKey($locale)
    {
        if (!isset($this->enabled[$locale]))
        {
            throw new \Zend\Stdlib\Exception\InvalidArgumentException('Provided locale ' . $locale . ' is not in the enabled locale list.');
        }

        return $this->enabled[$locale];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getLocale($key)
    {
        foreach($this->getEnabled() as $locale => $searchKey)
        {
            if(strtolower($key) === strtolower($searchKey))
            {
                return $locale;
            }
        }

        throw new \Zend\Stdlib\Exception\InvalidArgumentException('Provided key ' . $searchKey . ' is not in the enabled locale list.');
    }

    /**
     * @return string
     */
    public function getDefaultLocaleKey()
    {
        return $this->getLocaleKey($this->getDefaultLocale());
    }
}