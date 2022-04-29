<?php

namespace NathanReus\CI4APIFramework\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefreshTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField('id');
        $this->forge->addField([
            'user_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'family' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
            ],
            'issued_at' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
        ]);

        $this->forge->addUniqueKey('family');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('refresh_tokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('refresh_tokens');
    }
}
