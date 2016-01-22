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
 * Class PostRenderMessageTemplatePreviewEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class PostRenderMessageTemplatePreviewEvent extends Event
{
    const NAME = 'avisota.contao.post-render-message-template-preview';

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
     * @var string
     */
    protected $preview;

    /**
     * PostRenderMessageTemplatePreviewEvent constructor.
     *
     * @param Message                             $contaoMessage
     * @param PreRenderedMessageTemplateInterface $messageTemplate
     * @param RecipientInterface|null             $recipient
     * @param array                               $additionalData
     * @param                                     $preview
     * TODO is recipient standard value?
     */
    public function __construct(
        Message $contaoMessage,
        PreRenderedMessageTemplateInterface $messageTemplate,
        RecipientInterface $recipient = null,
        array $additionalData = array(),
        $preview = ''
    ) {
        $this->contaoMessage   = $contaoMessage;
        $this->messageTemplate = $messageTemplate;
        $this->recipient       = $recipient;
        $this->additionalData  = $additionalData;
        $this->preview         = $preview;
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
     * @param string $preview
     *
     * @return $this
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }
}
