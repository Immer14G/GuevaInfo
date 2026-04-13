<?php
include_once "funciones.php";
session_start();

$idUsuario = $_SESSION['usuario']->id;

$data = obtenerCierreCajaPro();

$efectivoReal = $_POST['efectivo_real'];
$diferencia = $efectivoReal - $data["efectivo"];

$sql = "INSERT INTO cierres_caja (
fecha_apertura,
fecha_cierre,
idUsuario,
total_ventas,
efectivo_sistema,
efectivo_real,
diferencia,
ventas_efectivo,
ventas_tarjeta,
ventas_transferencia,
estado
) VALUES (NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, 'CERRADO')";

$stmt = conectarBaseDatos()->prepare($sql);

$stmt->execute([
    $idUsuario,
    $data["total"],
    $data["efectivo"],
    $efectivoReal,
    $diferencia,
    $data["efectivo"],
    $data["tarjeta"],
    $data["transferencia"]
]);

echo "ok";