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

namespace Avisota\Contao\Message\Core\Template;

use Avisota\Contao\Core\Message\ContaoAwareNativeMessage;
use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Core\ReplaceInsertTagsHook;
use Avisota\Contao\Message\Core\Event\PostRenderMessageTemplateEvent;
use Avisota\Contao\Message\Core\Event\PostRenderMessageTemplatePreviewEvent;
use Avisota\Contao\Message\Core\Event\PreRenderMessageTemplateEvent;
use Avisota\Contao\Message\Core\Event\PreRenderMessageTemplatePreviewEvent;
use Avisota\Contao\Message\Core\Renderer\TagReplacementService;
use Avisota\Recipient\RecipientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractPostRenderingMessageTemplate implements PreRenderedMessageTemplateInterface
{
	/**
	 * @var Message
	 */
	protected $message;

	protected function __construct(Message $message)
	{
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	protected function parseContent(RecipientInterface $recipient, array $additionalData = array())
	{
		$content = $this->getContent();

		if (is_string($content)) {
			$replaceInsertTagsHook = new ReplaceInsertTagsHook();

			/** @var TagReplacementService $tagReplacementService */
			$tagReplacementService = $GLOBALS['container']['avisota.message.tagReplacementEngine'];

			$content = $tagReplacementService->parse(
				$content
			);
		}

		return $content;
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderPreview(RecipientInterface $recipient, array $additionalData = array())
	{
		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		// dispatch a pre render event
		$event = new PreRenderMessageTemplatePreviewEvent($this->message, $this, $recipient, $additionalData);
		$eventDispatcher->dispatch($event::NAME, $event);

		// fetch updates on additional data
		$additionalData = $event->getAdditionalData();

		$content = $this->parseContent($recipient, $additionalData);

		// dispatch a post render event
		$event = new PostRenderMessageTemplatePreviewEvent($this->message, $this, $recipient, $additionalData, $content);
		$eventDispatcher->dispatch($event::NAME, $event);

		$content = $event->getPreview();

		return $content;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(RecipientInterface $recipient = null, array $additionalData = array())
	{
		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		// dispatch a pre render event
		$event = new PreRenderMessageTemplateEvent($this->message, $this, $recipient, $additionalData);
		$eventDispatcher->dispatch($event::NAME, $event);

		// fetch updates on additional data
		$additionalData = $event->getAdditionalData();

		$content = $this->parseContent($recipient, $additionalData);

		$swiftMessage = new \Swift_Message();

		$name = trim($recipient->get('forename') . ' ' . $recipient->get('surname'));

		$swiftMessage->setTo($recipient->getEmail(), $name);
		$swiftMessage->setSubject($this->message->getSubject());
		$swiftMessage->setBody($content, $this->getContentType(), $this->getContentEncoding());
		$swiftMessage->setDescription($this->message->getDescription());

		$message = new ContaoAwareNativeMessage($swiftMessage, $this->message, array($recipient));

		// dispatch a post render event
		$event = new PostRenderMessageTemplateEvent($this->message, $this, $recipient, $additionalData, $message);
		$eventDispatcher->dispatch($event::NAME, $event);

		return $message;
	}
}
