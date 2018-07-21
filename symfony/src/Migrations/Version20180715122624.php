<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180715122624 extends AbstractMigration
{
    const PRODUCTS = [
        [
            'name' => 'Fallout',
            'price' => 1.99,
        ],
        [
            'name' => 'Don’t Starve',
            'price' => 2.99,
        ],
        [
            'name' => 'Baldur’s Gate',
            'price' => 3.99,
        ],
        [
            'name' => 'Icewind Dale',
            'price' => 4.99,
        ],
        [
            'name' => 'Bloodborne',
            'price' => 5.99,
        ],
    ];

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO rpg.rpg_products (id, name, price, created_at, updated_at) VALUES (1, 'Fallout', 1.99, '2018-07-15 14:24:57', '2018-07-15 14:24:57');");
        $this->addSql("INSERT INTO rpg.rpg_products (id, name, price, created_at, updated_at) VALUES (2, 'Don’t Starve', 2.99, '2018-07-15 14:24:57', '2018-07-15 14:24:57');");
        $this->addSql("INSERT INTO rpg.rpg_products (id, name, price, created_at, updated_at) VALUES (3, 'Baldur’s Gate', 3.99, '2018-07-15 14:24:57', '2018-07-15 14:24:57');");
        $this->addSql("INSERT INTO rpg.rpg_products (id, name, price, created_at, updated_at) VALUES (4, 'Icewind Dale', 4.99, '2018-07-15 14:24:57', '2018-07-15 14:24:57');");
        $this->addSql("INSERT INTO rpg.rpg_products (id, name, price, created_at, updated_at) VALUES (5, 'Bloodborne', 5.99, '2018-07-15 14:24:57', '2018-07-15 14:24:57');");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE rpg_products;');
    }
}
