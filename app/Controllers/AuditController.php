<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuditController extends BaseController
{
    protected $db;
    
    // Columnas que no se deben auditar
    private $excludedColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    
    // Tablas que no se deben auditar
    private $excludedTables = ['audits', 'technical_metadata', 'user', 'category'];
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        //
    }

    public function createTriggers()
    {
        // Solo en desarrollo o con permisos admin
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Solo disponible en desarrollo']);
        }
        
        $tables = $this->getAllTables();
        $results = [];
        
        echo "<h2>🔧 Generando Triggers de Auditoría...</h2>";
        echo "<style>body{font-family:monospace;padding:20px;} .success{color:green;} .error{color:red;}</style>";
        echo "<pre>";
        
        foreach ($tables as $table) {
            if (in_array($table, $this->excludedTables)) {
                continue;
            }
            
            $result = $this->generateTriggersForTable($table);
            $results[$table] = $result;
            
            if ($result['success']) {
                echo "<span class='success'>✓</span> {$table}: {$result['message']}\n";
            } else {
                echo "<span class='error'>✗</span> {$table}: {$result['message']}\n";
            }
        }
        
        echo "\n<strong>✅ ¡Proceso completado!</strong>\n";
        echo "Total tablas procesadas: " . count($results) . "\n";
        echo "</pre>";
        
        return;
    }

    public function dropTriggers()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Solo disponible en desarrollo']);
        }
        
        $tables = $this->getAllTables();
        $dropped = [];
        
        echo "<h2>🗑️ Eliminando Triggers de Auditoría...</h2>";
        echo "<style>body{font-family:monospace;padding:20px;}</style>";
        echo "<pre>";
        
        foreach ($tables as $table) {
            if (in_array($table, $this->excludedTables)) {
                continue;
            }
            
            foreach (['insert', 'update', 'delete'] as $type) {
                $triggerName = "trg_{$table}_after_{$type}";
                try {
                    $this->db->query("DROP TRIGGER IF EXISTS {$triggerName}");
                    $dropped[] = $triggerName;
                    echo "✓ Eliminado: {$triggerName}\n";
                } catch (\Exception $e) {
                    // Ignorar si no existe
                }
            }
        }
        
        echo "\n<strong>✅ Triggers eliminados: " . count($dropped) . "</strong>\n";
        echo "</pre>";
        
        return;
    }

    public function exportSql($table = null)
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Solo disponible en desarrollo']);
        }
        
        $sql = $this->generateTriggersSQL($table);
        
        $filename = 'audit_triggers_' . date('Ymd_His') . '.sql';
        
        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($sql);
    }

    public function createTriggerFor($table)
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Solo disponible en desarrollo']);
        }
        
        echo "<h2>🔧 Generando Trigger para: {$table}</h2>";
        echo "<style>body{font-family:monospace;padding:20px;}</style>";
        echo "<pre>";
        
        $result = $this->generateTriggersForTable($table);
        
        if ($result['success']) {
            echo "<span style='color:green;'>✓</span> {$result['message']}\n\n";
            echo "Triggers creados:\n";
            foreach ($result['triggers'] as $trigger) {
                echo "  - {$trigger}\n";
            }
        } else {
            echo "<span style='color:red;'>✗</span> {$result['message']}\n";
        }
        
        echo "</pre>";
        
        return;
    }

    public function listTables()
    {
        $tables = $this->getAllTables();
        
        echo "<h2>📋 Tablas en la base de datos</h2>";
        echo "<style>body{font-family:monospace;padding:20px;} .excluded{color:#999;}</style>";
        echo "<pre>";
        
        foreach ($tables as $table) {
            if (in_array($table, $this->excludedTables)) {
                echo "<span class='excluded'>⊗ {$table} (excluida)</span>\n";
            } else {
                echo "✓ {$table}\n";
            }
        }
        
        echo "\nTotal: " . count($tables) . " tablas\n";
        echo "</pre>";
        
        return;
    }

    private function executeTrigger($triggerSql): void
    {
        $commands = explode('DELIMITER', $triggerSql);
        
        foreach ($commands as $command) {
            $command = trim($command);
            if (empty($command) || $command === '$$' || $command === ';') {
                continue;
            }
            
            $command = str_replace(['$$', ';;'], ';', $command);
            $command = trim($command, "; \n\r\t");
            
            if (!empty($command)) {
                $this->db->query($command);
            }
        }
    }
    

    private function getAllTables(): array
    {
        $query = $this->db->query("SHOW TABLES");
        $tables = [];
        
        foreach ($query->getResultArray() as $row) {
            $tables[] = array_values($row)[0];
        }
        
        return $tables;
    }

    private function getTableColumns($table): array
    {
        $excludedList = "'" . implode("','", $this->excludedColumns) . "'";
        
        $query = $this->db->query("
            SELECT COLUMN_NAME, DATA_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ?
            AND COLUMN_NAME NOT IN ({$excludedList})
            ORDER BY ORDINAL_POSITION
        ", [$table]);
        
        return $query->getResultArray();
    }

    private function generateTriggersForTable($table): array
    {
        $columns = $this->getTableColumns($table);
        
        if (empty($columns)) {
            return [
                'success' => false,
                'message' => "No hay columnas para auditar"
            ];
        }
        
        try {
            // INSERT
            $triggerInsert = $this->generateInsertTrigger($table, $columns);
            $this->executeTrigger($triggerInsert);
            
            // UPDATE
            $triggerUpdate = $this->generateUpdateTrigger($table, $columns);
            $this->executeTrigger($triggerUpdate);
            
            // DELETE
            $triggerDelete = $this->generateDeleteTrigger($table, $columns);
            $this->executeTrigger($triggerDelete);
            
            return [
                'success' => true,
                'message' => 'Triggers creados exitosamente',
                'triggers' => [
                    "trg_{$table}_after_insert",
                    "trg_{$table}_after_update",
                    "trg_{$table}_after_delete"
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function generateInsertTrigger($table, $columns): string
    {
        $triggerName = "trg_{$table}_after_insert";
        
        $auditCalls = '';
        foreach ($columns as $column) {
            $colName = $column['COLUMN_NAME'];
            $auditCalls .= "        CALL sp_insert_audit(v_user_id, 'Created', '{$table}', NEW.id, '{$colName}', NULL, CAST(NEW.{$colName} AS CHAR), 'Registro creado');\n";
        }
        
        return "
            DROP TRIGGER IF EXISTS {$triggerName};

            DELIMITER \$\$

            CREATE TRIGGER {$triggerName}
            AFTER INSERT ON {$table}
            FOR EACH ROW
            BEGIN
                DECLARE v_user_id INT DEFAULT 1;
                SET v_user_id = COALESCE(@audit_user_id, 1);
                
            {$auditCalls}END\$\$

            DELIMITER ;
            ";
    }

    private function generateUpdateTrigger($table, $columns): string
    {
        $triggerName = "trg_{$table}_after_update";
        
        $updateChecks = '';
        foreach ($columns as $column) {
            $colName = $column['COLUMN_NAME'];
            $updateChecks .= "    IF (OLD.{$colName} IS NULL AND NEW.{$colName} IS NOT NULL) OR \n";
            $updateChecks .= "       (OLD.{$colName} IS NOT NULL AND NEW.{$colName} IS NULL) OR \n";
            $updateChecks .= "       (OLD.{$colName} IS NOT NULL AND NEW.{$colName} IS NOT NULL AND OLD.{$colName} != NEW.{$colName}) THEN\n";
            $updateChecks .= "        CALL sp_insert_audit(v_user_id, 'Updated', '{$table}', NEW.id, '{$colName}', CAST(OLD.{$colName} AS CHAR), CAST(NEW.{$colName} AS CHAR), 'Campo actualizado');\n";
            $updateChecks .= "    END IF;\n\n";
        }
        
        return "
            DROP TRIGGER IF EXISTS {$triggerName};

            DELIMITER \$\$

            CREATE TRIGGER {$triggerName}
            AFTER UPDATE ON {$table}
            FOR EACH ROW
            BEGIN
                DECLARE v_user_id INT DEFAULT 1;
                SET v_user_id = COALESCE(@audit_user_id, 1);
                
            {$updateChecks}END\$\$

            DELIMITER ;
            ";
    }

    private function generateDeleteTrigger($table, $columns): string
    {
        $triggerName = "trg_{$table}_after_delete";
        
        $auditCalls = '';
        foreach ($columns as $column) {
            $colName = $column['COLUMN_NAME'];
            $auditCalls .= "        CALL sp_insert_audit(v_user_id, 'Deleted', '{$table}', OLD.id, '{$colName}', CAST(OLD.{$colName} AS CHAR), '', 'Registro eliminado');\n";
        }
        
        return "
            DROP TRIGGER IF EXISTS {$triggerName};

            DELIMITER \$\$

            CREATE TRIGGER {$triggerName}
            AFTER DELETE ON {$table}
            FOR EACH ROW
            BEGIN
                DECLARE v_user_id INT DEFAULT 1;
                SET v_user_id = COALESCE(@audit_user_id, 1);
                
            {$auditCalls}END\$\$

            DELIMITER ;
        ";
    }

    private function generateTriggersSQL($table = null): string
    {
        $sql = "-- =====================================================\n";
        $sql .= "-- TRIGGERS DE AUDITORÍA - SIGCON\n";
        $sql .= "-- Generado automáticamente\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- =====================================================\n\n";
        
        $tables = $table ? [$table] : $this->getAllTables();
        
        foreach ($tables as $tableName) {
            if (in_array($tableName, $this->excludedTables)) {
                continue;
            }
            
            $columns = $this->getTableColumns($tableName);
            
            if (empty($columns)) {
                continue;
            }
            
            $sql .= "-- =====================================================\n";
            $sql .= "-- TABLA: {$tableName}\n";
            $sql .= "-- =====================================================\n\n";
            
            $sql .= $this->generateInsertTrigger($tableName, $columns) . "\n\n";
            $sql .= $this->generateUpdateTrigger($tableName, $columns) . "\n\n";
            $sql .= $this->generateDeleteTrigger($tableName, $columns) . "\n\n";
        }
        
        return $sql;
    }
}
