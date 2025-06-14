<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Task extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],

            'task_state_id'     => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'task_sprint_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'task_activity_id'  => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'task_user_id'      => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],

            'title'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'description'       => ['type' => 'TEXT', 'null' => TRUE],
            'orden'             => ['type' => 'INT', 'constraint' => '11', 'default' => 1],

            'date_task'         => ['type' => 'DATE', 'null' => TRUE],
            'date_state'        => ['type' => 'DATE', 'null' => TRUE],
            'created_at'        => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at'        => ['type' => 'DATETIME', 'null' => TRUE]
        ]);
		$this->forge->addKey('id', TRUE);

		$this->forge->addForeignKey('task_state_id', 'task_states', 'id');
		$this->forge->addForeignKey('task_sprint_id', 'task_sprints', 'id');
		$this->forge->addForeignKey('task_activity_id', 'task_activities', 'id');
		$this->forge->addForeignKey('task_user_id', 'users', 'id');

		$this->forge->createTable('tasks');
    }

    public function down()
    {
		$this->forge->createTable('tasks');
    }
}
