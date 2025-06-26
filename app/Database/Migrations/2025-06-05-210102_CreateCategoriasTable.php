<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriasTable extends Migration
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
            'PRIORIDAD_DEFAULT' => [
                'type'       => 'NUMBER',
                'constraint' => 1,
                'default'    => 2,
            ],
            'ACTIVO' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'S',
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->createTable('CATEGORIAS');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE CATEGORIAS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER CATEGORIAS_BI
            BEFORE INSERT ON CATEGORIAS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT CATEGORIAS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraints
        $this->db->query("ALTER TABLE CATEGORIAS ADD CONSTRAINT CHK_CATEGORIAS_ACTIVO CHECK (ACTIVO IN ('S', 'N'))");
        $this->db->query("ALTER TABLE CATEGORIAS ADD CONSTRAINT CHK_CATEGORIAS_PRIORIDAD CHECK (PRIORIDAD_DEFAULT BETWEEN 1 AND 4)");
    }

    public function down()
    {
        $this->forge->dropTable('CATEGORIAS');
        $this->db->query("DROP SEQUENCE CATEGORIAS_SEQ");
    }
}
