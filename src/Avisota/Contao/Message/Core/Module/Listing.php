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

use Contao\Doctrine\ORM\EntityHelper;

class Listing extends \TwigModule
{
	/**
	 * {@inheritdoc}
	 */
	protected $strTemplate = 'avisota/frontend/module/mod_listing';

	/**
	 * {@inheritdoc}
	 */
	protected function compile()
	{
		$repository   = EntityHelper::getRepository('Avisota\Contao:Message');
		$queryBuilder = $repository->createQueryBuilder('m');
		$expr         = $queryBuilder->expr();
		$queryBuilder
			->innerJoin('m.category', 'c')
			->where($expr->in('c.id', ':categories'))
			->andWhere($expr->gt('m.sendOn', 0))
			->orderBy('m.sendOn', 'DESC')
			->setParameter('categories', deserialize($this->avisota_message_categories, true));
		$query    = $queryBuilder->getQuery();
		$messages = $query->getResult();

		$jumpTo = \PageModel::findByPk($this->jumpTo);
		if (!$jumpTo) {
			$jumpTo = $GLOBALS['objPage'];
		}

		$this->Template->messages = $messages;
		$this->Template->jumpTo   = $jumpTo->row();
	}
}
