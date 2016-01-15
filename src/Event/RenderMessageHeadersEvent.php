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

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Message\Core\Renderer\MessageRendererInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RenderMessageHeadersEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class RenderMessageHeadersEvent extends Event
{
    const NAME = 'Avisota\Contao\Message\Core\Event\RenderMessageHeaders';

    /**
     * @var MessageRendererInterface
     */
    protected $renderer;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var \ArrayObject
     */
    protected $headers;

    /**
     * RenderMessageHeadersEvent constructor.
     *
     * @param MessageRendererInterface $renderer
     * @param                          $message
     * @param                          $headers
     */
    function __construct(MessageRendererInterface $renderer, $message, $headers)
    {
        $this->renderer = $renderer;
        $this->message  = $message;
        $this->headers  = $headers;
    }

    /**
     * @return MessageRendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return \Avisota\Contao\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \ArrayObject
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
