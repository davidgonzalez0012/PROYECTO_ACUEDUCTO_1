<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar los comentarios de los tickets.
 * Aquí defino la estructura y reglas para la tabla COMENTARIOS.
 * También agrego métodos útiles para consultar y marcar comentarios como solución.
 */
class ComentarioModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'COMENTARIOS';
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
        'TICKET_ID',         // ID del ticket al que pertenece el comentario
        'USUARIO_ID',        // ID del usuario que hizo el comentario
        'CONTENIDO',         // Texto del comentario
        'ES_SOLUCION',       // Si el comentario es la solución ('S' o 'N')
        'VISIBLE_USUARIO',   // Si el comentario es visible para el usuario ('S' o 'N')
        'CREATED_AT'         // Fecha de creación
    ];

    // No uso timestamps automáticos
    protected $useTimestamps = false;

    // Reglas de validación para los campos
    protected $validationRules = [
        'TICKET_ID' => 'required|integer',
        'USUARIO_ID' => 'required|integer',
        'CONTENIDO' => 'required',
        'ES_SOLUCION' => 'in_list[S,N]',
        'VISIBLE_USUARIO' => 'in_list[S,N]'
    ];

    // Callbacks para validar claves foráneas y establecer valores por defecto
    protected $beforeInsert = ['validateForeignKeys', 'setDefaults'];

    /**
     * Antes de insertar, reviso que existan el ticket y el usuario.
     */
    protected function validateForeignKeys(array $data)
    {
        if (isset($data['data']['TICKET_ID'])) {
            $ticketModel = new TicketModel();
            if (!$ticketModel->find($data['data']['TICKET_ID'])) {
                throw new \RuntimeException('El ticket especificado no existe');
            }
        }

        if (isset($data['data']['USUARIO_ID'])) {
            $usuarioModel = new UsuarioModel();
            if (!$usuarioModel->find($data['data']['USUARIO_ID'])) {
                throw new \RuntimeException('El usuario especificado no existe');
            }
        }

        return $data;
    }

    /**
     * Si no se especifican, dejo los valores por defecto para ES_SOLUCION y VISIBLE_USUARIO.
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['ES_SOLUCION'])) {
            $data['data']['ES_SOLUCION'] = 'N';
        }
        if (!isset($data['data']['VISIBLE_USUARIO'])) {
            $data['data']['VISIBLE_USUARIO'] = 'S';
        }
        return $data;
    }

    /**
     * Devuelvo todos los comentarios de un ticket, con opción de filtrar solo los visibles para el usuario.
     */
    public function getComentariosPorTicket($ticket_id, $visible_usuario = true)
    {
        $builder = $this->select("COMENTARIOS.*, USUARIOS.NOMBRE as USUARIO_NOMBRE, TO_CHAR(COMENTARIOS.CREATED_AT, 'YYYY-MM-DD HH24:MI:SS') AS CREATED_AT")
            ->join('USUARIOS', 'USUARIOS.ID = COMENTARIOS.USUARIO_ID')
            ->where('COMENTARIOS.TICKET_ID', $ticket_id);

        if ($visible_usuario) {
            $builder->where('COMENTARIOS.VISIBLE_USUARIO', 'S');
        }

        return $builder->orderBy('COMENTARIOS.ID', 'ASC')->findAll();
    }

    /**
     * Marco un comentario como solución.
     */
    public function marcarComoSolucion($id)
    {
        return $this->update($id, ['ES_SOLUCION' => 'S']);
    }
}
