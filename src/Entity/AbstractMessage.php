<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Entity;

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Queue;
use Avisota\Contao\Entity\RecipientSource;
use Contao\Doctrine\ORM\AliasableInterface;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityHelper;
use Contao\Doctrine\ORM\EntityInterface;

/**
 * Class AbstractMessage
 *
 * @package Avisota\Contao\Message\Core\Entity
 */
abstract class AbstractMessage implements EntityInterface, AliasableInterface
{
    /**
     * AbstractMessage constructor.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        if (isset($GLOBALS['TL_LANGUAGE'])) {
            $this->language = $GLOBALS['TL_LANGUAGE'];
        }
    }

    /**
     * Get recipients
     *
     * @return RecipientSource
     */
    public function getRecipients()
    {
        if (!$this->getCategory()) {
            return null;
        }

        $category = $this->getCategory();

        if ($category->getBoilerplates()
            || $category->getRecipientsMode() == 'byMessage'
        ) {
            $recipients = $this->recipients;
        } else {
            if ($category->getRecipientsMode() == 'byMessageOrCategory') {
                $recipients = $this->recipients;
                if (!$recipients) {
                    $recipients = $category->getRecipients();
                }
            } else {
                if ($category->getRecipientsMode() == 'byCategory') {
                    $recipients = $category->getRecipients();
                } else {
                    throw new \RuntimeException('Could not find recipients for message ' . $this->getId());
                }
            }
        }

        return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'recipients', $recipients);
    }

    /**
     * Get layout
     *
     * @return Layout
     */
    public function getLayout()
    {
        if (!$this->getCategory()) {
            return null;
        }

        $category = $this->getCategory();

        if ($category->getBoilerplates()
            || $category->getLayoutMode() == 'byMessage'
        ) {
            $layout = $this->layout;
        } else {
            if ($category->getLayoutMode() == 'byMessageOrCategory') {
                $layout = $this->layout;
                if (!$layout) {
                    $layout = $category->getLayout();
                }
            } else {
                if ($category->getLayoutMode() == 'byCategory') {
                    $layout = $category->getLayout();
                } else {
                    throw new \RuntimeException('Could not find layout for message ' . $this->getId());
                }
            }
        }

        return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'layout', $layout);
    }

    /**
     * Get queue
     *
     * @return Queue
     */
    public function getQueue()
    {
        if (!$this->getCategory()) {
            return null;
        }

        $category = $this->getCategory();

        if ($category->getBoilerplates()
            || $category->getQueueMode() == 'byMessage'
        ) {
            $queue = $this->queue;
        } else {
            if ($category->getQueueMode() == 'byMessageOrCategory') {
                $queue = $this->queue;
                if (!$queue) {
                    $queue = $category->getQueue();
                }
            } else {
                if ($category->getQueueMode() == 'byCategory') {
                    $queue = $category->getQueue();
                } else {
                    throw new \RuntimeException('Could not find queue for message ' . $this->getId());
                }
            }
        }

        return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'queue', $queue);
    }

    /**
     * Return the alias parent field value, to generate the alias from.
     *
     * @return string
     */
    public function getAliasParentValue()
    {
        return $this->getSubject();
    }
}
