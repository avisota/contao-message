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

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Message\Core\Message\Renderer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GenerateViewOnlineUrlEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
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

    /**
     * GenerateViewOnlineUrlEvent constructor.
     *
     * @param Message $message
     * @param         $url
     */
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
