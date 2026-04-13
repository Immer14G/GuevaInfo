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
#lista_productos a:hover{
    background:#0d6efd;
    color:white;
    cursor:pointer;
}

/* ===== MODAL PRO ===== */
.modal-content{
    border-radius: 18px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    overflow: hidden;
}

/* Encabezado del modal (si luego quieres agregar header) */
.modal-content h4{
    font-weight: 700;
    text-align: center;
    padding: 10px 0;
    background: linear-gradient(135deg, #0d6efd, #0a58ca);
    color: white;
    border-radius: 12px;
    margin-bottom: 15px;
}

/* Inputs más modernos */
.modal-content input,
.modal-content select{
    border-radius: 12px;
    padding: 10px;
    border: 1px solid #e0e0e0;
    transition: 0.2s;
}

.modal-content input:focus,
.modal-content select:focus{
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25);
}

/* Botón confirmar */
.modal-content .btn-primary{
    border-radius: 12px;
    padding: 12px;
    font-weight: 600;
    background: linear-gradient(135deg, #198754, #157347);
    border: none;
    transition: 0.2s;
}

.modal-content .btn-primary:hover{
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(25,135,84,0.3);
}

/* Selector tipo pago más bonito */
#tipoPago{
    background: #f8f9fa;
}

/* Caja de vuelto PRO */
.vuelto-box{
    background: linear-gradient(135deg, #f1f3f5, #e9ecef);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 12px;
    font-size: 22px;
    font-weight: 700;
    color: #198754;
}

/* Animación suave del modal */
.modal.fade .modal-dialog{
    transform: scale(0.95);
    transition: all 0.2s ease-in-out;
}

.modal.show .modal-dialog{
    transform: scale(1);
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

<div class="empty-state">
    <i class="fa fa-cash-register"></i>
    <h3>No hay productos en la venta</h3>
    <p>Escanea o busca un producto para comenzar</p>
</div>

<?php } ?>

</div>

<!-- ================= MODAL ================= -->
<!-- ================= MODAL ================= -->
<div class="modal fade" id="modalPago">
<div class="modal-dialog">
<div class="modal-content p-3">

<h4>Total: $<?= number_format($total,2) ?></h4>

<label>Tipo de pago</label>
<?php $tiposPago = obtenerTiposPago(); ?>

<label>Tipo de pago</label>
<?php $tiposPago = obtenerTiposPago(); ?>

<label>Tipo de pago</label>
<?php $tiposPago = obtenerTiposPago(); ?>

<label>Tipo de pago</label>
<select id="tipoPago" class="form-control mb-2">
    <?php foreach($tiposPago as $tp){ ?>
        <option value="<?= $tp->nombre ?>">
            <?= $tp->nombre ?>
        </option>
    <?php } ?>
</select>
<!-- EFECTIVO -->
<div id="boxEfectivo">
    <label>Pago cliente</label>
    <input type="number" id="pagoCliente" class="form-control">

    <div class="vuelto-box">
        Vuelto: $<span id="vuelto">0.00</span>
    </div>
</div>

<!-- TARJETA -->
<div id="boxTarjeta" style="display:none;">
    <label>Referencia tarjeta</label>
    <input type="text" id="refTarjeta" class="form-control">
</div>

<!-- TRANSFERENCIA -->
<div id="boxTransferencia" style="display:none;">
    <label>Referencia transferencia</label>
    <input type="text" id="refTransferencia" class="form-control">
</div>

<button class="btn btn-primary mt-3 w-100" onclick="finalizarVenta()">
Confirmar pago
</button>

</div>
</div>
</div>

<!-- ================= JS ================= -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let total = <?= $total ?>;

// CAMBIO TIPO PAGO
$("#tipoPago").on("change", function(){
    let tipo = $(this).val();

    $("#boxEfectivo, #boxTarjeta, #boxTransferencia").hide();

    if(tipo === "EFECTIVO") $("#boxEfectivo").show();
    if(tipo === "TARJETA") $("#boxTarjeta").show();
    if(tipo === "TRANSFERENCIA") $("#boxTransferencia").show();
});

// VUELTO
$("#pagoCliente").on("input", function(){
    let pago = parseFloat(this.value || 0);
    $("#vuelto").text((pago - total).toFixed(2));
});

function finalizarVenta(){

    let tipo = $("#tipoPago").val();
    let pago = parseFloat($("#pagoCliente").val() || 0);
    let referencia = "";

    // VALIDACIONES
    if(tipo === "EFECTIVO"){
        if(pago < total){
            alert("Pago insuficiente");
            return;
        }
    }

    if(tipo === "TARJETA"){
        referencia = $("#refTarjeta").val();
        if(referencia.trim() === ""){
            alert("Ingrese referencia");
            return;
        }
        pago = total;
    }

    if(tipo === "TRANSFERENCIA"){
        referencia = $("#refTransferencia").val();
        if(referencia.trim() === ""){
            alert("Ingrese referencia");
            return;
        }
        pago = total;
    }

    // 🔥 ENVIAR A TU MISMO FLUJO QUE YA FUNCIONA
    fetch("guardar_tipo_pago.php", {
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"tipo="+tipo+"&monto="+pago+"&referencia="+referencia
    });

    if(confirm("¿Imprimir ticket?")){
        window.open("ticket.php","_blank");
    }

    window.location.href = "registrar_venta.php";
}
// 🔥 BUSCADOR
$("#codigo").on("keyup", function () {
    let query = $(this).val().trim();

    if (query.length < 1) {
        $("#lista_productos").html("");
        return;
    }

    $.post("buscar_producto.php", {query}, function(data){
        $("#lista_productos").html(data);
    });
});

// 🔥 CLICK PRODUCTO
$(document).on("click", ".producto-item", function (e) {
    e.preventDefault();
    let id = $(this).data("id");

    $.post("agregar_producto_venta.php", {id}, function(){
        location.reload();
    });
});

// 🔥 CERRAR LISTA
$(document).click(function(e){
    if(!$(e.target).closest('#codigo').length){
        $("#lista_productos").html("");
    }
});
</script>