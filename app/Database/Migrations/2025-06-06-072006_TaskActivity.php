<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskActivity extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'date_start'    => ['type' => 'DATE', 'null' => TRUE],
            'date_end'      => ['type' => 'DATE', 'null' => TRUE],
            'created_at'    => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'    => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->createTable('task_activities');
    }

    public function down()
    {
        $this->forge->dropTable('task_activities');
    }
}
