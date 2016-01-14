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

class PreRenderMessageTemplateEvent extends Event
{
    const NAME = 'avisota.contao.pre-render-message-template';

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

    function __construct(
        Message $contaoMessage,
        PreRenderedMessageTemplateInterface $messageTemplate,
        RecipientInterface $recipient = null,
        array $additionalData = array()
    ) {
        $this->contaoMessage   = $contaoMessage;
        $this->messageTemplate = $messageTemplate;
        $this->recipient       = $recipient;
        $this->additionalData  = $additionalData;
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
     * @param array $additionalData
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
}
