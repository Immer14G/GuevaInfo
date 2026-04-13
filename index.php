<?php
include_once "encabezado.php";
include_once "navbar.php";
include_once "funciones.php";
session_start();

if(empty($_SESSION['usuario'])) header("location: login.php");

// ================= CARTAS PRINCIPALES =================
$cartas = [
    ["titulo" => "Total ventas", "icono" => "fa fa-money-bill", "total" => "$".obtenerTotalVentas(), "color" => "#A71D45"],
    ["titulo" => "Ventas hoy", "icono" => "fa fa-calendar-day", "total" => "$".obtenerTotalVentasHoy(), "color" => "#2A8D22"],
    ["titulo" => "Ventas semana", "icono" => "fa fa-calendar-week", "total" => "$".obtenerTotalVentasSemana(), "color" => "#223D8D"],
    ["titulo" => "Ventas mes", "icono" => "fa fa-calendar-alt", "total" => "$".obtenerTotalVentasMes(), "color" => "#D55929"],
];

$totales = [
    ["nombre" => "Total productos", "total" => obtenerNumeroProductos(), "imagen" => "img/productos.png"],
    ["nombre" => "Ventas registradas", "total" => obtenerNumeroVentas(), "imagen" => "img/ventas.png"],
    ["nombre" => "Usuarios registrados", "total" => obtenerNumeroUsuarios(), "imagen" => "img/usuarios.png"],
    ["nombre" => "Clientes registrados", "total" => obtenerNumeroClientes(), "imagen" => "img/clientes.png"],
];

$ventasUsuarios = obtenerVentasPorUsuario();
$ventasClientes = obtenerVentasPorCliente();
$productosMasVendidos = obtenerProductosMasVendidos();
?>

<!-- ================= ESTILOS ================= -->
<style>
body{
    background:#f4f6f9;
}

.dashboard-title{
    font-weight:700;
    margin-bottom:15px;
}

.card-modern{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.2s;
}

.card-modern:hover{
    transform:scale(1.02);
}

.stat-img{
    width:60px;
    margin-bottom:10px;
}

.section-title{
    margin-top:25px;
    font-weight:600;
    border-left:5px solid #0d6efd;
    padding-left:10px;
}
</style>

<div class="container py-3">

<!-- ================= SALUDO ================= -->
<div class="alert alert-info shadow-sm">
    <h3>👋 Hola, <?= $_SESSION['usuario']->usuario ?></h3>
</div>

<!-- ================= TARJETAS ESTADÍSTICAS ================= -->
<div class="row g-3">
<?php foreach($totales as $t){ ?>
    <div class="col-md-3">
        <div class="card card-modern text-center p-3">
            <img src="<?= $t['imagen'] ?>" class="stat-img mx-auto">
            <h5><?= $t['nombre'] ?></h5>
            <h3 class="text-primary"><?= $t['total'] ?></h3>
        </div>
    </div>
<?php } ?>
</div>

<!-- ================= CARTAS DE VENTAS ================= -->
<div class="mt-4">
    <?php include_once "cartas_totales.php"; ?>
</div>

<!-- ================= TABLAS PRINCIPALES ================= -->
<div class="row mt-4">

    <!-- USUARIOS -->
    <div class="col-md-6">
        <div class="card card-modern p-3">
            <h5 class="section-title">📊 Ventas por usuarios</h5>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Ventas</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($ventasUsuarios as $u){ ?>
                    <tr>
                        <td><?= $u->usuario ?></td>
                        <td><?= $u->numeroVentas ?></td>
                        <td>$<?= $u->total ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CLIENTES -->
    <div class="col-md-6">
        <div class="card card-modern p-3">
            <h5 class="section-title">🧑‍🤝‍🧑 Ventas por clientes</h5>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Compras</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($ventasClientes as $c){ ?>
                    <tr>
                        <td><?= $c->cliente ?></td>
                        <td><?= $c->numeroCompras ?></td>
                        <td>$<?= $c->total ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= CAJA ================= -->
<div class="mt-4 text-center">
    <a href="cierre_caja.php" class="btn btn-dark btn-lg shadow">
        💰 Cierre de caja
    </a>

    <div class="card card-modern mt-3 p-3">
        <h5>📦 Panel Contador</h5>

        <a href="cierre_caja.php" class="btn btn-dark m-1">
            📊 Corte de caja
        </a>

        <a href="reporte_cierres.php" class="btn btn-info m-1">
            📑 Historial de cortes
        </a>
    </div>
</div>

<!-- ================= PRODUCTOS MAS VENDIDOS ================= -->
<div class="mt-4">
    <div class="card card-modern p-3">
        <h5 class="section-title">🔥 Top 10 productos más vendidos</h5>

        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Unidades</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($productosMasVendidos as $p){ ?>
                <tr>
                    <td><?= $p->nombre ?></td>
                    <td><?= $p->unidades ?></td>
                    <td>$<?= $p->total ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</div>