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

use Contao\BackendTemplate;
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
     * Generate string for this module. If stay in backend return the wildcard.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            global $container;

            $translator = $container['translator'];

            $template = new BackendTemplate('be_wildcard');

            $template->wildcard = '### AVISOTA '
                                  . utf8_strtoupper($translator->translate('avisota_message_list.0', 'FMD'))
                                  . ' ###';
            $template->title    = $this->headline;
            $template->id       = $this->id;
            $template->link     = $this->name;
            $template->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $template->parse();
        }

        return parent::generate();

        #return !empty($this->Template->articles) ? $strBuffer : '';
    }

    /**
     * Compile the current element
     */
    protected function compile()
    {
        global $objPage;

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
            $jumpTo = $objPage;
        }

        $this->Template->messages = $messages;
        $this->Template->jumpTo   = $jumpTo->row();
    }
}
