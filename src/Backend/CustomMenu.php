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

use Avisota\Contao\Entity\MessageCategory;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;

/**
 * Class CustomMenu
 *
 * @package Avisota\Contao\Message\Core\Backend
 */
class CustomMenu extends \BackendModule
{
    /**
     * @param array $navigation
     * @param       $showAll
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function hookGetUserNavigation(array $navigation, $showAll)
    {
        if (TL_MODE == 'BE') {
            try {
                if (!$showAll) {
                    $database = \Database::getInstance();

                    if ($database->tableExists('orm_avisota_message_category')) {

                        $category = Helper::resolveCategoryFromInput();

                        if ($category) {
                            $foundCustomEntry = false;

                            $menu = &$navigation['avisota'];
                            foreach ($menu['modules'] as $name => &$module) {
                                if ($name == 'avisota_category_' . $category->getId()) {
                                    $module['class'] .= ' active';
                                    $foundCustomEntry = true;
                                }
                            }

                            if ($foundCustomEntry) {
                                $classes = explode(' ', $menu['modules']['avisota_newsletter']['class']);
                                $classes = array_map('trim', $classes);
                                $pos     = array_search('active', $classes);
                                if ($pos !== false) {
                                    unset($classes[$pos]);
                                }
                                $menu['modules']['avisota_newsletter']['class'] = implode(' ', $classes);
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
                // silently ignore
            }
        }
        return $navigation;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function injectMenu()
    {
        global $container;

        // initialize the entity manager and class loaders
        $container['doctrine.orm.entityManager'];

        $beModules = array();

        if (class_exists('Avisota\Contao\Entity\MessageCategory')) {
            try {
                $messageCategoryRepository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
                $queryBuilder              = $messageCategoryRepository->createQueryBuilder('mc');
                $queryBuilder
                    ->select('mc')
                    ->where('mc.showInMenu=:showInMenu')
                    ->setParameter('showInMenu', true)
                    ->orderBy('mc.title');
                $query = $queryBuilder->getQuery();
                /** @var MessageCategory[] $messageCategories */
                $messageCategories = $query->getResult();

                foreach ($messageCategories as $messageCategory) {
                    $id    = $messageCategory->getId();
                    $icon  = $messageCategory->getUseCustomMenuIcon()
                        ? $messageCategory->getMenuIcon()
                        : 'assets/avisota/message/images/newsletter.png';
                    $title = $messageCategory->getTitle();

                    $beModules['avisota_category_' . $id] = array(
                        'callback' => 'Avisota\Contao\Message\Core\Backend\CustomMenu',
                        'icon'     => $icon,
                    );

                    $GLOBALS['TL_LANG']['MOD']['avisota_category_' . $id] = array($title);
                }
            } catch (\Exception $e) {
                // silently ignore
            }
        }

        if (count($beModules)) {
            $GLOBALS['BE_MOD']['avisota'] = array_merge(
                $beModules,
                $GLOBALS['BE_MOD']['avisota']
            );
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function generate()
    {
        $do = \Input::get('do');
        $id = preg_replace('#^avisota_category_(.*)$#', '$1', $do);

        $serializer = new ModelId('orm_avisota_message_category', $id);

        $this->redirect(
            'contao/main.php?do=avisota_newsletter&table=orm_avisota_message&pid=' .
            $serializer->getSerialized()
        );
    }

    /**
     * Compile the current element
     */
    protected function compile()
    {
    }
}
