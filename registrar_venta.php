<?php
include_once "funciones.php";
session_start();

$productos = $_SESSION['lista'];
$idUsuario = $_SESSION['idUsuario'];
$total = calcularTotalLista($productos);
$idCliente = $_SESSION['clienteVenta'];

if(count($productos) === 0){
    header("location: vender.php");
    exit;
}

$resultado = registrarVenta($productos, $idUsuario, $idCliente, $total);

if(!$resultado){
    echo "Error al registrar la venta";
    exit;
}

$_SESSION['lista'] = [];
$_SESSION['clienteVenta'] = "";

header("location: vender.php");
exit;