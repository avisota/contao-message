<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-image
 * @license    LGPL-3.0+
 * @filesource
 */

namespace DoctrineMigrations\AvisotaMessage;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20140413
 *
 * @package DoctrineMigrations\AvisotaMessage
 */
class Version20140413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('orm_avisota_message_content')) {
            return;
        }

        $stmt = $this->connection->prepare('SELECT COUNT(id) FROM orm_avisota_message_content WHERE sorting=0');
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            $stmt = $this->connection->prepare('SELECT MAX(sorting) FROM orm_avisota_message_content');
            $stmt->execute();

            $max = max(128, (int) $stmt->fetchColumn());

            $this->addSql(sprintf('SET @sorting := %s', $max));
            $this->addSql('UPDATE orm_avisota_message_content SET sorting=(@sorting := 2 * @sorting) WHERE sorting=0');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
