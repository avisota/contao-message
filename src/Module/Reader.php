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

use Avisota\Contao\Core\CoreEvents;
use Avisota\Contao\Core\Event\CreatePublicEmptyRecipientEvent;
use Avisota\Contao\Core\Recipient\SynonymizerService;
use Avisota\Contao\Message\Core\Renderer\MessageRenderer;
use Avisota\Contao\Message\Core\Renderer\TagReplacementService;
use Contao\BackendTemplate;
use Contao\Doctrine\ORM\EntityHelper;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Reader
 *
 * @package Avisota\Contao\Message\Core\Module
 */
class Reader extends \TwigModule
{
    /**
     * {@inheritdoc}
     */
    protected $strTemplate = 'avisota/frontend/module/mod_reader';

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
                                  . utf8_strtoupper($translator->translate('avisota_message_reader.0', 'FMD'))
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
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function compile()
    {
        if (TL_MODE == 'BE') {
            return;
        }

        if (\Config::get('useAutoItem') && \Input::get('auto_item')) {
            $messageAlias = \Input::get('auto_item');
        } else {
            $messageAlias = \Input::get('items');
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
                /** @var SynonymizerService $synonymizer */
                $synonymizer = $GLOBALS['container']['avisota.recipient.synonymizer'];

                $additionalData['recipient'] = $synonymizer->expandDetailsWithSynonyms($recipient);
            }
            $additionalData['_recipient'] = $recipient;

            /** @var TagReplacementService $tagReplacementService */
            $tagReplacementService = $GLOBALS['container']['avisota.message.tagReplacementEngine'];

            foreach ($cells as $cell) {
                /** @var MessageRenderer $renderer */
                $renderer = $GLOBALS['container']['avisota.message.renderer'];
                $content  = $renderer->renderCell($message, $cell, $layout);
                $content  = array_map(
                    function ($content) use ($tagReplacementService, $additionalData) {
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
        } catch (NoResultException $e) {
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($GLOBALS['objPage']->id);
        }
    }
}
