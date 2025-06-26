<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login_iniciar');
        }
        // Si se pasa un argumento de rol, verifica que el usuario tenga ese rol
        if ($arguments && isset($arguments[0])) {
            $rolRequerido = $arguments[0];
            $rolUsuario = $session->get('rol');
            // Debug temporal
            //  die('ROL EN SESION: ' . $rolUsuario . ' | ROL REQUERIDO: ' . $rolRequerido);
             
            if ($rolRequerido === 'EMPLEADO') {
                if (in_array(strtoupper($rolUsuario), ['ADMIN', 'SOPORTE'])) {
                    // Si es admin o soporte, no puede entrar aquí
                    // Redirige a su dashboard
                    switch (strtoupper($rolUsuario)) {
                        case 'ADMIN':
                            return redirect()->to('/inicio_admin');
                        case 'SOPORTE':
                            return redirect()->to('/inicio_soporte');
                    }
                }
                // Si no es admin ni soporte, ¡puede entrar!
            } else {
                // Para admin y soporte, sigue la comparación exacta
                if (strtoupper($rolUsuario) !== strtoupper($rolRequerido)) {
                    switch (strtoupper($rolUsuario)) {
                        case 'ADMIN':
                            return redirect()->to('/inicio_admin');
                        case 'SOPORTE':
                            return redirect()->to('/inicio_soporte');
                        default:
                            return redirect()->to('/inicio_empleado');
                    }
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
      
    }
} 