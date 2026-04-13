<?php
include_once "encabezado.php";
include_once "navbar.php";
include_once "funciones.php";
session_start();

if(empty($_SESSION['usuario'])) header("location: login.php");

$data = obtenerCierreCajaPro();

$total = $data["total"];
$efectivoSistema = $data["efectivo"];
$tarjeta = $data["tarjeta"];
$transferencia = $data["transferencia"];

$efectivoReal = $_POST['efectivo_real'] ?? null;
$diferencia = null;

if($efectivoReal !== null){
    $efectivoReal = floatval($efectivoReal);
    $diferencia = $efectivoReal - $efectivoSistema;
}
?>

<div class="container mt-4">

<h2>💰 CIERRE DE CAJA PRO</h2>

<div class="card p-4">

    <h4>📊 Total ventas: $<?= number_format($total,2) ?></h4>

    <hr>

    <h5>💵 Efectivo: $<?= number_format($efectivoSistema,2) ?></h5>
    <h5>💳 Tarjeta: $<?= number_format($tarjeta,2) ?></h5>
    <h5>🏦 Transferencia: $<?= number_format($transferencia,2) ?></h5>

    <hr>

    <form method="POST">
        <label>💰 Efectivo contado físicamente</label>
        <input type="number" step="0.01" name="efectivo_real" class="form-control" required>

        <button class="btn btn-primary mt-3 w-100">
            Calcular cierre
        </button>
    </form>

    <?php if($diferencia !== null){ ?>

        <div class="alert mt-3 <?= $diferencia == 0 ? 'alert-success' : 'alert-warning' ?>">

            <h3>📌 Diferencia: $<?= number_format($diferencia,2) ?></h3>

            <?php if($diferencia == 0){ ?>
                ✔ Caja cuadrada
            <?php } elseif($diferencia > 0){ ?>
                ⚠ Sobrante
            <?php } else { ?>
                ❌ Faltante
            <?php } ?>

        </div>

        <button onclick="window.print()" class="btn btn-success w-100">
            🖨 Imprimir corte
        </button>

    <?php } ?>

</div>
</div>