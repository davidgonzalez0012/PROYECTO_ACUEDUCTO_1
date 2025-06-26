<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketsTable extends Migration
{
   public function up()
    {
        $this->forge->addField([
            'ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => false,
            ],
            'TITULO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 200,
            ],
            'DESCRIPCION' => [
                'type' => 'CLOB',
            ],
            'USUARIO_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'CATEGORIA_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'ASIGNADO_A' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => true,
            ],
            'ESTADO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 20,
                'default'    => 'ABIERTO',
            ],
            'PRIORIDAD' => [
                'type'       => 'VARCHAR2',
                'constraint' => 20,
                'default'    => 'MEDIA',
            ],
            'FECHA_CREACION' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
            'FECHA_ACTUALIZACION' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
            'FECHA_CIERRE' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->addForeignKey('USUARIO_ID', 'USUARIOS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('CATEGORIA_ID', 'CATEGORIAS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ASIGNADO_A', 'USUARIOS', 'ID', 'SET NULL', 'CASCADE');
        $this->forge->createTable('TICKETS');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE TICKETS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER TICKETS_BI
            BEFORE INSERT ON TICKETS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT TICKETS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraints
        $this->db->query("ALTER TABLE TICKETS ADD CONSTRAINT CHK_TICKETS_ESTADO CHECK (ESTADO IN ('ABIERTO', 'EN_PROCESO', 'RESUELTO', 'CERRADO'))");
        $this->db->query("ALTER TABLE TICKETS ADD CONSTRAINT CHK_TICKETS_PRIORIDAD CHECK (PRIORIDAD IN ('BAJA', 'MEDIA', 'ALTA', 'CRITICA'))");
    }

    public function down()
    {
        $this->forge->dropTable('TICKETS');
        $this->db->query("DROP SEQUENCE TICKETS_SEQ");
    }
}
