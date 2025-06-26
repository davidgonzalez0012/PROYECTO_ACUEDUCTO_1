<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfiguracionEmailTable extends Migration
{
   public function up()
    {
           $this->forge->addField([
        'ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
                'null'       => false,
            ],
            'DEPENDENCIA_ID' => [
                'type'       => 'NUMBER',
                'constraint' => 11,
            ],
            'SMTP_HOST' => [
                'type'       => 'VARCHAR2',
                'constraint' => 100,
                'null'       => true,
            ],
            'SMTP_PORT' => [
                'type'    => 'NUMBER',
                'default' => 587,
            ],
            'SMTP_USER' => [
                'type'       => 'VARCHAR2',
                'constraint' => 100,
                'null'       => true,
            ],
            'SMTP_PASSWORD' => [
                'type'       => 'VARCHAR2',
                'constraint' => 200,
                'null'       => true,
            ],
            'EMAIL_FROM' => [
                'type'       => 'VARCHAR2',
                'constraint' => 100,
                'null'       => true,
            ],
            'SSL_ENABLED' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'S',
            ],
         ]);


        $this->forge->addKey('ID', true);
        $this->forge->addForeignKey('DEPENDENCIA_ID', 'DEPENDENCIAS', 'ID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('CONFIGURACION_EMAIL');

        // Secuencia para autoincremento
        $this->db->query("CREATE SEQUENCE CONFIGURACION_EMAIL_SEQ START WITH 1 INCREMENT BY 1");

        // Trigger para autoincremento
        $this->db->query("
            CREATE OR REPLACE TRIGGER CONFIGURACION_EMAIL_BI
            BEFORE INSERT ON CONFIGURACION_EMAIL
            FOR EACH ROW
            BEGIN
                IF :NEW.ID IS NULL THEN
                    SELECT CONFIGURACION_EMAIL_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
                END IF;
            END;
        ");

        // Agregar constraint
        $this->db->query("ALTER TABLE CONFIGURACION_EMAIL ADD CONSTRAINT CHK_CONFIG_EMAIL_SSL CHECK (SSL_ENABLED IN ('S', 'N'))");
    }

    public function down()
    {
        $this->forge->dropTable('CONFIGURACION_EMAIL');
        $this->db->query("DROP SEQUENCE CONFIGURACION_EMAIL_SEQ");
    }
}
