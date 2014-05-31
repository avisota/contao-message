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

namespace Avisota\Contao\Message\Core\Module;

use Avisota\Contao\Core\CoreEvents;
use Avisota\Contao\Core\Event\CreatePublicEmptyRecipientEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Avisota\Contao\Message\Core\Renderer\MessageRenderer;
use Avisota\Contao\Message\Core\Renderer\TagReplacementService;
use Contao\Doctrine\ORM\EntityHelper;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Reader extends \TwigModule
{
	/**
	 * {@inheritdoc}
	 */
	protected $strTemplate = 'avisota/frontend/module/mod_reader';

	/**
	 * {@inheritdoc}
	 */
	protected function compile()
	{
		if (TL_MODE == 'BE') {
			return;
		}

		if ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
			$messageAlias = \Input::getInstance()->get('auto_item');
		}
		else {
			$messageAlias = \Input::getInstance()->get('items');
		}

		$repository   = EntityHelper::getRepository('Avisota\Contao:Message');
		$queryBuilder = $repository->createQueryBuilder('m');
		$expr         = $queryBuilder->expr();
		$queryBuilder
			->innerJoin('m.category', 'c')
			->where($expr->eq(is_numeric($messageAlias) ? 'm.id' : 'm.alias', ':alias'))
			->andWhere($expr->in('c.id', ':categories'))
			->andWhere($expr->gt('m.sendOn', 0))
			->orderBy('m.sendOn', 'DESC')
			->setParameter('alias', $messageAlias)
			->setParameter('categories', deserialize($this->avisota_message_categories, true));
		$query = $queryBuilder->getQuery();

		try {
			$message = $query->getSingleResult();

			$repository = EntityHelper::getRepository('Avisota\Contao:Layout');
			$layout     = $repository->find($this->avisota_message_layout);

			$cells    = deserialize($this->avisota_message_cell, true);
			$contents = array();

			/** @var EventDispatcherInterface $eventDispatcher */
			$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

			$event = new CreatePublicEmptyRecipientEvent($message);
			$eventDispatcher->dispatch(CoreEvents::CREATE_PUBLIC_EMPTY_RECIPIENT, $event);

			$recipient = $event->getRecipient();

			if (!isset($additionalData['recipient'])) {
				$additionalData['recipient'] = $recipient->getDetails();
			}
			$additionalData['_recipient'] = $recipient;

			/** @var TagReplacementService $tagReplacementService */
			$tagReplacementService = $GLOBALS['container']['avisota.message.tagReplacementEngine'];

			foreach ($cells as $cell) {
				/** @var MessageRenderer $renderer */
				$renderer = $GLOBALS['container']['avisota.message.renderer'];
				$content  = $renderer->renderCell($message, $cell, $layout);
				$content  = array_map(
					function($content) use ($tagReplacementService, $additionalData) {
						return $tagReplacementService->parse(
							$content,
							$additionalData
						);
					},
					(array) $content
				);

				$contents[$cell] = $content;
			}

			$this->Template->message  = $message;
			$this->Template->contents = $contents;
		}
		catch (NoResultException $e) {
			$objHandler = new $GLOBALS['TL_PTY']['error_404']();
			$objHandler->generate($GLOBALS['objPage']->id);
		}
	}
}
