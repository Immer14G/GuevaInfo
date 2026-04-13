<?php
session_start();

$_SESSION['tipoPago'] = $_POST['tipo'] ?? 'EFECTIVO';
$_SESSION['montoPago'] = floatval($_POST['monto'] ?? 0);
$_SESSION['referenciaPago'] = $_POST['referencia'] ?? null;

echo "ok";