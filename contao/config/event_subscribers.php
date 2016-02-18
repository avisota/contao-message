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

use Avisota\Contao\Message\Core\Backend\Preview;
use Avisota\Contao\Message\Core\DataContainer\LayoutOptions;
use Avisota\Contao\Message\Core\DataContainer\Message;
use Avisota\Contao\Message\Core\DataContainer\MessageCategoryOptions;
use Avisota\Contao\Message\Core\DataContainer\MessageContent;
use Avisota\Contao\Message\Core\DataContainer\MessageContentOptions;
use Avisota\Contao\Message\Core\DataContainer\MessageOptions;
use Avisota\Contao\Message\Core\DataContainer\ThemeOptions;
use Avisota\Contao\Message\Core\EventsSubscriber;
use Avisota\Contao\Message\Core\Layout\ContaoStylesheets;

return array(
    new Preview(),
    new Message(),
    new MessageContent(),
    new EventsSubscriber(),
    new ContaoStylesheets(),
    new ThemeOptions(),
    new LayoutOptions(),
    new MessageCategoryOptions(),
    new MessageOptions(),
    new MessageContentOptions(),
);
