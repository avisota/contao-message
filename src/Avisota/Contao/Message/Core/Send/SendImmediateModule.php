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

namespace Avisota\Contao\Message\Core\Send;

use Avisota\Contao\Entity\Message;
use Avisota\RecipientSource\RecipientSourceInterface;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SendImmediateModule extends \Controller implements SendModuleInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(Message $message)
    {
        global $container;

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $eventDispatcher->dispatch(
            ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
            new LoadLanguageFileEvent('avisota_send_immediate')
        );

        $recipientSourceData = $message->getRecipients();

        if ($recipientSourceData) {
            $serviceName = sprintf('avisota.recipientSource.%s', $recipientSourceData->getId());
            /** @var RecipientSourceInterface $recipientSource */
            $recipientSource = $container[$serviceName];

            $template = new \TwigTemplate('avisota/send/send_immediate', 'html5');
            return $template->parse(
                array(
                    'message' => $message,
                    'count'   => $recipientSource->countRecipients(),
                )
            );
        }

        return '';
    }
}
