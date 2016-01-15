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

namespace Avisota\Contao\Message\Core\Module;

use Contao\Doctrine\ORM\EntityHelper;

/**
 * Class Listing
 *
 * @package Avisota\Contao\Message\Core\Module
 */
class Listing extends \TwigModule
{
    /**
     * {@inheritdoc}
     */
    protected $strTemplate = 'avisota/frontend/module/mod_listing';

    /**
     * Compile the current element
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
