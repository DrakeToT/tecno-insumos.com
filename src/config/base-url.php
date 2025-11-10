<?php
// Detectar protocolo (http o https)
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Host del servidor (localhost, dominio, etc.)
$host = $_SERVER['HTTP_HOST'];

// Nombre de la carpeta del proyecto (ajustar según sea necesario)
$projectFolder = '/tecno-insumos.com/public';

// Definir BASE_URL apuntando siempre al public/
define('BASE_URL', $protocolo . $host . $projectFolder);


/* Agregar: 
    <?php echo BASE_URL?> 
cuando se utilice una ruta para el navegador */