<?php

namespace App\Controllers;
use App\Models\UsuarioModel;
use App\Controllers\BaseController;

/**
 * Controlador de autenticación de usuarios.
 * Aquí se maneja el proceso de login y la gestión de sesión.
 */
class Auth extends BaseController
{
    /**
     * Procesa el inicio de sesión del usuario.
     * Valida el correo, contraseña y rol, y guarda los datos en sesión si son correctos.
     * Redirige según el rol del usuario.
     */
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $rol = $this->request->getPost('rol');

        $usuarioModel = new UsuarioModel();

        // Valida el usuario por email y rol
        $usuario = $usuarioModel->validateLogin($email, $rol);

        // Verifica la contraseña (en este ejemplo, comparación directa)
        if ($usuario && $password === $usuario['CONTRASENA']) { 
            // Guardar datos en sesión
            $session = session();
            $session->set([
                'id' => $usuario['ID'],
                'email' => $usuario['EMAIL'],
                'rol' => $usuario['ROL'],
                'nombre' => $usuario['NOMBRE'],
                'isLoggedIn' => true
            ]);
            // Redirigir según rol
            if ($usuario['ROL'] === 'ADMIN') {
                return redirect()->to('/inicio_admin');
            } elseif ($usuario['ROL'] === 'SOPORTE') {
                return redirect()->to('/inicio_soporte');
            } else {
                return redirect()->to('/inicio_empleado');
            }
        } else {
            // Si falla, vuelve al login con mensaje de error
            return redirect()->back()->with('error', 'Correo, contraseña, rol incorrectos o usuario inactivo');
        }
    }
}