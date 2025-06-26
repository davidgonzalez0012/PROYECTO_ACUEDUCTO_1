<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar las notificaciones del sistema (email o app).
 * Aquí defino la estructura y reglas para la tabla NOTIFICACIONES.
 * También agrego métodos útiles para consultar, marcar y listar notificaciones.
 */
class NotificacionModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'NOTIFICACIONES';
    // Clave primaria
    protected $primaryKey = 'ID';
    // Indico que la clave es autoincremental
    protected $useAutoIncrement = true;
    // Siempre devuelvo los resultados como array
    protected $returnType = 'array';
    // No uso soft deletes aquí
    protected $useSoftDeletes = false;

    // Campos permitidos para inserción o actualización masiva
    protected $allowedFields = [
        'USUARIO_ID',    // ID del usuario destinatario
        'TICKET_ID',     // ID del ticket relacionado
        'TIPO',          // Tipo de notificación (EMAIL, APP)
        'ASUNTO',        // Asunto de la notificación
        'MENSAJE',       // Mensaje de la notificación
        'ENVIADO',       // Si la notificación ya fue enviada ('S' o 'N')
        'SENT_AT'        // Fecha de envío
    ];

    // No uso timestamps automáticos
    protected $useTimestamps = false;

    // Reglas de validación para los campos
    protected $validationRules = [
        'USUARIO_ID' => 'required|integer',
        'TICKET_ID' => 'required|integer',
        'TIPO' => 'required|in_list[EMAIL,APP]',
        'ASUNTO' => 'required|max_length[200]',
        'MENSAJE' => 'required',
        'ENVIADO' => 'in_list[S,N]'
    ];

    // Callbacks para timestamps, claves foráneas y valores por defecto
    protected $beforeInsert = ['setTimestamps', 'validateForeignKeys', 'setDefaults'];

    /**
     * Antes de insertar, establezco la fecha de creación si no se proporciona.
     */
    protected function setTimestamps(array $data)
    {
        if (!isset($data['data']['CREATED_AT'])) {
            $data['data']['CREATED_AT'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Antes de insertar, reviso que existan el usuario y el ticket.
     */
    protected function validateForeignKeys(array $data)
    {
        if (isset($data['data']['USUARIO_ID'])) {
            $usuarioModel = new UsuarioModel();
            if (!$usuarioModel->find($data['data']['USUARIO_ID'])) {
                throw new \RuntimeException('El usuario especificado no existe');
            }
        }

        if (isset($data['data']['TICKET_ID'])) {
            $ticketModel = new TicketModel();
            if (!$ticketModel->find($data['data']['TICKET_ID'])) {
                throw new \RuntimeException('El ticket especificado no existe');
            }
        }

        return $data;
    }

    /**
     * Si no se especifica, dejo ENVIADO en 'N' (no enviado por defecto).
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['ENVIADO'])) {
            $data['data']['ENVIADO'] = 'N';
        }
        return $data;
    }

    /**
     * Devuelvo todas las notificaciones pendientes de envío, filtrando por tipo si se indica.
     */
    public function getNotificacionesPendientes($tipo = null)
    {
        $builder = $this->where('ENVIADO', 'N');
        
        if ($tipo) {
            $builder->where('TIPO', $tipo);
        }
        
        return $builder->orderBy('CREATED_AT', 'ASC')->findAll();
    }

    /**
     * Marco una notificación como enviada y registro la fecha de envío.
     */
    public function marcarComoEnviado($id)
    {
        return $this->update($id, [
            'ENVIADO' => 'S',
            'SENT_AT' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Devuelvo las notificaciones de un usuario, ordenadas por fecha de creación.
     */
    public function getNotificacionesPorUsuario($usuario_id, $limit = 10)
    {
        return $this->where('USUARIO_ID', $usuario_id)
                   ->orderBy('CREATED_AT', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
