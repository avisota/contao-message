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

use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\Message;
use Avisota\Recipient\RecipientInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostRenderMessageContentEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class PostRenderMessageContentEvent extends Event
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var PreRenderedMessageTemplateInterface
     */
    protected $messageTemplate;

    /**
     * @var RecipientInterface
     */
    protected $recipient;

    /**
     * @var array
     */
    protected $additionalData;

    protected $content;

    /**
     * PostRenderMessageContentEvent constructor.
     *
     * @param Message                             $message
     * @param PreRenderedMessageTemplateInterface $messageTemplate
     * @param RecipientInterface|null             $recipient
     * @param array                               $additionalData
     * @param                                     $content
     */
    public function __construct(
        Message $message,
        PreRenderedMessageTemplateInterface $messageTemplate,
        RecipientInterface $recipient = null,
        array $additionalData = array(),
        $content
    ) {
        $this->message         = $message;
        $this->messageTemplate = $messageTemplate;
        $this->recipient       = $recipient;
        $this->additionalData  = $additionalData;
        $this->content         = $content;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return PreRenderedMessageTemplateInterface
     */
    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }

    /**
     * @return RecipientInterface
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param array $additionalData
     *
     * @return $this
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
