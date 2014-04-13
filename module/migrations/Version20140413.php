<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-image
 * @license    LGPL-3.0+
 * @filesource
 */

namespace DoctrineMigrations\AvisotaMessage;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140413 extends AbstractMigration
{
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

	public function down(Schema $schema)
	{
	}
}
