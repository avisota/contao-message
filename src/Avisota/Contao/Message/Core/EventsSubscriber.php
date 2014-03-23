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

namespace Avisota\Contao\Message\Core;

use Avisota\Contao\Entity\Message;
use BackendTemplate;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventsSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			MessageEvents::CREATE_MESSAGE_OPTIONS                 => 'createMessageOptions',
			MessageEvents::CREATE_BOILERPLATE_MESSAGE_OPTIONS     => 'createBoilerplateMessageOptions',
			MessageEvents::CREATE_NON_BOILERPLATE_MESSAGE_OPTIONS => 'createNonBoilerplateMessageOptions',
		);
	}

	public function createMessageOptions(CreateOptionsEvent $event)
	{
		$this->getMessageOptions($event->getOptions());
	}

	public function getMessageOptions($options = array())
	{
		$repository   = EntityHelper::getRepository('Avisota\Contao:Message');
		$queryBuilder = $repository->createQueryBuilder('m');
		$queryBuilder
			->select('m')
			->innerJoin('m.category', 'c')
			->orderBy('c.title')
			->addOrderBy('m.subject');
		$query    = $queryBuilder->getQuery();
		$messages = $query->getResult();

		$this->fillOptions($options, $messages);

		return $options;
	}

	public function createBoilerplateMessageOptions(CreateOptionsEvent $event)
	{
		$this->getBoilerplateMessageOptions($event->getOptions());
	}

	public function getBoilerplateMessageOptions($options = array())
	{
		$repository   = EntityHelper::getRepository('Avisota\Contao:Message');
		$queryBuilder = $repository->createQueryBuilder('m');
		$expr         = $queryBuilder->expr();
		$queryBuilder
			->select('m')
			->innerJoin('m.category', 'c')
			->where($expr->eq('c.boilerplates', ':boilerplates'))
			->setParameter('boilerplates', true)
			->orderBy('c.title')
			->addOrderBy('m.subject');
		$query    = $queryBuilder->getQuery();
		$messages = $query->getResult();

		$this->fillOptions($options, $messages);

		return $options;
	}

	public function createNonBoilerplateMessageOptions(CreateOptionsEvent $event)
	{
		$this->getNonBoilerplateMessageOptions($event->getOptions());
	}

	public function getNonBoilerplateMessageOptions($options = array())
	{
		$repository   = EntityHelper::getRepository('Avisota\Contao:Message');
		$queryBuilder = $repository->createQueryBuilder('m');
		$expr         = $queryBuilder->expr();
		$queryBuilder
			->select('m')
			->innerJoin('m.category', 'c')
			->where($expr->eq('c.boilerplates', ':boilerplates'))
			->setParameter('boilerplates', false)
			->orderBy('c.title')
			->addOrderBy('m.subject');
		$query    = $queryBuilder->getQuery();
		$messages = $query->getResult();

		$this->fillOptions($options, $messages);

		return $options;
	}

	/**
	 * Fill the options array with the messages.
	 *
	 * @param array|\ArrayAccess $options
	 * @param array|Message[]    $messages
	 */
	protected function fillOptions($options, array $messages)
	{
		foreach ($messages as $message) {
			$category = $message->getCategory();

			$options[$category->getTitle()][$message->getId()] = $message->getSubject();
		}
	}
}
