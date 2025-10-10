<?php

use CodeIgniter\I18n\Time;

function different($data)
{
    $myTime = new Time('now', 'America/Bogota', 'es_CO');
    $time = Time::parse($data, 'America/Bogota', 'es_CO');
    $diff =  $time->difference($myTime, 'America/Bogota');
    return $diff->humanize();
}

function formatDate($fecha){
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    // $fecha = date('Y-m-d'); // "2025-03-11"
    $partes = explode('-', $fecha);
    
    $dia = (int)$partes[2];
    $mes = $meses[(int)$partes[1]];
    $anio = $partes[0];
    
    return "$dia de $mes de $anio";
    
}