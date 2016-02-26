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

use ContaoCommunityAlliance\DcGeneral\DC_General;

/**
 * Class Layout
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class Layout
{
    /**
     * Add the type of content element
     *
     * @param array
     *
     * @return string
     */
    static public function addElement($contentData)
    {
        return sprintf(
            '<div>%s</div>' . "\n",
            $contentData['title']
        );
    }

    /**
     * @param DC_General|\Avisota\Contao\Entity\Layout $layout
     *
     * @return array
     */
    static public function getDefaultSelectedCellContentElements($layout)
    {
        $value = array();

        list($group, $mailChimpTemplate) = explode(':', $layout->getMailchimpTemplate());
        if (isset($GLOBALS['AVISOTA_MAILCHIMP_TEMPLATE'][$group][$mailChimpTemplate])) {
            $config = $GLOBALS['AVISOTA_MAILCHIMP_TEMPLATE'][$group][$mailChimpTemplate];

            if (isset($config['cells'])) {
                foreach ($config['cells'] as $cellName => $cellConfig) {
                    if (isset($cellConfig['preferredElements'])) {
                        foreach ($cellConfig['preferredElements'] as $elementName) {
                            $value[] = $cellName . ':' . $elementName;
                        }
                    } else {
                        foreach ($GLOBALS['TL_MCE'] as $elements) {
                            foreach ($elements as $elementType) {
                                $value[] = $cellName . ':' . $elementType;
                            }
                        }
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @param                               $value
     * @param \Avisota\Contao\Entity\Layout $layout
     *
     * @return array
     */
    static public function getterCallbackAllowedCellContents($value, \Avisota\Contao\Entity\Layout $layout)
    {
        if ($value === null) {
            return static::getDefaultSelectedCellContentElements($layout);
        }

        return $value;
    }

    /**
     * @param                               $value
     * @param \Avisota\Contao\Entity\Layout $layout
     *
     * @return null
     */
    static public function setterCallbackAllowedCellContents($value, \Avisota\Contao\Entity\Layout $layout)
    {
        if (!is_array($value)) {
            $value = null;
        } else {
            if ($value !== null) {
                $defaultValue = static::getDefaultSelectedCellContentElements($layout);

                $diffLeft  = array_diff($value, $defaultValue);
                $diffRight = array_diff($defaultValue, $value);

                if (!(count($diffLeft) + count($diffRight))) {
                    $value = null;
                }
            }
        }

        return $value;
    }
}
