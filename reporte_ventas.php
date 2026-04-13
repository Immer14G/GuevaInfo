<?php
include_once "encabezado.php";
include_once "navbar.php";
include_once "funciones.php";

session_start();
if(empty($_SESSION['usuario'])) header("location: login.php");

// ================= VALIDACIONES =================
$fechaInicio = $_POST['inicio'] ?? null;
$fechaFin = $_POST['fin'] ?? null;
$idUsuario = $_POST['idUsuario'] ?? null;
$idCliente = $_POST['idCliente'] ?? null;

// ================= QUERY BASE =================
$parametros = [];
$sql = "SELECT 
        ventas.*,
        usuarios.usuario,
        IFNULL(clientes.nombre,'MOSTRADOR') AS cliente,
        tipos_pago.nombre AS tipo_pago
        FROM ventas
        INNER JOIN usuarios ON usuarios.id = ventas.idUsuario
        LEFT JOIN clientes ON clientes.id = ventas.idCliente
        LEFT JOIN tipos_pago ON tipos_pago.id = ventas.idTipoPago
        WHERE 1=1";
// ================= FILTROS =================
if(!empty($fechaInicio) && !empty($fechaFin)){
    $sql .= " AND DATE(ventas.fecha) BETWEEN ? AND ?";
    $parametros[] = $fechaInicio;
    $parametros[] = $fechaFin;
}

if(!empty($idUsuario)){
    $sql .= " AND ventas.idUsuario = ?";
    $parametros[] = $idUsuario;
}

if(!empty($idCliente)){
    $sql .= " AND ventas.idCliente = ?";
    $parametros[] = $idCliente;
}

$sql .= " ORDER BY ventas.id DESC";

$ventas = select($sql, $parametros);

$usuarios = obtenerUsuarios();
$clientes = obtenerClientes();

// ================= FUNCIÓN BADGE PAGO =================


?>

<style>
.reporte-card{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.badge-pos{
    padding:6px 10px;
    border-radius:10px;
    font-size:12px;
}
</style>

<div class="container mt-3">

<h3>📊 Reporte de Ventas</h3>

<!-- ================= FILTROS ================= -->
<form method="post" class="row g-2 mt-2">

    <div class="col-md-4">
        <label>Fecha inicio</label>
        <input type="date" name="inicio" class="form-control">
    </div>

    <div class="col-md-4">
        <label>Fecha fin</label>
        <input type="date" name="fin" class="form-control">
    </div>

    <div class="col-md-4">
        <label>Usuario</label>
        <select name="idUsuario" class="form-select">
            <option value="">Todos</option>
            <?php foreach($usuarios as $u){ ?>
                <option value="<?= $u->id ?>"><?= $u->usuario ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-4 mt-2">
        <label>Cliente</label>
        <select name="idCliente" class="form-select">
            <option value="">Todos</option>
            <?php foreach($clientes as $c){ ?>
                <option value="<?= $c->id ?>"><?= $c->nombre ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-12 mt-3">
        <button class="btn btn-primary">🔎 Buscar</button>
    </div>

</form>

<!-- ================= TABLA ================= -->
<div class="reporte-card mt-4">

<?php if(count($ventas) > 0){ ?>

<table class="table table-hover align-middle">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Fecha</th>
    <th>Cliente</th>
    <th>Usuario</th>
    <th>Total</th>
    <th>Pago</th>
    <th>Documento</th>
    <th>Estado</th>
    <th>Ver</th>
</tr>
</thead>

<tbody>

<?php foreach($ventas as $v){ ?>

<?php [$colorPago, $textoPago] = badgePago($v->tipo_pago); ?>

<tr>
    <td><?= $v->id ?></td>

    <td><?= date("d/m/Y H:i", strtotime($v->fecha)) ?></td>

    <td><?= $v->cliente ?></td>

    <td><?= $v->usuario ?></td>

    <td><b>$<?= number_format($v->total,2) ?></b></td>

    <!-- ================= TIPO PAGO ================= -->
    <td>
        <span class="badge bg-<?= $colorPago ?> badge-pos">
            <?= $textoPago ?>
        </span>
    </td>

    <td>
        <span class="badge bg-secondary badge-pos">
            <?= $v->tipo_documento ?>
        </span>
    </td>

    <td>
        <span class="badge bg-success badge-pos">
            <?= $v->estado ?>
        </span>
    </td>

    <td>
        <a class="btn btn-sm btn-success"
           target="_blank"
           href="ticket_reporte.php?id=<?= $v->id ?>">
           🧾
        </a>
    </td>

</tr>

<?php } ?>

</tbody>
</table>

<?php } else { ?>

<div class="alert alert-warning text-center">
    <h4>No hay ventas registradas</h4>
</div>

<?php } ?>

</div>
</div>