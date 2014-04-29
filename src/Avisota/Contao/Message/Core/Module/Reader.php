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

use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Avisota\Contao\Message\Core\Renderer\MessageRenderer;
use Contao\Doctrine\ORM\EntityHelper;
use Doctrine\ORM\NoResultException;

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

			/** @var MessageRenderer $renderer */
			$renderer = $GLOBALS['container']['avisota.message.renderer'];
			$content  = $renderer->renderCell($message, $this->avisota_message_cell, $layout);

			$this->Template->message = $message;
			$this->Template->content = implode(PHP_EOL, $content);
		}
		catch (NoResultException $e) {
			$objHandler = new $GLOBALS['TL_PTY']['error_404']();
			$objHandler->generate($GLOBALS['objPage']->id);
		}
	}
}
