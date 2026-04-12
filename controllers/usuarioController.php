<?php
require_once 'models/Usuario.php';
session_start();

class UsuarioController {
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function loginForm() {
        require './views/usuarios/login.php';
    }

    public function login($data) {
        $user = $this->usuario->findByEmail($data['email']);

        if($user && password_verify($data['password'], $user['password'])) {
            $_SESSION['usuario'] = $user['nombre'];
            header('Location: index.php?action=home');
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
            require './views/usuarios/login.php';
        }
    }

    public function registerForm() {
        require './views/usuarios/register.php';
    }

    public function register($data) {

        if($data['password'] !== $data['confirm_password']) {
            $error = "Las contraseñas no coinciden";
            require './views/usuarios/register.php';
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->usuario->create($data);

        header('Location: index.php?action=login&success=1');
        exit();
    }

    public function home() {
        if(!isset($_SESSION['usuario'])) {
            header('Location: index.php?action=login');
            exit();
        }

        echo "<h1>Bienvenido ".$_SESSION['usuario']."</h1>";
        echo "<a href='index.php?action=logout'>Cerrar sesión</a>";
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}