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

namespace Avisota\Contao\Message\Core\DataContainer;

use Avisota\Contao\Core\Event\CreateOptionsEvent;
use Contao\Doctrine\ORM\EntityHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ThemeOptions
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class ThemeOptions implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'avisota.create-template-directory-options' => array(
                array('createTemplateDirectoryOptions'),
            ),

            'avisota.create-theme-options' => array(
                array('createThemeOptions'),
            ),
        );
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createTemplateDirectoryOptions(CreateOptionsEvent $event)
    {
        $basePath = TL_ROOT . '/templates/';

        $iterator = new \RecursiveDirectoryIterator(
            $basePath,
            \FilesystemIterator::KEY_AS_PATHNAME |
            \FilesystemIterator::CURRENT_AS_FILEINFO |
            \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \CallbackFilterIterator(
            $iterator,
            function (\SplFileInfo $file) {
                return $file->getBasename() != '..' && $file->isDir();
            }
        );

        $directories = array();

        /** @var \SplFileInfo $directory */
        foreach ($iterator as $directory) {
            $path = str_replace($basePath, '', $directory->getPathname());
            $path = rtrim($path, '.');
            $path = rtrim($path, '/');

            if ($path) {
                $directories[] = $path;
            }
        }

        usort($directories, 'strnatcasecmp');

        $options = $event->getOptions();
        foreach ($directories as $directory) {
            /** @var string $directory */
            $options[$directory] = $directory;
        }
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createThemeOptions(CreateOptionsEvent $event)
    {
        $this->getThemeOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getThemeOptions($options = array())
    {
        $themeRepository = EntityHelper::getRepository('Avisota\Contao:Theme');
        $themes          = $themeRepository->findBy(array(), array('title' => 'ASC'));

        /** @var \Avisota\Contao\Entity\Theme $theme */
        foreach ($themes as $theme) {
            $options[$theme->getId()] = $theme->getTitle();
        }

        return $options;
    }
}
