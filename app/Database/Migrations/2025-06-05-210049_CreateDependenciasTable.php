<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDependenciasTable extends Migration
{
   public function up()
    {
        $this->forge->addField([
            'ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => false,
            ],
            'NOMBRE' => [
                'type'       => 'VARCHAR2',
                'constraint' => 100,
            ],
            'DESCRIPCION' => [
                'type'       => 'VARCHAR2',
                'constraint' => 500,
                'null'       => true,
            ],
            'ACTIVO' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'S',
            ],
            'CREATED_AT' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
            'UPDATED_AT' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->createTable('DEPENDENCIAS');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE DEPENDENCIAS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER DEPENDENCIAS_BI
            BEFORE INSERT ON DEPENDENCIAS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT DEPENDENCIAS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Constraint para ACTIVO
        $this->db->query("ALTER TABLE DEPENDENCIAS ADD CONSTRAINT CHK_DEPENDENCIAS_ACTIVO CHECK (ACTIVO IN ('S', 'N'))");
    }

    public function down()
    {
        $this->forge->dropTable('DEPENDENCIAS');
    }
    
}
