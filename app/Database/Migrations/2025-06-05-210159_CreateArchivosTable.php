<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArchivosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => false,
            ],
            'TICKET_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'USUARIO_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'NOMBRE_ARCHIVO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 255,
            ],
            'RUTA_ARCHIVO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 500,
            ],
            'TAMANO' => [
                'type' => 'NUMBER',
                'null' => true,
            ],
            'UPLOADED_AT' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->addForeignKey('TICKET_ID', 'TICKETS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('USUARIO_ID', 'USUARIOS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ARCHIVO');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE ARCHIVOS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER ARCHIVOS_BI
            BEFORE INSERT ON ARCHIVOS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT ARCHIVOS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");
    }

    public function down()
    {
        $this->forge->dropTable('ARCHIVO');
        $this->db->query("DROP SEQUENCE ARCHIVOS_SEQ");
    }
}
