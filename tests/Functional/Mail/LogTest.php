<?php

declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Functional\Models\Mail;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Mail\Log;
use Shopware\Models\Order\Document\Document;
use Shopware\Tests\Functional\Traits\ContainerTrait;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;

class LogTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTransactionBehaviour;
    public const SQL_DOCUMENTS_ID = 99999;
    public const SQL_MAIL_LOG_ID = 99999;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function setUp(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        $connection->executeQuery(
            "
                INSERT INTO s_order_documents  VALUES(:documentid, '2000-05-07', 69, 69, 69420, 420.69, 'test', 'loremipsumfoobar1337');
                INSERT INTO s_mail_log(id, sender, sent_at) VALUES(:mailid, 'ipsumlorem','2000-07-05 00:00:00');
                INSERT INTO s_mail_log_document VALUES(:mailid,:documentid);
            ",
            [
                'documentid' => self::SQL_DOCUMENTS_ID,
                'mailid' => self::SQL_MAIL_LOG_ID,
            ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThatDocumentIsNotDeletedWithLogEntry(): void
    {
        $modelmanager = $this->getContainer()->get('models');
        $log = $modelmanager->find(Log::class, self::SQL_MAIL_LOG_ID);
        static::assertInstanceOf(Log::class, $log);
        $modelmanager->remove($log);
        $modelmanager->flush();
        $document = $modelmanager->find(Document::class, self::SQL_DOCUMENTS_ID);
        static::assertInstanceOf(Document::class, $document);
    }
}
