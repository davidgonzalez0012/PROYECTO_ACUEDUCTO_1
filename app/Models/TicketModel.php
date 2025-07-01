<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de tickets en el sistema.
 * Representa la tabla TICKETS y contiene métodos para inserción, actualización y consultas relacionadas.
 */
class TicketModel extends Model
{
    /**
     * Nombre de la tabla asociada en la base de datos.
     */
    protected $table = 'TICKETS';

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
     * Estos campos corresponden a las columnas de la tabla TICKETS.
     */
    protected $allowedFields = [
        'TITULO',           // Título del ticket
        'DESCRIPCION',      // Descripción del problema o solicitud
        'USUARIO_ID',       // ID del usuario que crea el ticket
        'CATEGORIA_ID',     // ID de la categoría del ticket
        'SUBCATEGORIA_ID',  // ID de la subcategoría (opcional)
        'DEPENDENCIA_ID',   // ID de la dependencia relacionada
        'ASIGNADO_A',       // ID del usuario asignado (soporte)
        'ESTADO',           // Estado del ticket (ABIERTO, EN_PROCESO, CERRADO)
        'PRIORIDAD',        // Prioridad del ticket (BAJA, MEDIA, ALTA, CRITICA)
        'FECHA_ACTUALIZACION', // Fecha de última actualización
        'FECHA_CIERRE'      // Fecha de cierre del ticket
    ];

    /**
     * No se usan timestamps automáticos en este modelo.
     */
    protected $useTimestamps = false; 

    /**
     * Reglas de validación para los campos del modelo.
     */
    protected $validationRules = [
        'TITULO' => 'required|max_length[200]',
        'DESCRIPCION' => 'required',
        'USUARIO_ID' => 'required|integer',
        'CATEGORIA_ID' => 'required|integer',
        'ASIGNADO_A' => 'permit_empty|integer',
        'ESTADO' => 'in_list[ABIERTO,EN_PROCESO,RESUELTO,CERRADO]',
        'PRIORIDAD' => 'in_list[BAJA,MEDIA,ALTA,CRITICA]'
    ];

    /**
     * Mensajes personalizados para las reglas de validación.
     */
    protected $validationMessages = [
        'TITULO' => [
            'required' => 'El título del ticket es obligatorio'
        ],
        'DESCRIPCION' => [
            'required' => 'La descripción del ticket es obligatoria'
        ]
    ];

    /**
     * Filtros que se ejecutan antes de insertar o actualizar registros.
     */
    protected $beforeInsert = ['validateForeignKeys', 'setDefaults'];
    protected $beforeUpdate = ['validateForeignKeys'];

    /**
     * Valida que las claves foráneas (usuario, categoría, asignado) existan antes de insertar o actualizar.
     * @param array $data Datos a validar
     * @return array Datos validados o excepción si alguna clave no existe
     */
    protected function validateForeignKeys(array $data)
    {
        if (isset($data['data']['USUARIO_ID'])) {
            $usuarioModel = new UsuarioModel();
            if (!$usuarioModel->find($data['data']['USUARIO_ID'])) {
                throw new \RuntimeException('El usuario especificado no existe');
            }
        }

        if (isset($data['data']['CATEGORIA_ID'])) {
            $categoriaModel = new CategoriaModel();
            if (!$categoriaModel->find($data['data']['CATEGORIA_ID'])) {
                throw new \RuntimeException('La categoría especificada no existe');
            }
        }

        if (isset($data['data']['ASIGNADO_A']) && !empty($data['data']['ASIGNADO_A'])) {
            $usuarioModel = new UsuarioModel();
            if (!$usuarioModel->find($data['data']['ASIGNADO_A'])) {
                throw new \RuntimeException('El usuario asignado no existe');
            }
        }

        return $data;
    }

    /**
     * Establece valores por defecto para los campos ESTADO y PRIORIDAD si no se proporcionan.
     * @param array $data Datos a insertar o actualizar
     * @return array Datos con valores por defecto
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['ESTADO'])) {
            $data['data']['ESTADO'] = 'ABIERTO';
        }
        if (!isset($data['data']['PRIORIDAD'])) {
            $data['data']['PRIORIDAD'] = 'MEDIA';
        }
        return $data;
    }

    /**
     * Inserta un ticket en la base de datos usando Query Builder y maneja fechas para Oracle.
     * @param array $data Datos del ticket a insertar
     * @return int|false ID del ticket insertado o false si falla
     */
    public function insertarTicket(array $data)
    {
        $db = \Config\Database::connect();

        try {
            // Validar datos antes de insertar
            if (!$this->validate($data)) {
                return false;
            }

            // Ejecutar callbacks de validación
            $callbackData = ['data' => $data];
            $callbackData = $this->validateForeignKeys($callbackData);
            $callbackData = $this->setDefaults($callbackData);
            $data = $callbackData['data'];

            // Normalizar SUBCATEGORIA_ID
            $subcategoria_id = $data['SUBCATEGORIA_ID'] ?? null;
            if ($subcategoria_id === '' || $subcategoria_id === null) {
                $subcategoria_id = null;
            } else {
                $subcategoria_id = (int)$subcategoria_id;
            }

            // Normalizar DEPENDENCIA_ID
            $dependencia_id = $data['DEPENDENCIA_ID'] ?? null;
            if ($dependencia_id === '' || $dependencia_id === null) {
                $dependencia_id = null;
            } else {
                $dependencia_id = (int)$dependencia_id;
            }

            // Preparar la consulta con SYSDATE para fechas
            $sql = "INSERT INTO TICKETS (
                        TITULO, 
                        DESCRIPCION, 
                        USUARIO_ID, 
                        CATEGORIA_ID, 
                        SUBCATEGORIA_ID,
                        DEPENDENCIA_ID,
                        PRIORIDAD, 
                        ESTADO, 
                        FECHA_CREACION, 
                        FECHA_ACTUALIZACION
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, SYSDATE, SYSDATE)";

            $result = $db->query($sql, [
                $data['TITULO'],
                $data['DESCRIPCION'],
                $data['USUARIO_ID'],
                $data['CATEGORIA_ID'],
                $subcategoria_id,
                $dependencia_id,
                $data['PRIORIDAD'],
                $data['ESTADO']
            ]);

            if ($result) {
                // Para Oracle: obtener el último ID insertado usando la secuencia
                $query = $db->query("SELECT TICKETS_SEQ.CURRVAL AS ID FROM dual");
                $row = $query->getRow();
                $insertId = $row->ID ?? null;
                return $insertId;
            }

            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error al insertar ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un ticket en la base de datos con manejo de fechas para Oracle.
     * @param int $id ID del ticket a actualizar
     * @param array $data Datos a actualizar
     * @return bool True si la actualización fue exitosa, false si no
     */
    public function actualizarTicket($id, array $data)
    {
        $db = \Config\Database::connect();

        // Eliminar FECHA_CREACION si viene en el array de datos
        if (isset($data['FECHA_CREACION'])) {
            unset($data['FECHA_CREACION']);
        }

        try {
            // Construir la consulta dinámicamente
            $setClauses = [];
            $params = [];

            foreach ($data as $field => $value) {
                if (in_array($field, $this->allowedFields)) {
                    if ($field === 'FECHA_CIERRE') {
                        $setClauses[] = "FECHA_CIERRE = SYSDATE";
                        // No agregues el valor a $params
                        continue;
                    }
                    $setClauses[] = "{$field} = ?";
                    $params[] = $value;
                }
            }

            // Agregar FECHA_ACTUALIZACION automáticamente
            $setClauses[] = "FECHA_ACTUALIZACION = SYSDATE";

            // Agregar ID al final de los parámetros
            $params[] = $id;

            $sql = "UPDATE TICKETS SET " . implode(', ', $setClauses) . " WHERE ID = ?";

            return $db->query($sql, $params);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar ticket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cierra un ticket, actualizando el estado y las fechas correspondientes.
     * @param int $id ID del ticket a cerrar
     * @return bool True si la operación fue exitosa
     */
    public function cerrarTicket($id)
    {
        $db = \Config\Database::connect();

        $sql = "UPDATE TICKETS SET 
                    ESTADO = 'CERRADO',
                    FECHA_CIERRE = SYSDATE,
                    FECHA_ACTUALIZACION = SYSDATE
                WHERE ID = ?";

        return $db->query($sql, [$id]);
    }

    /**
     * Obtiene todos los datos completos de un ticket, incluyendo información de usuario, categoría, asignado y dependencia.
     * @param int $id ID del ticket
     * @return array|null Datos completos del ticket
     */
    public function getTicketCompleto($id)
    {
        return $this->select('T.ID, T.TITULO, T.DESCRIPCION, T.ESTADO, T.PRIORIDAD,
                             T.FECHA_CREACION, T.FECHA_ACTUALIZACION, T.FECHA_CIERRE,
                             U.NOMBRE as USUARIO_NOMBRE,
                             U.EMAIL as USUARIO_EMAIL,
                             C.NOMBRE as CATEGORIA_NOMBRE,
                             A.NOMBRE as ASIGNADO_NOMBRE,
                             D.NOMBRE as DEPENDENCIA_NOMBRE')
            ->from('TICKETS T')
            ->join('USUARIOS U', 'U.ID = T.USUARIO_ID')
            ->join('CATEGORIAS C', 'C.ID = T.CATEGORIA_ID')
            ->join('USUARIOS A', 'A.ID = T.ASIGNADO_A', 'left')
            ->join('DEPENDENCIAS D', 'D.ID = U.DEPENDENCIA_ID')
            ->where('T.ID', $id)
            ->first();
    }

    /**
     * Obtiene todos los tickets creados por un usuario específico.
     * @param int $usuario_id ID del usuario
     * @param int|null $limit Límite de resultados (opcional)
     * @return array Lista de tickets
     */
    public function getTicketsPorUsuario($usuario_id, $limit = null)
    {
        $builder = $this->db->query(
            "SELECT T.ID, T.TITULO, T.ESTADO, T.PRIORIDAD, 
                    TO_CHAR(T.FECHA_CREACION, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_CREACION, 
                    C.NOMBRE as CATEGORIA_NOMBRE
             FROM TICKETS T
             JOIN CATEGORIAS C ON C.ID = T.CATEGORIA_ID
             WHERE T.USUARIO_ID = ?
             ORDER BY T.FECHA_CREACION DESC" .
             ($limit ? " FETCH FIRST $limit ROWS ONLY" : ""),
            [$usuario_id]
        );
        return $builder->getResultArray();
    }

    /**
     * Obtiene todos los tickets asignados a un usuario de soporte (no cerrados).
     * @param int $usuario_id ID del usuario asignado
     * @return array Lista de tickets asignados
     */
    public function getTicketsAsignados($usuario_id)
    {
        return $this->select('T.*, U.NOMBRE as USUARIO_NOMBRE, C.NOMBRE as CATEGORIA_NOMBRE')
            ->from('TICKETS T')
            ->join('USUARIOS U', 'U.ID = T.USUARIO_ID')
            ->join('CATEGORIAS C', 'C.ID = T.CATEGORIA_ID')
            ->where('T.ASIGNADO_A', $usuario_id)
            ->whereNotIn('T.ESTADO', ['CERRADO'])
            ->orderBy('CASE T.PRIORIDAD 
                             WHEN \'CRITICA\' THEN 1 
                             WHEN \'ALTA\' THEN 2 
                             WHEN \'MEDIA\' THEN 3 
                             ELSE 4 END', 'ASC')
            ->orderBy('T.FECHA_CREACION', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene todos los tickets por estado.
     * @param string $estado Estado del ticket (ABIERTO, EN_PROCESO, etc.)
     * @return array Lista de tickets
     */
    public function getTicketsPorEstado($estado)
    {
        return $this->where('ESTADO', $estado)->findAll();
    }

    /**
     * Obtiene estadísticas de tickets agrupados por estado.
     * @return array Lista de estados y cantidad de tickets
     */
    public function getEstadisticasTickets()
    {
        return $this->select('ESTADO, COUNT(*) as TOTAL')
            ->groupBy('ESTADO')
            ->findAll();
    }

    /**
     * Obtiene los tickets más recientes creados por un usuario.
     * @param int $usuario_id ID del usuario
     * @param int $limit Límite de resultados (por defecto 10)
     * @return array Lista de tickets recientes
     */
    public function getTicketsRecientesPorUsuario($usuario_id, $limit = 10)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT T.ID, T.TITULO, T.ESTADO, T.PRIORIDAD, 
                       TO_CHAR(T.FECHA_CREACION, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_CREACION, 
                       T.DESCRIPCION, T.CATEGORIA_ID, C.NOMBRE AS CATEGORIA_NOMBRE
                FROM TICKETS T
                JOIN CATEGORIAS C ON C.ID = T.CATEGORIA_ID
                WHERE T.USUARIO_ID = ?
                ORDER BY T.FECHA_CREACION DESC
                FETCH FIRST ? ROWS ONLY";
        $query = $db->query($sql, [$usuario_id, $limit]);
        return $query->getResultArray();
    }
}
