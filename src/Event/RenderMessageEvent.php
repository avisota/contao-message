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
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RenderMessageEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RenderMessageEvent extends Event
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var PreRenderMessageTemplateEvent
     */
    protected $preRenderedMessageTemplate;

    /**
     * RenderMessageEvent constructor.
     *
     * @param Message     $message
     * @param Layout|null $layout
     */
    public function __construct(Message $message, Layout $layout = null)
    {
        $this->message = $message;
        $this->layout  = $layout;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
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

        return $this->message
            ->getLayout();
    }

    /**
     * @param PreRenderMessageTemplateEvent $preRenderedMessageTemplate
     *
     * @return $this
     */
    public function setPreRenderedMessageTemplate($preRenderedMessageTemplate)
    {
        $this->preRenderedMessageTemplate = $preRenderedMessageTemplate;
        return $this;
    }

    /**
     * @return PreRenderMessageTemplateEvent
     */
    public function getPreRenderedMessageTemplate()
    {
        return $this->preRenderedMessageTemplate;
    }
}
