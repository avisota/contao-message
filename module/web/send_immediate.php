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

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Message\Core\Event\GenerateViewOnlineUrlEvent;
use Avisota\Contao\Message\Core\MessageEvents;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\GetPageDetailsEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\GenerateFrontendUrlEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\IdSerializer;

$dir = dirname(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__);

while ($dir && $dir != '.' && $dir != '/' && !is_file($dir . '/system/initialize.php')) {
    $dir = dirname($dir);

}

if (!is_file($dir . '/system/initialize.php')) {
    header("HTTP/1.0 500 Internal Server Error");
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>500 Internal Server Error</h1>';
    echo '<p>Could not find initialize.php!</p>';
    exit(1);
}

define('TL_MODE', 'FE');
require($dir . '/system/initialize.php');

BackendUser::getInstance();

class send_immediate extends \Avisota\Contao\Message\Core\Send\AbstractWebRunner
{
    protected function execute(Message $message, \BackendUser $user)
    {
        global $container;

        $input       = \Input::getInstance();
        $environment = Environment::getInstance();

        $eventDispatcher = $this->getEventDispatcher();
        $entityManager   = EntityHelper::getEntityManager();

        $queueData   = $message->getQueue();
        $serviceName = sprintf('avisota.queue.%s', $queueData->getId());
        $queue       = $container[$serviceName];

        $recipientSourceData = $message->getRecipients();
        $serviceName         = sprintf('avisota.recipientSource.%s', $recipientSourceData->getId());

        /** @var \Avisota\RecipientSource\RecipientSourceInterface $recipientSource */
        $recipientSource = $container[$serviceName];

        /** @var \Avisota\Contao\Message\Core\Renderer\MessageRendererInterface $renderer */
        $renderer        = $container['avisota.message.renderer'];
        $messageTemplate = $renderer->renderMessage($message);

        $event = new LoadLanguageFileEvent('avisota_message');
        $eventDispatcher->dispatch(ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $event);

        $idSerializer = new IdSerializer();
        $idSerializer->setDataProviderName('orm_avisota_message');
        $idSerializer->setId($message->getId());

        $pidSerializer = new IdSerializer();
        $pidSerializer->setDataProviderName('orm_avisota_message_category');
        $pidSerializer->setId($message->getCategory()->getId());

        $viewOnlinePage = $message->getCategory()->getViewOnlinePage();

        if ($viewOnlinePage) {
            $getPageDetailsEvent = new GetPageDetailsEvent($viewOnlinePage);
            $eventDispatcher->dispatch(ContaoEvents::CONTROLLER_GET_PAGE_DETAILS, $getPageDetailsEvent);
            $pageDetails = $getPageDetailsEvent->getPageDetails();

            $generateUrlEvent = new GenerateFrontendUrlEvent(
                $pageDetails,
                '/' . $message->getAlias(),
                $pageDetails['language']
            );
            $eventDispatcher->dispatch(ContaoEvents::CONTROLLER_GENERATE_FRONTEND_URL, $generateUrlEvent);

            $url = $generateUrlEvent->getUrl();

            if (!preg_match('~^\w+:~', $url)) {
                $url = $environment->base . $url;
            }

            $generateViewOnlineUrlEvent = new GenerateViewOnlineUrlEvent($message, $url);
            $eventDispatcher->dispatch(MessageEvents::GENERATE_VIEW_ONLINE_URL, $generateViewOnlineUrlEvent);

            $url = sprintf(
                $GLOBALS['TL_LANG']['avisota_message']['viewOnline'],
                $generateViewOnlineUrlEvent->getUrl()
            );
        } else {
            $url = false;
        }

        // TODO fix view online link
        $additionalData = array('view_online_link' => $url);

        $turn = $input->get('turn');
        if (!$turn) {
            $turn = 0;
        }

        $loop = $input->get('loop');
        if (!$loop) {
            $loop = uniqid();
        }

        $event = new \Avisota\Contao\Core\Event\PreSendImmediateEvent($message, $turn, $loop);
        $eventDispatcher->dispatch('avisota.pre-send-immediate', $event);

        $queueHelper = new \Avisota\Queue\QueueHelper();
        $queueHelper->setEventDispatcher($GLOBALS['container']['event-dispatcher']);
        $queueHelper->setQueue($queue);
        $queueHelper->setRecipientSource($recipientSource);
        $queueHelper->setMessageTemplate($messageTemplate);
        $queueHelper->setNewsletterData($additionalData);

        $count = $queueHelper->enqueue(30, $turn * 30);

        $event = new \Avisota\Contao\Core\Event\PostSendImmediateEvent($count, $message, $turn, $loop);
        $eventDispatcher->dispatch('avisota.post-send-immediate', $event);

        if ($count || ($turn * 30 + 30) < $recipientSource->countRecipients()) {
            $eventDispatcher->dispatch(
                ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
                new LoadLanguageFileEvent('avisota_message_preview')
            );

            $_SESSION['TL_CONFIRM'][] = sprintf(
                $GLOBALS['TL_LANG']['avisota_message_preview']['messagesEnqueued'],
                $count,
                $turn + 1
            );

            $parameters = array(
                'id'   => $message->getId(),
                'turn' => $turn + 1,
                'loop' => $loop,
            );
            $url        = sprintf(
                '%ssystem/modules/avisota-message/web/send_immediate.php?%s',
                $environment->base,
                http_build_query($parameters)
            );

            $entityManager->flush();
        } else {
            $parameters = array(
                'do'      => 'avisota_outbox',
                'execute' => $queueData->getId(),
            );
            $url        = sprintf(
                '%scontao/main.php?%s',
                $environment->base,
                http_build_query($parameters)
            );

            $message->setSendOn(new \DateTime());
            $entityManager->persist($message);
            $entityManager->flush();
        }

        echo '<html><head><meta http-equiv="refresh" content="0; URL=' . $url . '"></head><body>Still generating...</body></html>';
        exit;
    }

    function createUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }
}

$send_immediate = new send_immediate();
$send_immediate->run();
