<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de archivos adjuntos a los tickets.
 * Representa la tabla ARCHIVOS y contiene métodos para validación y consultas relacionadas.
 */
class ArchivoModel extends Model
{
    /**
     * Nombre de la tabla asociada en la base de datos.
     */
    protected $table = 'ARCHIVOS';

    /**
     * Clave primaria de la tabla.
     */
    protected $primaryKey = 'ID';

    /**
     * Indica si la clave primaria es autoincremental.
     */
    protected $useAutoIncrement = true; 

    /**
     * Tipo de dato que retorna el modelo (array).
     */
    protected $returnType = 'array';

    /**
     * Indica si se usan borrados suaves (soft deletes).
     */
    protected $useSoftDeletes = false;

    /**
     * Campos permitidos para inserción y actualización masiva.
     * Estos campos corresponden a las columnas de la tabla ARCHIVOS.
     */
    protected $allowedFields = [
        'TICKET_ID',      // ID del ticket al que pertenece el archivo
        'USUARIO_ID',     // ID del usuario que subió el archivo
        'NOMBRE_ARCHIVO', // Nombre del archivo
        'RUTA_ARCHIVO',   // Ruta física donde se almacena el archivo
        'TAMANO'          // Tamaño del archivo en bytes
    ];
 
    /**
     * No se usan timestamps automáticos en este modelo.
     */
    protected $useTimestamps = false;

    /**
     * Reglas de validación para los campos del modelo.
     */
    protected $validationRules = [
        'TICKET_ID' => 'required|integer',
        'USUARIO_ID' => 'required|integer',
        'NOMBRE_ARCHIVO' => 'required|max_length[255]',
        'RUTA_ARCHIVO' => 'required|max_length[500]',
        'TAMANO' => 'permit_empty|integer'
    ];

    /**
     * Filtros que se ejecutan antes de insertar registros.
     */
    protected $beforeInsert = ['setTimestamps', 'validateForeignKeys'];

    /**
     * Establece la fecha y hora de subida si no se proporciona.
     * @param array $data Datos a insertar
     * @return array Datos con timestamp
     */
    protected function setTimestamps(array $data)
    {
        if (!isset($data['data']['UPLOADED_AT'])) {
            $data['data']['UPLOADED_AT'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Valida que las claves foráneas (ticket y usuario) existan antes de insertar.
     * @param array $data Datos a validar
     * @return array Datos validados o excepción si alguna clave no existe
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
     * Obtiene todos los archivos asociados a un ticket, incluyendo el nombre del usuario que los subió.
     * @param int $ticket_id ID del ticket
     * @return array Lista de archivos
     */
    public function getArchivosPorTicket($ticket_id)
    {
        return $this->select('A.*, U.NOMBRE as USUARIO_NOMBRE')
                   ->from('ARCHIVOS A')
                   ->join('USUARIOS U', 'U.ID = A.USUARIO_ID')
                   ->where('A.TICKET_ID', $ticket_id)
                   ->orderBy('A.UPLOADED_AT', 'DESC')
                   ->findAll();
    }

    /**
     * Obtiene el tamaño total de los archivos asociados a un ticket.
     * @param int $ticket_id ID del ticket
     * @return int Tamaño total en bytes
     */
    public function getTotalTamañoPorTicket($ticket_id)
    {
        $result = $this->select('SUM(TAMANO) as TOTAL_TAMANO')
                      ->where('TICKET_ID', $ticket_id)
                      ->first();
        
        return $result['TOTAL_TAMANO'] ?? 0;
    }
}
