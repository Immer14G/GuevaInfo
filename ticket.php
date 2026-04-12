<?php
session_start();
include_once "funciones.php";

date_default_timezone_set('America/El_Salvador');

class TicketPOS {

    private $ticket;
    private $fecha;
    private $cliente;
    private $productos = [];
    private $subtotal = 0;
    private $iva = 0;
    private $total = 0;

    public function __construct($cliente = "CLIENTE GENERAL") {
        $this->ticket = str_pad(rand(1,999999), 6, "0", STR_PAD_LEFT);
        $this->fecha = date("d/m/Y H:i:s");
        $this->cliente = strtoupper($cliente);
    }

    public function add($nombre, $cantidad, $precio) {
        $sub = $cantidad * $precio;

        $this->productos[] = [
            "nombre" => strtoupper($nombre),
            "cantidad" => $cantidad,
            "precio" => $precio,
            "subtotal" => $sub
        ];

        $this->subtotal += $sub;
    }

    private function calcular() {

        // 🔥 IVA INCLUIDO (no se suma encima)
        $this->iva = $this->subtotal - ($this->subtotal / 1.13);
        $this->total = $this->subtotal;
    }

    private function linea() {
        return str_repeat("-", 32) . "\n";
    }

    public function print() {

        $this->calcular();

        header("Content-Type: text/plain");

        echo "        MI NEGOCIO POS        \n";
        echo $this->linea();
        echo "TICKET: " . $this->ticket . "\n";
        echo "FECHA : " . $this->fecha . "\n";
        echo "CLIENTE: " . $this->cliente . "\n";
        echo $this->linea();

        echo "PRODUCTO         CANT   TOTAL\n";
        echo $this->linea();

        foreach ($this->productos as $p) {

            $nombre = substr($p["nombre"], 0, 14);

            printf(
                "%-14s %4d %8.2f\n",
                $nombre,
                $p["cantidad"],
                $p["subtotal"]
            );
        }

        echo $this->linea();

        printf("SUBTOTAL:           %10.2f\n", $this->subtotal);
        printf("IVA INCLUIDO:       %10.2f\n", $this->iva);
        echo $this->linea();
        printf("TOTAL:              %10.2f\n", $this->total);

        echo $this->linea();
        echo "     GRACIAS POR SU COMPRA     \n";
        echo "        VUELVA PRONTO          \n";
    }
}

/* ================= USO REAL ================= */

$cliente = "CLIENTE GENERAL";

if(isset($_SESSION['clienteVenta']) && $_SESSION['clienteVenta']){
    $clienteData = obtenerClientePorId($_SESSION['clienteVenta']);
    if($clienteData){
        $cliente = $clienteData->nombre;
    }
}

$ticket = new TicketPOS($cliente);

if(isset($_SESSION['lista']) && count($_SESSION['lista']) > 0){

    foreach($_SESSION['lista'] as $p){
        $ticket->add(
            $p->nombre,
            $p->cantidad,
            $p->venta
        );
    }

    $ticket->print();

} else {
    echo "No hay productos en la venta";
}