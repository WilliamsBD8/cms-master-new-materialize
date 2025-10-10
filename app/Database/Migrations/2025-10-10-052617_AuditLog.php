<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuditLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment'  => TRUE],
            'table_name'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
            'column_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
            'record_id'     => ['type' => 'INT', 'constraint' => '11', 'null' => FALSE],
            'old_value'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
            'new_value'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
            'changed_by'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('changed_by', 'users', 'id');
		$this->forge->createTable('audit_logs');
    }

    public function down()
    {
		$this->forge->dropTable('audit_logs');
    }
}
