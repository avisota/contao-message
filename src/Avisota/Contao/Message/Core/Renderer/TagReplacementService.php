<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Renderer;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageContent;

class TagReplacementService
{
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    function __construct(\Twig_Environment $twigEnvironment = null)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param \Twig_Environment $twigEnvironment
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

    public function parse($buffer, $context = array())
    {
        /** @var \Twig_Loader_Array $loader */
        $loader = $this->twigEnvironment->getLoader();
        $loader->setTemplate('__TEMPLATE__', $buffer);

        return $this->twigEnvironment->render('__TEMPLATE__', $context);
    }
}
