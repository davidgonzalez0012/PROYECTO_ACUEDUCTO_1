<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de usuarios del sistema.
 * Representa la tabla USUARIOS y contiene métodos para validación, autenticación y consultas relacionadas.
 */
class UsuarioModel extends Model
{
    /**
     * Nombre de la tabla asociada en la base de datos.
     */
    protected $table = 'USUARIOS';

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
     * Estos campos corresponden a las columnas de la tabla USUARIOS.
     */
    protected $allowedFields = [
        'NOMBRE',         // Nombre completo del usuario
        'EMAIL',          // Correo electrónico
        'TELEFONO',       // Teléfono de contacto
        'DEPENDENCIA_ID', // ID de la dependencia a la que pertenece
        'ROL',            // Rol del usuario (ADMIN, SOPORTE, USUARIO, etc.)
        'ACTIVO',         // Estado de activación ('S' = Sí, 'N' = No)
        'CONTRASENA',     // Contraseña encriptada
        'CREATED_AT',     // Fecha de creación
        'UPDATED_AT'      // Fecha de actualización
    ];

    /**
     * Habilita el uso de timestamps automáticos para CREATED_AT y UPDATED_AT.
     */
    protected $useTimestamps = true;
    protected $createdField = 'CREATED_AT';
    protected $updatedField = 'UPDATED_AT';

    /**
     * Reglas de validación para los campos del modelo.
     */
    protected $validationRules = [
        'NOMBRE' => 'required|max_length[100]',
        'EMAIL' => 'required|valid_email|max_length[100]',
        'TELEFONO' => 'permit_empty|max_length[20]',
        'DEPENDENCIA_ID' => 'required|integer',
        // Solo permite ciertos valores para el rol
        'ROL' => 'required|in_list[USUARIO,SOPORTE,ADMIN]',
        'ACTIVO' => 'in_list[S,N]',
        'CONTRASENA' => 'required|min_length[6]|max_length[255]'
    ];

    /**
     * Mensajes personalizados para las reglas de validación.
     */
    protected $validationMessages = [
        'EMAIL' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'Debe ingresar un email válido'
        ]
    ];

    /**
     * Filtros que se ejecutan antes de insertar o actualizar registros.
     */
    protected $beforeInsert = ['validateUnique', 'validateForeignKeys', 'setDefaults'];
    protected $beforeUpdate = ['validateForeignKeys', 'setDefaults'];

    /**
     * Valida que el email sea único antes de insertar un usuario.
     * @param array $data Datos a insertar
     * @return array Datos validados o excepción si ya existe el email
     */
    protected function validateUnique(array $data)
    {
        if (isset($data['data']['EMAIL'])) {
            $existing = $this->where('EMAIL', $data['data']['EMAIL'])->first();
            if ($existing) {
                throw new \RuntimeException('Ya existe un usuario con este email');
            }
        }
        return $data;
    }

    /**
     * Valida que la dependencia asociada exista antes de insertar o actualizar.
     * @param array $data Datos a validar
     * @return array Datos validados o excepción si la dependencia no existe
     */
    protected function validateForeignKeys(array $data)
    {
        if (isset($data['data']['DEPENDENCIA_ID'])) {
            $dependenciaModel = new DependenciaModel();
            if (!$dependenciaModel->find($data['data']['DEPENDENCIA_ID'])) {
                throw new \RuntimeException('La dependencia seleccionada no existe');
            }
        }
        return $data;
    }

    /**
     * Establece valores por defecto para los campos ACTIVO y ROL si no se proporcionan.
     * @param array $data Datos a insertar o actualizar
     * @return array Datos con valores por defecto
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['ACTIVO'])) {
            $data['data']['ACTIVO'] = 'S';
        }
        if (!isset($data['data']['ROL'])) {
            $data['data']['ACTIVO'] = 'USUARIO';
        }
        return $data;
    }

    /**
     * Obtiene un usuario junto con el nombre de su dependencia.
     * @param int $id ID del usuario
     * @return array|null Usuario con datos de dependencia
     */
    public function getUsuarioConDependencia($id)
    {
        return $this->select('USUARIOS.*, DEPENDENCIAS.NOMBRE as DEPENDENCIA_NOMBRE')
                    ->join('DEPENDENCIAS', 'DEPENDENCIAS.ID = USUARIOS.DEPENDENCIA_ID')
                    ->where('USUARIOS.ID', $id)
                    ->first();
    }

    /**
     * Obtiene todos los usuarios activos.
     * @return array Lista de usuarios activos
     */
    public function getUsuariosActivos()
    {
        return $this->where('ACTIVO', 'S')->findAll();
    }

    /**
     * Obtiene todos los usuarios con rol SOPORTE o ADMIN que estén activos.
     * @return array Lista de usuarios de soporte o admin
     */
    public function getUsuariosSoporte()
    {
        return $this->whereIn('ROL', ['SOPORTE', 'ADMIN'])
                   ->where('ACTIVO', 'S')
                   ->findAll();
    }

    /**
     * Obtiene todos los usuarios activos de una dependencia específica.
     * @param int $dependencia_id ID de la dependencia
     * @return array Lista de usuarios de la dependencia
     */
    public function getUsuariosPorDependencia($dependencia_id)
    {
        return $this->where('DEPENDENCIA_ID', $dependencia_id)
                   ->where('ACTIVO', 'S')
                   ->findAll();
    }

    /**
     * Valida el login de un usuario según email y rol.
     * Si el rol es ADMIN o SOPORTE, lo filtra exactamente; si no, permite cualquier otro rol.
     * @param string $email Email del usuario
     * @param string|null $rol Rol solicitado
     * @return array|null Usuario encontrado o null
     */
    public function validateLogin($email, $rol = null)
    {
        $builder = $this->builder()->select('ID, EMAIL, ROL, NOMBRE, CONTRASENA, ACTIVO');
        $builder->where('EMAIL', $email);
        $builder->where('ACTIVO', 'S');
        
        if ($rol === 'ADMIN' || $rol === 'SOPORTE') {
            $builder->where('ROL', $rol);
        } else {
            // Para empleados y otros roles, excluye admin y soporte
            $builder->where("(ROL != 'ADMIN' AND ROL != 'SOPORTE')");
        }
        
        return $builder->get()->getRowArray();
    }
}
