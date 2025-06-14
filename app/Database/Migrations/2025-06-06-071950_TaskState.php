<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskState extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 100],
            'color_background'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'color_font'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'            => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
            'created_at'        => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'        => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('task_states');
    }

    public function down()
    {
		$this->forge->dropTable('task_states');
    }
}
