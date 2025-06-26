<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar las dependencias (áreas/departamentos) de la empresa.
 * Aquí defino la estructura y reglas para la tabla DEPENDENCIAS.
 * También agrego métodos útiles para consultar dependencias activas y con usuarios.
 */
class DependenciaModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table            = 'DEPENDENCIAS';
    // Clave primaria
    protected $primaryKey       = 'ID';
    // Indico que la clave es autoincremental
    protected $useAutoIncrement = true;
    // Siempre devuelvo los resultados como array
    protected $returnType       = 'array';
    // No uso soft deletes aquí
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Campos permitidos para inserción o actualización masiva
    protected $allowedFields = [
        'NOMBRE',         // Nombre de la dependencia
        'DESCRIPCION',    // Descripción de la dependencia
        'ACTIVO'          // Si la dependencia está activa ('S' o 'N')
    ];

    // Opciones de inserción y actualización
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // No uso casteo especial
    protected array $casts = [];
    protected array $castHandlers = [];

    // Fechas y timestamps
    protected $useTimestamps = FALSE;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Reglas de validación para los campos
    protected $validationRules = [
        'NOMBRE' => 'required|max_length[100]',
        'DESCRIPCION' => 'permit_empty|max_length[500]',
        'ACTIVO' => 'in_list[S,N]'
    ];

    // Mensajes personalizados para validación
    protected $validationMessages = [
        'NOMBRE' => [
            'required' => 'El nombre de la dependencia es obligatorio',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Callbacks personalizados para timestamps y unicidad
    protected $beforeInsert = ['setTimestamps', 'validateUnique'];
    protected $beforeUpdate = ['setTimestamps', 'setDefaults'];

    /**
     * Antes de insertar o actualizar, establezco las fechas de creación y actualización.
     */
    protected function setTimestamps(array $data)
    {
        $now = date('Y-m-d H:i:s');
        
        if (!isset($data['data']['CREATED_AT'])) {
            $data['data']['CREATED_AT'] = $now;
        }
        $data['data']['UPDATED_AT'] = $now;
        
        return $data;
    }

    /**
     * Si no se especifica el campo ACTIVO, lo dejo en 'S' (activo por defecto).
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['ACTIVO'])) {
            $data['data']['ACTIVO'] = 'S';
        }
        return $data;
    }

    /**
     * Antes de insertar, reviso que no exista otra dependencia con el mismo nombre.
     */
    protected function validateUnique(array $data)
    {
        if (isset($data['data']['NOMBRE'])) {
            $existing = $this->where('NOMBRE', $data['data']['NOMBRE'])->first();
            if ($existing) {
                throw new \RuntimeException('Ya existe una dependencia con este nombre');
            }
        }
        return $data;
    }

    /**
     * Devuelvo todas las dependencias activas (útil para combos y filtros).
     */
    public function getDependenciasActivas()
    {
        return $this->where('ACTIVO', 'S')->findAll();
    }

    /**
     * Devuelvo una dependencia junto con la cantidad de usuarios asociados.
     * Esto es útil para estadísticas o reportes.
     */
    public function getDependenciaConUsuarios($id)
    {
        return $this->select('D.*, COUNT(U.ID) as TOTAL_USUARIOS')
                   ->from('DEPENDENCIAS D')
                   ->join('USUARIOS U', 'U.DEPENDENCIA_ID = D.ID', 'left')
                   ->where('D.ID', $id)
                   ->groupBy('D.ID, D.NOMBRE, D.DESCRIPCION, D.ACTIVO, D.CREATED_AT, D.UPDATED_AT')
                   ->first();
    }
}
