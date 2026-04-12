<?php   
    include_once "funciones.php";
    session_start();
    if(isset($_POST['agregar'])){
    
        if(isset($_POST['codigo'])) {
           $codigo = trim($_POST['codigo']);
// 🔥 BUSCAR POR CODIGO PRIMERO
        $producto = obtenerProductoPorCodigo($codigo);

// 🔥 SI NO EXISTE → BUSCAR POR NOMBRE (PRIMER RESULTADO)
        if(!$producto){
    $productos = obtenerProductos($codigo); // usa LIKE

    if($productos){
        $producto = $productos[0]; // toma el primero
    }
}
            print_r($producto);
            $_SESSION['lista'] = agregarProductoALista($producto,  $_SESSION['lista']);
            unset($_POST['codigo']);
            header("location: vender.php");
        }
    }

?>