<?php
require_once './controllers/UsuarioController.php';

$controller = new UsuarioController();
$action = $_GET['action'] ?? 'login';

switch($action) {

    case 'login':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login($_POST);
        } else {
            $controller->loginForm();
        }
        break;

    case 'register':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register($_POST);
        } else {
            $controller->registerForm();
        }
        break;

    case 'home':
        $controller->home();
        break;

    case 'logout':
        $controller->logout();
        break;
}