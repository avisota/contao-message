<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Event;

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Message\Core\Message\Renderer;
use Symfony\Component\EventDispatcher\Event;

class GenerateViewOnlineUrlEvent extends Event
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $url;

    public function __construct(Message $message, $url)
    {
        $this->message = $message;
        $this->url     = $url;;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return GenerateViewOnlineUrlEvent
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;
        return $this;
    }
}
