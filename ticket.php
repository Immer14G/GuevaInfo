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

    public function __construct() {

        $this->ticket = str_pad(rand(1,999999), 6, "0", STR_PAD_LEFT);
        $this->fecha = date("d/m/Y H:i:s");

        $cliente = "CLIENTE GENERAL";

        if(isset($_SESSION['clienteVenta']) && $_SESSION['clienteVenta']){
            $clienteData = obtenerClientePorId($_SESSION['clienteVenta']);
            
            if($clienteData){
                $cliente = $clienteData->nombre;
            }
        }

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

        $this->total += $sub; // 🔥 total ya incluye IVA
    }

    private function calcular() {

        // 🔥 TOTAL ya incluye IVA
        $this->subtotal = round($this->total / 1.13, 2);
        $this->iva = round($this->total - $this->subtotal, 2);
    }

    private function linea() {
        return str_repeat("-", 32) . "\n";
    }

    public function print() {

        $this->calcular();

        header("Content-Type: text/html");

        echo "<div style='font-family:monospace; width:250px;'>";

        // 🔥 LOGO
        echo "<div style='text-align:center;'>
                <img src='logo.png' width='120'><br>
                <b>GuevaINfo</b>
              </div>";

        echo "<pre>";

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
        printf("IVA (13%%):          %10.2f\n", $this->iva);
        echo $this->linea();
        printf("TOTAL:              %10.2f\n", $this->total);

        echo $this->linea();
        echo "     GRACIAS POR SU COMPRA     \n";
        echo "        VUELVA PRONTO          \n";

        echo "</pre>";
        echo "</div>";
    }
}

/* ================= USO REAL ================= */

// 🔥 CORREGIDO (SIN PARÁMETRO)
$ticket = new TicketPOS();

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