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

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Message\Core\Message\Renderer;
use Symfony\Component\EventDispatcher\Event;

class RenderMessageEvent extends Event
{
	/**
	 * @var Message
	 */
	protected $message;

	/**
	 * @var PreRenderMessageTemplateEvent
	 */
	protected $preRenderedMessageTemplate;

	/**
	 * @param Message $message
	 */
	public function setMessage(Message $message)
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * @return Message
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param \Avisota\Contao\Message\Core\Event\PreRenderMessageTemplateEvent $preRenderedMessageTemplate
	 */
	public function setPreRenderedMessageTemplate($preRenderedMessageTemplate)
	{
		$this->preRenderedMessageTemplate = $preRenderedMessageTemplate;
		return $this;
	}

	/**
	 * @return \Avisota\Contao\Message\Core\Event\PreRenderMessageTemplateEvent
	 */
	public function getPreRenderedMessageTemplate()
	{
		return $this->preRenderedMessageTemplate;
	}
}