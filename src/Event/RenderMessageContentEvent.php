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

use Avisota\Contao\Entity\MessageContent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RenderMessageContentEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class RenderMessageContentEvent extends Event
{
    /**
     * @var MessageContent
     */
    protected $messageContent;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var string
     */
    protected $renderedContent;

    /**
     * RenderMessageContentEvent constructor.
     *
     * @param MessageContent $messageContent
     * @param Layout|null    $layout
     */
    public function __construct(MessageContent $messageContent, Layout $layout = null)
    {
        $this->messageContent = $messageContent;
        $this->layout         = $layout;
    }

    /**
     * @return MessageContent
     */
    public function getMessageContent()
    {
        return $this->messageContent;
    }

    /**
     * @param Layout $layout
     *
     * @return $this
     */
    public function setLayout(Layout $layout = null)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return Layout
     */
    public function getLayout()
    {
        if ($this->layout) {
            return $this->layout;
        }

        return $this->messageContent
            ->getMessage()
            ->getLayout();
    }

    /**
     * @param string $renderedContent
     *
     * @return $this
     */
    public function setRenderedContent($renderedContent)
    {
        $this->renderedContent = (string) $renderedContent;
        return $this;
    }

    /**
     * @return string
     */
    public function getRenderedContent()
    {
        return $this->renderedContent;
    }
}
