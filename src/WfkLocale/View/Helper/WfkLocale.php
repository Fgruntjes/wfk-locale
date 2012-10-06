<?php

namespace WfkLocale\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Translator\Translator;
use WfkLocale\Options\ModuleOptions;
use Zend\View\Exception\RuntimeException;

class WfkLocale extends AbstractHelper
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param ModuleOptions $options
     * @param Translator $translator
     */
    public function __construct(ModuleOptions $options, Translator $translator)
    {
        $this->options = $options;
        $this->translator = $translator;
    }

    /**
     * __invoke
     *
     * @param string $name
     * @param array $extraOptions
     * @return string
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param array $options
     * @return string
     */
    public function render(array $options = array())
    {
        $translationHelper = $this->view->plugin('translate');
        $urlHelper = $this->view->plugin('url');

        $html = '<ul' . (isset($options['ulClass']) ? ' class="' . $options['ulClass'] . '"' : '') . '>' . PHP_EOL;
        foreach ($this->options->getEnabled() as $locale => $key)
        {
            $html .= '<li'.($this->translator->getLocale() === $locale ? ' class="active"' :  '').'>';
            try
            {
                $html .= '<a href="' . $urlHelper(null, array('locale' => $key), array(), true) . '">'.$translationHelper($locale, 'wfklocale').'</a>';
            }
            catch(RuntimeException $exception)
            {
                $html .= '<a href="' . $urlHelper('home', array('locale' => $key)) . '">'.$translationHelper($locale, 'wfklocale').'</a>';
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>';

        return $html;
    }
}
