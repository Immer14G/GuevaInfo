<?php
include_once "funciones.php";

$id = $_GET['id'] ?? 0;

$ventas = obtenerVentas(null, null, null, null);

$venta = null;

foreach($ventas as $v){
    if($v->id == $id){
        $venta = $v;
        break;
    }
}

if(!$venta){
    die("Venta no encontrada");
}

// 🔥 CALCULOS
$subtotal = round($venta->total / 1.13, 2);
$iva = round($venta->total - $subtotal, 2);

function linea(){
    return str_repeat("-", 32);
}
?>

<body onload="window.print()" style="font-family:monospace;">

<div style="width:260px; margin:auto; text-align:center;">

    <!-- 🔥 LOGO -->
    <img src="logo.png" width="120"><br>
    <b>MI NEGOCIO POS</b>

    <pre style="text-align:left; font-size:12px;">

<?= linea() ?>
TICKET: <?= str_pad($venta->id, 6, "0", STR_PAD_LEFT) ?>

FECHA : <?= date("d/m/Y H:i:s", strtotime($venta->fecha)) ?>

CLIENTE: <?= strtoupper($venta->cliente) ?>

USUARIO: <?= strtoupper($venta->usuario) ?>

<?= linea() ?>

PRODUCTO         CANT   TOTAL
<?= linea() ?>

<?php foreach($venta->productos as $p){ ?>

<?= str_pad(substr($p->nombre,0,14), 14) ?>
<?= str_pad($p->cantidad, 4, " ", STR_PAD_LEFT) ?>
<?= str_pad(number_format($p->cantidad * $p->precio,2), 10, " ", STR_PAD_LEFT) ?>

<?php } ?>

<?= linea() ?>

SUBTOTAL:           <?= number_format($subtotal,2) ?>

IVA (13%):          <?= number_format($iva,2) ?>

<?= linea() ?>

TOTAL:              <?= number_format($venta->total,2) ?>

<?= linea() ?>

        GRACIAS POR SU COMPRA
          VUELVA PRONTO

    </pre>

</div>

</body>

<style>
@media print {

   
    @page {
        margin: 0;
    }

    body {
        margin: 0;
    }

    /* opcional: ajustar ticket centrado */
    .ticket {
        width: 250px;
        margin: auto;
    }
}
</style>