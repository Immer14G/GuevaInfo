<?php
include_once "funciones.php";
session_start();

// 🔒 VALIDAR SESIÓN
if(empty($_SESSION['usuario'])){
    echo "error: sesion";
    exit;
}

// 🔒 VALIDAR CARRITO
if(empty($_SESSION['lista'])){
    echo "error: carrito vacio";
    exit;
}

try {

    // 🔥 DATOS RECIBIDOS
    $tipo = $_POST['tipo'] ?? 'EFECTIVO';
    $monto = $_POST['monto'] ?? 0;
    $referencia = $_POST['referencia'] ?? null;

    // 🔥 LIMPIAR DATOS
    $monto = floatval($monto);
    $referencia = ($referencia == "") ? null : $referencia;

    // 🔥 DATOS DE SESIÓN
    $lista = $_SESSION['lista'];
    $idUsuario = intval($_SESSION['usuario']->id);

    // 👇 FIX IMPORTANTE
    $idCliente = isset($_SESSION['clienteVenta']) ? intval($_SESSION['clienteVenta']) : null;

    // 🔥 CALCULAR TOTALES
    $totales = calcularTotales($lista);
    $subtotal = round($totales['subtotal'], 2);
    $iva = round($totales['iva'], 2);
    $total = round($totales['total'], 2);

    // 🔥 SI MONTO VIENE 0 → USAR TOTAL
    if($monto <= 0){
        $monto = $total;
    }

    // 🔥 CONEXIÓN
    $bd = conectarBaseDatos();

    // 🚨 ACTIVAR ERRORES REALES
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🚀 TRANSACCIÓN (CLAVE PARA SISTEMA REAL)
    $bd->beginTransaction();

    // 🔥 INSERT VENTA

$tipoPago = $_SESSION['tipoPago'] ?? 'EFECTIVO'; 
$sql = "INSERT INTO ventas (
fecha, total, idUsuario, idCliente,
subtotal, iva,
tipo_pago,
tipo_documento,
estado,
monto_recibido,
referencia
)
VALUES (NOW(), ?, ?, ?, ?, ?, ?, 'TICKET', 'FINALIZADA', ?, ?)";
$stmt = $bd->prepare($sql);

$stmt->execute([
    $total,
    $idUsuario,
    $idCliente,
    $subtotal,
    $iva,
    $tipoPago, 
    $monto,
    $referencia
]);
    // 🔥 ID VENTA
    $idVenta = $bd->lastInsertId();

    // 🔥 DETALLE + STOCK
    foreach ($lista as $producto) {

        // VALIDACIÓN STOCK
        if($producto->cantidad <= 0){
            throw new Exception("Cantidad inválida");
        }

        // INSERT DETALLE
        $sqlDetalle = "INSERT INTO productos_ventas 
        (cantidad, precio, idProducto, idVenta)
        VALUES (?, ?, ?, ?)";

        $stmtDetalle = $bd->prepare($sqlDetalle);
        $stmtDetalle->execute([
            $producto->cantidad,
            $producto->venta,
            $producto->id,
            $idVenta
        ]);

        // DESCONTAR STOCK
        $sqlStock = "UPDATE productos 
        SET existencia = existencia - ? 
        WHERE id = ?";

        $stmtStock = $bd->prepare($sqlStock);
        $stmtStock->execute([
            $producto->cantidad,
            $producto->id
        ]);
    }

    // ✅ TODO OK → CONFIRMAR
    $bd->commit();

    // 🔥 LIMPIAR SESIÓN
    $_SESSION['lista'] = [];
    unset($_SESSION['clienteVenta']);

    echo "ok";

} catch (Exception $e) {

    // ❌ SI FALLA → DESHACER TODO
    if(isset($bd)) $bd->rollBack();

    // 🔥 VER ERROR REAL
    echo "error: " . $e->getMessage();
}