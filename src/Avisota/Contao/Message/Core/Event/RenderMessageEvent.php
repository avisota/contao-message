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