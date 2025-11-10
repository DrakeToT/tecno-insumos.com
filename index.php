<?php
// Redirección automática al sitio público usando BASE_URL
// Ubicación: C:\xampp\htdocs\tecno-insumos.com\index.php

// Cargar la constante BASE_URL
require_once __DIR__ . '/src/config/base-url.php';

// Redirigir al directorio public definido por BASE_URL
// Usamos redirección temporal 302 (cambia a 301 si querés permanente)
header('Location: ' . BASE_URL, true, 302);
exit;
