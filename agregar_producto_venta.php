<?php
include_once "funciones.php";
session_start();

$_SESSION['lista'] = $_SESSION['lista'] ?? [];

// 🔥 CLICK
if(isset($_POST['id'])){
    $producto = obtenerProductoPorId($_POST['id']);

    if($producto){
        $_SESSION['lista'] = agregarProductoALista($producto, $_SESSION['lista']);
    }

    exit;
}

// 🔥 INPUT
if(isset($_POST['agregar']) && isset($_POST['codigo'])){

    $codigo = trim($_POST['codigo']);

    $producto = obtenerProductoPorCodigo($codigo);

    if(!$producto){
        $productos = obtenerProductos($codigo);
        if($productos){
            $producto = $productos[0];
        }
    }

    if($producto){
        $_SESSION['lista'] = agregarProductoALista($producto, $_SESSION['lista']);
    }

    header("Location: vender.php");
}