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

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CollectStylesheetsEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class CollectStylesheetsEvent extends Event
{
    const NAME = 'Avisota\Contao\Message\Core\Event\CollectStylesheets';

    /**
     * @var \ArrayObject
     */
    protected $stylesheets;

    /**
     * CollectStylesheetsEvent constructor.
     *
     * @param \ArrayObject $stylesheets
     */
    public function __construct(\ArrayObject $stylesheets)
    {
        $this->stylesheets = $stylesheets;
    }

    /**
     * @return \ArrayObject
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }
}
