<?php
include_once "funciones.php";

$query = $_POST['query'] ?? '';

if(strlen($query) < 1) return;

$productos = obtenerProductos($query);

foreach($productos as $p){
    echo '
    <a href="#" class="list-group-item list-group-item-action producto-item"
       data-id="'.$p->id.'">
        <b>'.$p->nombre.'</b><br>
        <small>Código: '.$p->codigo.' | $'.number_format($p->venta,2).'</small>
    </a>';
}