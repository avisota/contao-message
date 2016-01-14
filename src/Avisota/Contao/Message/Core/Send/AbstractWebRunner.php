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

namespace Avisota\Contao\Message\Core\Send;

use Avisota\Contao\Entity\Message;

/**
 * Class AbstractWebRunner
 *
 * @package Avisota\Contao\Message\Core\Send
 */
abstract class AbstractWebRunner extends \Backend
{
    /**
     * AbstractWebRunner constructor.
     */
    function __construct()
    {
        // preserve object initialisation order
        \BackendUser::getInstance();
        \Database::getInstance();

        parent::__construct();
    }

    public function run()
    {
        $input             = \Input::getInstance();
        $messageRepository = \Contao\Doctrine\ORM\EntityHelper::getRepository('Avisota\Contao:Message');

        $messageId = $input->get('id');
        $message   = $messageRepository->find($messageId);
        /** @var \Avisota\Contao\Entity\Message $message */

        if (!$message) {
            header("HTTP/1.0 404 Not Found");
            echo '<h1>404 Not Found</h1>';
            exit;
        }

        $user = \BackendUser::getInstance();
        $user->authenticate();

        $this->execute($message, $user);
    }

    /**
     * @param Message      $message
     * @param \BackendUser $user
     *
     * @return mixed
     */
    abstract protected function execute(Message $message, \BackendUser $user);
}
