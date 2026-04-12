<?php
include_once "funciones.php";

$query = $_POST['query'] ?? '';

if($query == ''){
    return;
}

// 🔥 BUSCA POR NOMBRE Y CODIGO
$productos = obtenerProductos($query);

if(!$productos){
    echo '<div class="list-group-item">No hay resultados</div>';
    return;
}

// 🔥 MUESTRA TODOS (NO SOLO UNO)
foreach($productos as $p){
    echo '<a href="#" 
            class="list-group-item list-group-item-action producto-item"
           data-id="'.$p->id.'">
            
            <b>'.$p->nombre.'</b> 
            <span class="text-muted">('.$p->codigo.')</span>
            - $'.number_format($p->venta,2).'
          </a>';
}