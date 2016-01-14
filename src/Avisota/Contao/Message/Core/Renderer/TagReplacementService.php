<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Renderer;


/**
 * Class TagReplacementService
 *
 * @package Avisota\Contao\Message\Core\Renderer
 */
class TagReplacementService
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * TagReplacementService constructor.
     *
     * @param \Twig_Environment|null $twigEnvironment
     */
    function __construct(\Twig_Environment $twigEnvironment = null)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     *
     * @return $this
     */
    public function setTwigEnvironment($twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        return $this;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    /**
     * @param       $buffer
     * @param array $context
     *
     * @return string
     */
    public function parse($buffer, $context = array())
    {
        /** @var \Twig_Loader_Array $loader */
        $loader = $this->twigEnvironment->getLoader();
        $loader->setTemplate('__TEMPLATE__', $buffer);

        return $this->twigEnvironment->render('__TEMPLATE__', $context);
    }
}
