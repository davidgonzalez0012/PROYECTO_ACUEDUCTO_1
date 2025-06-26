<?php

namespace App\Controllers;

use App\Models\DependenciaModel;
use App\Models\UsuarioModel;
use CodeIgniter\Controller;

/**
 * Controlador para la gestión de dependencias y usuarios por dependencia.
 * Permite listar dependencias y usuarios asociados, tanto para administrador como para soporte.
 */
class Dependencia extends Controller
{
    /**
     * Muestra todas las dependencias para el administrador.
     */
    public function index()
    {
        $model = new DependenciaModel();
        $data['dependencias'] = $model->findAll();
        return view('administrador/lista_dependencias', $data);
    }

    /**
     * Muestra una dependencia específica para el administrador.
     */
    public function mostrarDependencia($id)
    {
        $model = new DependenciaModel();
        $data['dependencia'] = $model->find($id);
        return view('administrador/lista_dependencias', $data);
    }

    /**
     * Muestra los usuarios asociados a una dependencia para el administrador.
     */
    public function usuariosPorDependencia($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuarios = $usuarioModel->where('DEPENDENCIA_ID', $id)->findAll();

        $dependenciaModel = new DependenciaModel();
        $dependencia = $dependenciaModel->find($id);

        $data = [
            'usuarios' => $usuarios,
            'dependencia' => $dependencia ? $dependencia['NOMBRE'] : 'Desconocida'
        ];

        return view('administrador/usuarios_dependencia', $data);
    }

    /**
     * Muestra todas las dependencias para el soporte.
     */
    public function index_soporte()
    {
        $model = new DependenciaModel();
        $data['dependencias'] = $model->findAll();
        return view('soporte/lista_dependencias_soporte', $data);
    }

    /**
     * Muestra una dependencia específica para el soporte.
     */
    public function mostrarDependencia_soporte($id)
    {
        $model = new DependenciaModel();
        $data['dependencia'] = $model->find($id);
        return view('soporte/lista_dependencias_soporte', $data);
    }

    /**
     * Muestra los usuarios asociados a una dependencia para el soporte.
     */
    public function usuariosPorDependencia_soporte($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuarios = $usuarioModel->where('DEPENDENCIA_ID', $id)->findAll();

        $dependenciaModel = new DependenciaModel();
        $dependencia = $dependenciaModel->find($id);

        $data = [
            'usuarios' => $usuarios,
            'dependencia' => $dependencia ? $dependencia['NOMBRE'] : 'Desconocida'
        ];

        return view('soporte/Usuarios_dependencia_soporte', $data);
    }
}