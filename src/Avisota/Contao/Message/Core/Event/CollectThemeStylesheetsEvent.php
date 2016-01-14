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

/**
 * Class CollectThemeStylesheetsEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class CollectThemeStylesheetsEvent extends CollectStylesheetsEvent
{
    const NAME = 'Avisota\Contao\Message\Core\Event\CollectThemeStylesheets';

    /**
     * @var array
     */
    protected $theme;

    /**
     * CollectThemeStylesheetsEvent constructor.
     *
     * @param array        $theme
     * @param \ArrayObject $stylesheets
     */
    function __construct(array $theme, \ArrayObject $stylesheets)
    {
        $this->theme = $theme;
        parent::__construct($stylesheets);
    }

    /**
     * @return array
     */
    public function getTheme()
    {
        return $this->theme;
    }
}
