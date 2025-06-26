<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificacionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => false,
            ],
            'USUARIO_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'TICKET_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'TIPO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 20,
                'default'    => 'EMAIL',
            ],
            'ASUNTO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 200,
            ],
            'MENSAJE' => [
                'type' => 'CLOB',
            ],
            'ENVIADO' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'N',
            ],
            'CREATED_AT' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
            'SENT_AT' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->addForeignKey('USUARIO_ID', 'USUARIOS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('TICKET_ID', 'TICKETS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('NOTIFICACION');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE NOTIFICACIONES_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER NOTIFICACIONES_BI
            BEFORE INSERT ON NOTIFICACIONES
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT NOTIFICACIONES_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraints
        $this->db->query("ALTER TABLE NOTIFICACIONES ADD CONSTRAINT CHK_NOTIFICACIONES_TIPO CHECK (TIPO IN ('EMAIL', 'APP'))");
        $this->db->query("ALTER TABLE NOTIFICACIONES ADD CONSTRAINT CHK_NOTIFICACIONES_ENVIADO CHECK (ENVIADO IN ('S', 'N'))");
    }

    public function down()
    {
        $this->forge->dropTable('NOTIFICACION');
        $this->db->query("DROP SEQUENCE NOTIFICACIONES_SEQ");
    }
}
