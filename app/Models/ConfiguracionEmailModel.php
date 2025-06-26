<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para manejar la configuración de correo SMTP por dependencia.
 * Aquí defino la estructura y reglas para la tabla CONFIGURACION_EMAIL.
 * Permite obtener y validar la configuración de email para cada dependencia.
 */
class ConfiguracionEmailModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'CONFIGURACION_EMAIL';
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
        'DEPENDENCIA_ID',   // ID de la dependencia
        'SMTP_HOST',        // Servidor SMTP
        'SMTP_PORT',        // Puerto SMTP
        'SMTP_USER',        // Usuario SMTP
        'SMTP_PASSWORD',    // Contraseña SMTP
        'EMAIL_FROM',       // Correo remitente
        'SSL_ENABLED'       // Si usa SSL ('S' o 'N')
    ];

    // No uso timestamps automáticos
    protected $useTimestamps = false;

    // Reglas de validación para los campos
    protected $validationRules = [
        'DEPENDENCIA_ID' => 'required|integer',
        'SMTP_HOST' => 'permit_empty|max_length[100]',
        'SMTP_PORT' => 'permit_empty|integer',
        'SMTP_USER' => 'permit_empty|max_length[100]',
        'SMTP_PASSWORD' => 'permit_empty|max_length[200]',
        'EMAIL_FROM' => 'permit_empty|valid_email|max_length[100]',
        'SSL_ENABLED' => 'in_list[S,N]'
    ];

    // Callbacks para validar claves foráneas y establecer valores por defecto
    protected $beforeInsert = ['validateForeignKeys', 'setDefaults'];
    protected $beforeUpdate = ['validateForeignKeys', 'setDefaults'];

    /**
     * Antes de insertar o actualizar, reviso que exista la dependencia.
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
     * Si no se especifican, dejo los valores por defecto para SSL_ENABLED y SMTP_PORT.
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['SSL_ENABLED'])) {
            $data['data']['SSL_ENABLED'] = 'N';
        }
        if (!isset($data['data']['SMTP_PORT'])) {
            $data['data']['SMTP_PORT'] = 587;
        }
        return $data;
    }

    /**
     * Devuelvo la configuración de email para una dependencia específica.
     */
    public function getConfiguracionPorDependencia($dependencia_id)
    {
        return $this->where('DEPENDENCIA_ID', $dependencia_id)->first();
    }
}
