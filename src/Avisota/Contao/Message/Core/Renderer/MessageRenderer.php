<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Renderer;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Avisota\Recipient\RecipientInterface;
use Contao\Doctrine\ORM\EntityHelper;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MessageRenderer
{
	/**
	 * Render a complete message.
	 *
	 * @param Message            $message
	 *
	 * @return PreRenderedMessageTemplateInterface
	 */
	public function renderMessage(Message $message)
	{
		$event = new RenderMessageEvent();
		$event->setMessage($message);

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE, $event);

		return $event->getPreRenderedMessageTemplate();
	}

	/**
	 * Render content from a cell.
	 *
	 * @param Message $message
	 * @param string  $cell
	 *
	 * @return string
	 */
	public function renderCell(Message $message, $cell)
	{
		$messageContentRepository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
		$queryBuilder = $messageContentRepository->createQueryBuilder('mc');
		$queryBuilder
			->select('mc')
			->where('mc.message=:message')
			->andWhere('mc.cell=:cell')
			->setParameter('message', $message->getId())
			->setParameter('cell', $cell);
		$query = $queryBuilder->getQuery();
		$contents = $query->getResult();

		$elements = array();
		foreach ($contents as $content) {
			$elements[] = $this->renderContent($content);
		}

		return $elements;
	}

	/**
	 * Render a single message content element.
	 *
	 * @param MessageContent $messageContent
	 *
	 * @return string
	 */
	public function renderContent(MessageContent $messageContent)
	{
		$event = new RenderMessageContentEvent();
		$event->setMessageContent($messageContent);

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE_CONTENT, $event);

		return $event->getRenderedContent();
	}
}
