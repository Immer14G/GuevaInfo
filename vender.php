<?php
include_once "encabezado.php";
include_once "navbar.php";
include_once "funciones.php";
session_start();

if(empty($_SESSION['usuario'])) header("location: login.php");

$_SESSION['lista'] = $_SESSION['lista'] ?? [];

// 🔥 CALCULO PRO
$totales = calcularTotales($_SESSION['lista']);
$subtotal = $totales['subtotal'];
$iva = $totales['iva'];
$total = $totales['total'];

$clientes = obtenerClientes();
$clienteSeleccionado = $_SESSION['clienteVenta'] ?? null;
$clienteSeleccionado = $clienteSeleccionado ? obtenerClientePorId($clienteSeleccionado) : null;
?>

<div class="container mt-3">

<!-- ================= BUSCADOR ================= -->
<form action="agregar_producto_venta.php" method="post" class="row position-relative">
    <div class="col-10 position-relative">
        <input class="form-control form-control-lg"
               name="codigo"
               id="codigo"
               autocomplete="off"
               type="text"
               placeholder="Código o nombre del producto">

        <div id="lista_productos"
             class="list-group position-absolute w-100"
             style="z-index:1000;"></div>
    </div>

    <div class="col">
        <input type="submit" value="Agregar" name="agregar" class="btn btn-success mt-2">
    </div>
</form>

<style>
.vuelto-box{
    text-align:center;
    font-size:24px;
    font-weight:bold;
    background:#f8f9fa;
    border-radius:10px;
    padding:10px;
    margin-top:10px;
}

.empty-state{
    text-align:center;
    margin-top:80px;
    color:#6c757d;
}

.empty-state i{
    font-size:70px;
    margin-bottom:15px;
    opacity:0.7;
}
</style>

<!-- ================= LISTA ================= -->
<?php if(!empty($_SESSION['lista'])) { ?>

<div class="mt-3">

<table class="table table-hover">
<thead class="table-dark">
<tr>
<th>Código</th>
<th>Producto</th>
<th>Precio</th>
<th>Cantidad</th>
<th>Subtotal</th>
<th></th>
</tr>
</thead>

<tbody>
<?php foreach($_SESSION['lista'] as $lista) { ?>
<tr>
<td><?= $lista->codigo ?></td>
<td><?= $lista->nombre ?></td>
<td>$<?= number_format($lista->venta,2) ?></td>
<td><?= $lista->cantidad ?></td>
<td>$<?= number_format($lista->cantidad * $lista->venta,2) ?></td>
<td>
<a href="quitar_producto_venta.php?id=<?= $lista->id ?>" class="btn btn-danger btn-sm">X</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- ================= CLIENTE ================= -->
<form class="row" method="post" action="establecer_cliente_venta.php">
<div class="col-10">
<select class="form-select" name="idCliente">
<?php foreach($clientes as $cliente) { ?>
<option value="<?= $cliente->id ?>"><?= $cliente->nombre ?></option>
<?php } ?>
</select>
</div>
<div class="col-auto">
<input class="btn btn-info" type="submit" value="Seleccionar cliente">
</div>
</form>

<?php if($clienteSeleccionado){ ?>
<div class="alert alert-primary mt-3">
<b>Cliente:</b> <?= $clienteSeleccionado->nombre ?>
</div>
<?php } ?>

<!-- ================= TOTALES ================= -->
<div class="text-center mt-3">
<h5>Subtotal: $<?= number_format($subtotal,2) ?></h5>
<h5>IVA (13%): $<?= number_format($iva,2) ?></h5>
<h1>Total: $<?= number_format($total,2) ?></h1>

<button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalPago">
💰 Pagar
</button>

<a class="btn btn-danger btn-lg" href="cancelar_venta.php">
Cancelar
</a>
</div>

</div>

<?php } else { ?>

<!-- 🔥 ESTADO VACÍO -->
<div class="empty-state">
    <i class="fa fa-cash-register"></i>
    <h3>No hay productos en la venta</h3>
    <p>Escanea o busca un producto para comenzar</p>

    <div class="alert alert-info mt-3">
        💡 Puedes buscar por nombre o código
    </div>
</div>

<?php } ?>

</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="modalPago">
<div class="modal-dialog">
<div class="modal-content p-3">

<h4>Total: $<?= number_format($total,2) ?></h4>

<label>Tipo de pago</label>
<select id="tipoPago" class="form-control mb-2">
<option value="CONTADO">Contado</option>
<option value="CREDITO">Crédito</option>
</select>

<label>Pago cliente</label>
<input type="number" id="pagoCliente" class="form-control">

<div class="vuelto-box">
Vuelto: $<span id="vuelto">0.00</span>
</div>

<button class="btn btn-primary mt-3 w-100" onclick="finalizarVenta()">
Confirmar pago
</button>

</div>
</div>
</div>

<!-- ================= JS ================= -->
<script>
let total = <?= $total ?>;

document.getElementById("pagoCliente")?.addEventListener("input", function(){
    let pago = parseFloat(this.value || 0);
    document.getElementById("vuelto").innerText = (pago - total).toFixed(2);
});

function finalizarVenta(){
    let pago = parseFloat(document.getElementById("pagoCliente").value || 0);
    let tipo = document.getElementById("tipoPago").value;

    if(tipo === "CONTADO" && pago < total){
        alert("Pago insuficiente");
        return;
    }

    fetch("guardar_tipo_pago.php", {
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"tipo="+tipo
    });

    if(confirm("¿Imprimir ticket?")){
        window.open("ticket.php","_blank");
    }

    window.location.href = "registrar_venta.php";
}
</script>

<!-- ================= BUSCADOR ================= -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    $("#codigo").on("keyup", function () {

        let query = $(this).val().trim();

        if (query.length < 1) {
            $("#lista_productos").html("");
            return;
        }

        $.ajax({
            url: "buscar_productos.php",
            method: "POST",
            data: { query: query },
            success: function (data) {
                $("#lista_productos").html(data);
            }
        });

    });

  $(document).on("click", ".producto-item", function (e) {
    e.preventDefault();

   let id = $(this).data("id");

$.post("agregar_producto_venta.php", {
    id: id,
    agregar: true
}, function(){
    location.reload();
});
});
});
</script>