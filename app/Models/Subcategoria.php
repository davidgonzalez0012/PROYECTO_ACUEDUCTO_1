<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar las subcategorías de los tickets.
 * Aquí defino la estructura y reglas para la tabla SUBCATEGORIAS.
 * Este modelo es sencillo, pero útil para organizar los tickets por subcategoría.
 */
class Subcategoria extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table            = 'SUBCATEGORIAS';
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
    protected $allowedFields    = [
        'NOMBRE ',         // Nombre de la subcategoría
        'CATEGORIA_ID',    // ID de la categoría a la que pertenece
    ];

    // Opciones de inserción y actualización
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // No uso casteo especial
    protected array $casts = [];
    protected array $castHandlers = [];

    // Fechas y timestamps
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validación (no hay reglas estrictas aquí, pero se pueden agregar)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
