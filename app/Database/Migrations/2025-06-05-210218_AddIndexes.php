<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexes extends Migration
{
   public function up()
    {
        // Índices para USUARIOS
        $this->db->query("CREATE INDEX IDX_USUARIOS_EMAIL ON USUARIOS (EMAIL)");
        $this->db->query("CREATE INDEX IDX_USUARIOS_DEPENDENCIA ON USUARIOS (DEPENDENCIA_ID)");
        $this->db->query("CREATE INDEX IDX_USUARIOS_ROL ON USUARIOS (ROL)");

        // Índices para TICKETS
        $this->db->query("CREATE INDEX IDX_TICKETS_USUARIO ON TICKETS (USUARIO_ID)");
        $this->db->query("CREATE INDEX IDX_TICKETS_ASIGNADO ON TICKETS (ASIGNADO_A)");
        $this->db->query("CREATE INDEX IDX_TICKETS_ESTADO ON TICKETS (ESTADO)");
        $this->db->query("CREATE INDEX IDX_TICKETS_PRIORIDAD ON TICKETS (PRIORIDAD)");
        $this->db->query("CREATE INDEX IDX_TICKETS_FECHA_CREACION ON TICKETS (FECHA_CREACION)");
        $this->db->query("CREATE INDEX IDX_TICKETS_CATEGORIA ON TICKETS (CATEGORIA_ID)");

        // Índices para COMENTARIOS
        $this->db->query("CREATE INDEX IDX_COMENTARIOS_TICKET ON COMENTARIOS (TICKET_ID)");
        $this->db->query("CREATE INDEX IDX_COMENTARIOS_USUARIO ON COMENTARIOS (USUARIO_ID)");
        $this->db->query("CREATE INDEX IDX_COMENTARIOS_FECHA ON COMENTARIOS (CREATED_AT)");

        // Índices para ARCHIVOS
        $this->db->query("CREATE INDEX IDX_ARCHIVOS_TICKET ON ARCHIVOS (TICKET_ID)");
        $this->db->query("CREATE INDEX IDX_ARCHIVOS_USUARIO ON ARCHIVOS (USUARIO_ID)");

        // Índices para NOTIFICACIONES
        $this->db->query("CREATE INDEX IDX_NOTIF_USUARIO ON NOTIFICACIONES (USUARIO_ID)");
        $this->db->query("CREATE INDEX IDX_NOTIF_TICKET ON NOTIFICACIONES (TICKET_ID)");
        $this->db->query("CREATE INDEX IDX_NOTIF_ENVIADO ON NOTIFICACIONES (ENVIADO)");
    }

    public function down()
    {
       
        /*
        $this->db->query("DROP INDEX IDX_USUARIOS_EMAIL");
        $this->db->query("DROP INDEX IDX_USUARIOS_DEPENDENCIA");
        $this->db->query("DROP INDEX IDX_USUARIOS_ROL");
        // ... etc
        */
    }
}
