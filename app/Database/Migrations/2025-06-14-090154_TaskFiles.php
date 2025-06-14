<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskFiles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],

            'task_id'   => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],

            'file'      => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
		$this->forge->addKey('id', TRUE);

		$this->forge->addForeignKey('task_id', 'tasks', 'id');

		$this->forge->createTable('task_files');
    }

    public function down()
    {
		$this->forge->dropTable('task_files');
    }
}
