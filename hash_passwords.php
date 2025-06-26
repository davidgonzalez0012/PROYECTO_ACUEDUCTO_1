<?php

// Carga el framework CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . 'bootstrap.php';

use App\Models\UsuarioModel;

$usuarioModel = new UsuarioModel();
$usuarios = $usuarioModel->findAll();

foreach ($usuarios as $usuario) {
    // Solo si la contraseña no está hasheada (opcional: verifica longitud o patrón)
    if (strlen($usuario['CONTRASENA']) < 60) {
        $hash = password_hash($usuario['CONTRASENA'], PASSWORD_DEFAULT);
        $usuarioModel->update($usuario['ID'], ['CONTRASENA' => $hash]);
        echo "Usuario {$usuario['EMAIL']} actualizado.<br>";
    }
}

echo "Actualización completada.";