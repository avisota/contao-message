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

use Avisota\Contao\Core\Message\ContaoAwareNativeMessage;
use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\Message;
use Avisota\Recipient\RecipientInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostRenderMessageTemplateEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class PostRenderMessageTemplateEvent extends Event
{
    const NAME = 'avisota.contao.post-render-message-template';

    /**
     * @var Message
     */
    protected $contaoMessage;

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

    /**
     * @var ContaoAwareNativeMessage
     */
    protected $message;

    /**
     * PostRenderMessageTemplateEvent constructor.
     *
     * @param Message                             $contaoMessage
     * @param PreRenderedMessageTemplateInterface $messageTemplate
     * @param RecipientInterface|null             $recipient
     * @param array                               $additionalData
     * @param ContaoAwareNativeMessage            $message
     * TODO is message an sting?
     */
    public function __construct(
        Message $contaoMessage,
        PreRenderedMessageTemplateInterface $messageTemplate,
        RecipientInterface $recipient = null,
        array $additionalData = array(),
        ContaoAwareNativeMessage $message = ''
    ) {
        $this->contaoMessage   = $contaoMessage;
        $this->messageTemplate = $messageTemplate;
        $this->recipient       = $recipient;
        $this->additionalData  = $additionalData;
        $this->message         = $message;
    }

    /**
     * @return Message
     */
    public function getContaoMessage()
    {
        return $this->contaoMessage;
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
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @return ContaoAwareNativeMessage
     */
    public function getMessage()
    {
        return $this->message;
    }
}
