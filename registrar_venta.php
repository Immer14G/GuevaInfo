<?php
session_start();
include_once "funciones.php";

$lista = $_SESSION['lista'];
$idUsuario = $_SESSION['usuario']->id;
$idCliente = $_SESSION['clienteVenta'] ?? null;
var_dump($_SESSION['usuario']);
$tipo = $_SESSION['tipoPago'] ?? 'CONTADO';
$monto = $_SESSION['montoPago'] ?? 0;
$ref = $_SESSION['referenciaPago'] ?? null;


// 🔥 OBTENER DATOS DE PAGO
$pago = $_SESSION['pago'] ?? null;

$tipo = $pago['tipo'] ?? "EFECTIVO";
$monto = $pago['monto'] ?? 0;
$referencia = $pago['referencia'] ?? null;

// 🔥 TOTAL
$totales = calcularTotales($lista);
$total = $totales['total'];

// 🔥 REGISTRAR VENTA
$idVenta = registrarVenta($productos, $idUsuario, $idCliente, $total);
sumarVentaACaja($total, $idUsuario);



// 🔥 GUARDAR INFO DE PAGO
$sentencia = "UPDATE ventas SET tipo_pago=?, monto_recibido=?, referencia=? WHERE id=?";
editar($sentencia, [$tipo, $monto, $referencia, $idVenta]);

// 🔥 LIMPIAR
unset($_SESSION['lista']);
unset($_SESSION['pago']);

header("location: vender.php");