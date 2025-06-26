<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TicketSystemSeeder extends Seeder
{
   public function run()
    {
        // Insertar dependencias base
        $dependencias = [
            ['NOMBRE' => 'SISTEMAS', 'DESCRIPCION' => 'DEPARTAMENTO DE SISTEMAS E INFORMATICA'],
            ['NOMBRE' => 'RECURSOS HUMANOS', 'DESCRIPCION' => 'DEPARTAMENTO DE RECURSOS HUMANOS'],
            ['NOMBRE' => 'CONTABILIDAD', 'DESCRIPCION' => 'DEPARTAMENTO DE CONTABILIDAD'],
            ['NOMBRE' => 'ADMINISTRACION', 'DESCRIPCION' => 'DEPARTAMENTO ADMINISTRATIVO'],
        ];

        $this->db->table('DEPENDENCIAS')->insertBatch($dependencias);

        // Insertar categorías base
        $categorias = [
            ['NOMBRE' => 'HARDWARE', 'DESCRIPCION' => 'PROBLEMAS CON EQUIPOS FISICOS', 'PRIORIDAD_DEFAULT' => 3],
            ['NOMBRE' => 'SOFTWARE', 'DESCRIPCION' => 'PROBLEMAS CON APLICACIONES', 'PRIORIDAD_DEFAULT' => 2],
            ['NOMBRE' => 'RED', 'DESCRIPCION' => 'PROBLEMAS DE CONECTIVIDAD', 'PRIORIDAD_DEFAULT' => 3],
            ['NOMBRE' => 'EMAIL', 'DESCRIPCION' => 'PROBLEMAS CON CORREO ELECTRONICO', 'PRIORIDAD_DEFAULT' => 2],
            ['NOMBRE' => 'ACCESO', 'DESCRIPCION' => 'PROBLEMAS DE ACCESO Y PERMISOS', 'PRIORIDAD_DEFAULT' => 3],
            ['NOMBRE' => 'OTRO', 'DESCRIPCION' => 'OTROS PROBLEMAS TECNICOS', 'PRIORIDAD_DEFAULT' => 1],
        ];

        $this->db->table('CATEGORIAS')->insertBatch($categorias);

        // Insertar usuarios base
        $usuarios = [
            [
                'NOMBRE'         => 'ADMINISTRADOR SISTEMA',
                'EMAIL'          => 'admin@empresa.com',
                'TELEFONO'       => '555-0000',
                'DEPENDENCIA_ID' => 1,
                'ROL'            => 'ADMIN',
            ],
            [
                'NOMBRE'         => 'SOPORTE TECNICO',
                'EMAIL'          => 'soporte@empresa.com',
                'TELEFONO'       => '555-0001',
                'DEPENDENCIA_ID' => 1,
                'ROL'            => 'SOPORTE',
            ],
        ];

        $this->db->table('USUARIOS')->insertBatch($usuarios);
    }
}
