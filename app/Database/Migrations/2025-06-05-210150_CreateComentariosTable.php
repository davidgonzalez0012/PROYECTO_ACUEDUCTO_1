<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComentariosTable extends Migration
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
            'CONTENIDO' => [
                'type' => 'CLOB',
            ],
            'ES_SOLUCION' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'N',
            ],
            'VISIBLE_USUARIO' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'S',
            ],
            'CREATED_AT' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->addForeignKey('TICKET_ID', 'TICKETS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('USUARIO_ID', 'USUARIOS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('COMENTARIO');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE COMENTARIOS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER COMENTARIOS_BI
            BEFORE INSERT ON COMENTARIOS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT COMENTARIOS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraints
        $this->db->query("ALTER TABLE COMENTARIOS ADD CONSTRAINT CHK_COMENTARIOS_ES_SOLUCION CHECK (ES_SOLUCION IN ('S', 'N'))");
        $this->db->query("ALTER TABLE COMENTARIOS ADD CONSTRAINT CHK_COMENTARIOS_VISIBLE CHECK (VISIBLE_USUARIO IN ('S', 'N'))");
    }

    public function down()
    {
        $this->forge->dropTable('COMENTARIO');
        $this->db->query("DROP SEQUENCE COMENTARIOS_SEQ");
    }
}
