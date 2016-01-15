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

namespace Avisota\Contao\Message\Core\Backend;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageCategory;
use Avisota\Contao\Entity\MessageContent;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;

/**
 * Class Helper
 *
 * @package Avisota\Contao\Message\Core\Backend
 */
class Helper
{
    /**
     * @return MessageCategory|null
     */
    static public function resolveCategoryFromInput()
    {
        $id            = \Input::get('id');
        $pid           = \Input::get('pid');
        $modelId       = null;
        $parentModelId = null;
        /** @var MessageCategory $category */
        $category = null;
        /** @var Message $message */
        $message = null;
        /** @var MessageContent $content */
        $content = null;

        if ($id) {
            $modelId = ModelId::fromSerialized($id);
        }
        if ($pid) {
            $parentModelId = ModelId::fromSerialized($pid);
        }

        // $id or $pid is a category ID
        if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message_category') {
            $repository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
            $category   = $repository->find($modelId->getId());
        } else {
            if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message_category') {
                $repository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
                $category   = $repository->find($parentModelId->getId());
            } // $id or $pid is a message ID
            else {
                if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message') {
                    $repository = EntityHelper::getRepository('Avisota\Contao:Message');
                    $message    = $repository->find($modelId->getId());
                    $category   = $message->getCategory();
                } else {
                    if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message') {
                        $repository = EntityHelper::getRepository('Avisota\Contao:Message');
                        $message    = $repository->find($parentModelId->getId());
                        $category   = $message->getCategory();
                    } // $id or $pid is a message content ID
                    else {
                        if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message_content') {
                            $repository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
                            $content    = $repository->find($modelId->getId());
                            $message    = $content->getMessage();
                            $category   = $message->getCategory();
                        } else {
                            if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message_content') {
                                $repository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
                                $content    = $repository->find($parentModelId->getId());
                                $message    = $content->getMessage();
                                $category   = $message->getCategory();
                            }
                        }
                    }
                }
            }
        }

        return $category;
    }
}
