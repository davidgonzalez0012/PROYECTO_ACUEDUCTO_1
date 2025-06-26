<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuariosTable extends Migration
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
            'EMAIL' => [
                'type'       => 'VARCHAR2',
                'constraint' => 100,
            ],
            'TELEFONO' => [
                'type'       => 'VARCHAR2',
                'constraint' => 20,
                'null'       => true,
            ],
            'DEPENDENCIA_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'ROL' => [
                'type'       => 'VARCHAR2',
                'constraint' => 20,
                'default'    => 'USUARIO',
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
            ' CONTRASENA' => [
                'type'       => 'VARCHAR2',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('ID', true);
        $this->forge->addUniqueKey('EMAIL');
        $this->forge->addForeignKey('DEPENDENCIA_ID', 'DEPENDENCIAS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('USUARIOS');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE USUARIOS_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER USUARIOS_BI
            BEFORE INSERT ON USUARIOS
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT USUARIOS_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraints
        $this->db->query("ALTER TABLE USUARIOS ADD CONSTRAINT CHK_USUARIOS_ACTIVO CHECK (ACTIVO IN ('S', 'N'))");
        $this->db->query("ALTER TABLE USUARIOS ADD CONSTRAINT CHK_USUARIOS_ROL CHECK (ROL IN ('USUARIO', 'SOPORTE', 'ADMIN'))");
    }

    public function down()
    {
        $this->forge->dropTable('USUARIOS');
        $this->db->query("DROP SEQUENCE USUARIOS_SEQ");
    }
}
