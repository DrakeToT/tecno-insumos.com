<?php
/**
 * Definición centralizada de los menús por rol.
 * Más adelante, se puede reemplazar por permisos (hasPermission).
 */

return [

    // Menú del Administrador
    'Administrador' => [
        [
            'label' => 'Inicio',
            'icon'  => 'bi-house',
            'route' => '/inicio',
            'page'  => 'inicio',
        ],
        [
            'label' => 'Usuarios',
            'icon'  => 'bi-people',
            'route' => '/usuarios',
            'page'  => 'usuarios',
        ],
        [
            'label' => 'Roles y Permisos',
            'icon'  => 'bi-ui-radios',
            'route' => '/roles',
            'page'  => 'roles',
        ],
        [
            'label' => 'Configuración',
            'icon'  => 'bi-gear',
            'route' => '/configuracion',
            'page'  => 'configuracion',
        ],
    ],

    // Menú del Encargado de Stock
    'Encargado de Stock' => [
        [
            'label' => 'Inicio',
            'icon'  => 'bi-house',
            'route' => '/inicio',
            'page'  => 'inicio',
        ],
        [
            'label' => 'Inventario',
            'icon'  => 'bi-box-seam',
            'route' => '/inventario',
            'page'  => 'inventario',
        ],
    ],

    // Menú de Soporte Técnico
    'Soporte Técnico' => [
        [
            'label' => 'Inicio',
            'icon'  => 'bi-house',
            'route' => '/inicio',
            'page'  => 'inicio',
        ],
        [
            'label' => 'Casos de Soporte',
            'icon'  => 'bi-tools',
            'route' => '/tickets',
            'page'  => 'tickets',
        ],
    ],

    // Menú del Coordinador IT
    'Coordinador IT' => [
        [
            'label' => 'Inicio',
            'icon'  => 'bi-house',
            'route' => '/inicio',
            'page'  => 'inicio',
        ],
        [
            'label' => 'Reportes',
            'icon'  => 'bi-graph-up',
            'route' => '/reportes',
            'page'  => 'reportes',
        ],
    ],
];
