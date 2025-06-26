<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar las categorías de los tickets.
 * Aquí defino la estructura y las reglas para la tabla CATEGORIAS.
 * También agrego algunos métodos útiles para consultar categorías activas y con tickets.
 */
class CategoriaModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'CATEGORIAS';
    // Clave primaria
    protected $primaryKey = 'ID';
    // Indico que la clave es autoincremental
    protected $useAutoIncrement = true;
    // Siempre devuelvo los resultados como array
    protected $returnType = 'array';
    // No uso soft deletes aquí
    protected $useSoftDeletes = false;

    // Estos son los campos que permito insertar o actualizar masivamente
    protected $allowedFields = [
        'NOMBRE',              // Nombre de la categoría
        'DESCRIPCION',         // Descripción opcional
        'PRIORIDAD_DEFAULT',   // Prioridad por defecto (1-4)
        'ACTIVO',               // Si la categoría está activa ('S' o 'N')
        'ENCARAGADO_ID' // DETERMINA EL ID DEL USUARIO SOPORTE ENCARGADO DE LA CATEGORIA

    ];

    // No uso timestamps automáticos
    protected $useTimestamps = false;

    // Reglas de validación para los campos
    protected $validationRules = [
        'NOMBRE' => 'required|max_length[100]',
        'DESCRIPCION' => 'permit_empty|max_length[500]',
        'PRIORIDAD_DEFAULT' => 'required|in_list[1,2,3,4]',
        'ACTIVO' => 'in_list[S,N]'
    ];

    // Mensajes personalizados para validación
    protected $validationMessages = [
        'NOMBRE' => [
            'required' => 'El nombre de la categoría es obligatorio'
        ],
        'PRIORIDAD_DEFAULT' => [
            'required' => 'La prioridad por defecto es obligatoria',
            'in_list' => 'La prioridad debe ser: 1=Baja, 2=Media, 3=Alta, 4=Crítica'
        ]
    ];

    // Callbacks para validar unicidad y establecer valores por defecto
    protected $beforeInsert = ['validateUnique', 'setDefaults'];
    protected $beforeUpdate = ['setDefaults'];

    /**
     * Antes de insertar, reviso que no exista otra categoría con el mismo nombre.
     * Si existe, lanzo una excepción.
     */
    protected function validateUnique(array $data)
    {
        if (isset($data['data']['NOMBRE'])) {
            $existing = $this->where('NOMBRE', $data['data']['NOMBRE'])->first();
            if ($existing) {
                throw new \RuntimeException('Ya existe una categoría con este nombre');
            }
        }
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
     * Devuelvo todas las categorías activas (útil para combos y filtros).
     */
    public function getCategoriasActivas()
    {
        return $this->where('ACTIVO', 'S')->findAll();
    }

    /**
     * Devuelvo una categoría junto con la cantidad de tickets asociados.
     * Esto es útil para estadísticas o reportes.
     */
    public function getCategoriaConTickets($id)
    {
        return $this->select('C.*, COUNT(T.ID) as TOTAL_TICKETS')
                   ->from('CATEGORIAS C')
                   ->join('TICKETS T', 'T.CATEGORIA_ID = C.ID', 'left')
                   ->where('C.ID', $id)
                   ->groupBy('C.ID, C.NOMBRE, C.DESCRIPCION, C.PRIORIDAD_DEFAULT, C.ACTIVO')
                   ->first();
    }

}
