<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Event;

use Avisota\Contao\Message\Core\Message\Renderer;
use Symfony\Component\EventDispatcher\Event;

class ResolveStylesheetEvent extends Event
{
    const NAME = 'Avisota\Contao\Message\Core\Event\ResolveStylesheet';

    /**
     * @var string
     */
    protected $stylesheet;

    function __construct($stylesheet)
    {
        $this->stylesheet = $stylesheet;
    }

    /**
     * @param string $stylesheet
     */
    public function setStylesheet($stylesheet)
    {
        $this->stylesheet = $stylesheet;
        return $this;
    }

    /**
     * @return string
     */
    public function getStylesheet()
    {
        return $this->stylesheet;
    }
}
