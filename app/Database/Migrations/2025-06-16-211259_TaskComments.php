<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskComments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],

            'task_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],

            'comment'       => ['type' => 'TEXT', 'null' => TRUE],
            'created_at'    => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'    => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);

		$this->forge->addForeignKey('task_id', 'tasks', 'id');
		$this->forge->addForeignKey('user_id', 'users', 'id');

		$this->forge->createTable('task_comments');
    }

    public function down()
    {
        $this->forge->dropTable('task_comments');
    }
}
