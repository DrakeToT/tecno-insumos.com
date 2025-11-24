-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2025 a las 21:15:12
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tecnoinsumos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Sistemas', NULL, 'Activo'),
(2, 'Recursos Humanos', NULL, 'Activo'),
(3, 'Contabilidad', NULL, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Ej: Notebook, Monitor, Impresora',
  `stock_minimo` int(11) DEFAULT 0 COMMENT 'Umbral para generar alertas',
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `stock_minimo`, `descripcion`, `estado`) VALUES
(1, 'Notebooks', 5, 'Laptops corporativas para uso de staff y gerencia', 'Activo'),
(2, 'Monitores', 3, 'Pantallas LED/IPS de 22 a 27 pulgadas', 'Activo'),
(3, 'Periféricos', 10, 'Teclados, mouses, auriculares y webcams', 'Activo'),
(4, 'Servidores', 1, 'Equipos de infraestructura y rack', 'Activo'),
(5, 'Impresoras', 2, 'Impresoras láser y multifunción', 'Activo'),
(6, 'Redes', 5, 'Routers, Switches y Access Points', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `codigo_inventario` varchar(50) NOT NULL COMMENT 'Etiqueta de patrimonio',
  `id_categoria` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `numero_serie` varchar(100) DEFAULT NULL COMMENT 'Serial único del fabricante',
  `estado` enum('Disponible','Asignado','En reparacion','Baja') DEFAULT 'Disponible',
  `ubicacion_detalle` varchar(100) DEFAULT NULL,
  `fecha_adquisicion` date DEFAULT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `valor_compra` decimal(10,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `codigo_inventario`, `id_categoria`, `marca`, `modelo`, `numero_serie`, `estado`, `ubicacion_detalle`, `fecha_adquisicion`, `proveedor`, `valor_compra`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'NB-001', 1, 'Dell', 'Latitude 5420', '8H29GK2', 'Disponible', 'Depósito IT - Estante A', '2023-01-15', 'Dell Direct', 1200.00, 'Equipo nuevo, listo para asignar.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(2, 'NB-002', 1, 'HP', 'ProBook 450 G8', 'CND1234X', 'Asignado', 'Oficina RRHH', '2022-11-20', 'Compumundo', 950.50, 'Asignada a Gerente RRHH.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(3, 'NB-003', 1, 'Lenovo', 'ThinkPad T14', 'LNV-998877', 'En reparacion', 'Servicio Técnico Externo', '2021-06-10', 'Lenovo Corp', 1100.00, 'Falla en disco duro, enviado a garantía.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(4, 'NB-004', 1, 'Apple', 'MacBook Air M1', 'C02F1234', 'Disponible', 'Depósito IT - Caja Fuerte', '2023-05-05', 'MacStation', 999.00, 'Reservada para Diseño.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(5, 'NB-005', 1, 'Dell', 'Vostro 3510', '7J19HL3', 'Baja', 'Depósito Residuos Electrónicos', '2019-03-15', 'Dell Direct', 600.00, 'Pantalla rota y placa madre quemada. Irreparable.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(6, 'MON-101', 2, 'Samsung', 'F24T35', 'Z123456', 'Disponible', 'Depósito IT - Estante B', '2023-02-01', 'Samsung Store', 180.00, 'Monitor 24 pulgadas IPS.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(7, 'MON-102', 2, 'LG', '29WP500', 'LG-ULTRA-01', 'Asignado', 'Oficina Desarrollo', '2023-02-01', 'Amazon', 250.00, 'Monitor Ultrawide para programadores.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(8, 'MON-103', 2, 'Dell', 'P2419H', 'CN-0H', 'Disponible', 'Depósito IT - Estante B', '2022-08-15', 'Dell Direct', 210.00, NULL, '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(9, 'PER-201', 3, 'Logitech', 'MX Master 3', 'LN-MOUSE-01', 'Asignado', 'Oficina Diseño', '2023-07-20', 'Logitech Store', 99.99, 'Mouse ergonómico.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(10, 'PER-202', 3, 'Logitech', 'K380', 'LN-KEY-02', 'Disponible', 'Depósito IT - Cajón 1', '2023-01-10', 'MercadoLibre', 45.00, 'Teclado bluetooth español.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(11, 'SRV-001', 4, 'Dell', 'PowerEdge R740', 'SRV-DELL-X1', 'Asignado', 'Sala de Servidores - Rack 1', '2020-01-01', 'Dell Enterprise', 5500.00, 'Servidor principal de base de datos.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(12, 'SRV-002', 4, 'HP', 'ProLiant DL380', 'SRV-HP-Y2', 'En reparacion', 'Sala de Servidores', '2021-05-12', 'HP Enterprise', 4200.00, 'Fallo en fuente redundante.', '2025-11-24 15:46:19', '2025-11-24 15:46:19'),
(13, 'PRT-301', 5, 'Brother', 'HL-1212W', 'BR-PRT-99', 'Disponible', 'Depósito IT', '2022-03-30', 'Office Depot', 150.00, 'Impresora láser monocromática.', '2025-11-24 15:46:19', '2025-11-24 15:46:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'ver_usuarios', 'Puede visualizar la lista de usuarios'),
(2, 'crear_usuarios', 'Puede crear nuevos usuarios'),
(3, 'editar_usuarios', 'Puede editar los datos de usuarios'),
(4, 'eliminar_usuarios', 'Puede eliminar usuarios'),
(5, 'ver_reportes', 'Puede ver reportes del sistema'),
(6, 'gestionar_stock', 'Puede crear o modificar insumos'),
(7, 'gestionar_mantenimientos', 'Puede registrar y actualizar reparaciones'),
(14, 'ver_roles', 'Permite visualizar la lista de roles'),
(15, 'crear_roles', 'Permite crear nuevos roles'),
(16, 'editar_roles', 'Permite editar roles existentes'),
(17, 'eliminar_roles', 'Permite eliminar roles'),
(18, 'asignar_permisos', 'Permite asignar permisos a los roles'),
(19, 'ver_inventario', 'Permite ver el listado y detalles de equipos'),
(20, 'crear_equipos', 'Permite dar de alta nuevos equipos'),
(21, 'editar_equipos', 'Permite modificar datos de equipos existentes'),
(22, 'eliminar_equipos', 'Permite eliminar equipos del sistema'),
(23, 'editar_perfil', 'Permite modificar nombre, apellido y email del propio perfil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Administrador', 'Acceso total al sistema', 'Activo'),
(2, 'Encargado de Stock', 'Gestiona inventarios y asignaciones', 'Activo'),
(3, 'Soporte Técnico', 'Registra mantenimientos y asignaciones técnicas', 'Activo'),
(4, 'Coordinador IT', 'Supervisa reportes y aprueba órdenes de compra', 'Activo'),
(5, 'RRHH', 'Gestión de Nómina.', 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rolespermisos`
--

CREATE TABLE `rolespermisos` (
  `id` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `idPermiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rolespermisos`
--

INSERT INTO `rolespermisos` (`id`, `idRol`, `idPermiso`) VALUES
(97, 1, 1),
(90, 1, 2),
(92, 1, 3),
(94, 1, 4),
(95, 1, 5),
(96, 1, 14),
(89, 1, 15),
(91, 1, 16),
(93, 1, 17),
(88, 1, 18),
(98, 1, 23),
(103, 2, 5),
(101, 2, 6),
(102, 2, 19),
(99, 2, 20),
(100, 2, 21),
(12, 3, 5),
(11, 3, 7),
(15, 4, 1),
(14, 4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `idRol` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fechaAlta` datetime DEFAULT current_timestamp(),
  `fotoPerfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `idRol`, `estado`, `fechaAlta`, `fotoPerfil`) VALUES
(1, 'Ezequiel', 'Fernandez', 'admin@tecnoinsumos.com', '$2y$10$iJcf6SlH/qsSm0nfWrEa/uzOUuiHPLEw5php/YbP9elUdH0ukU7sy', 1, 'Activo', '2025-10-17 13:17:24', 'perfil_1_1764015245.jpg'),
(2, 'Gonzalo', 'Fernandez', 'stock@tecnoinsumos.com', '$2y$10$Wm38WelPtSK99npv3LUvCubhBHS.U6F6.vu9JWeKAvtdiLA5BcPly', 2, 'Activo', '2025-10-25 20:19:07', 'perfil_2_1763945974.gif'),
(5, 'Nicolas', 'Garrido', 'soporte@tecnoinsumos.com', '$2y$10$Kq0pdeTugWhYzxMflXYcduT2JDhA/KlYIhFM19OlouNV0DdPDP3EO', 3, 'Activo', '2025-10-26 18:10:40', 'perfil_5_1764015127.png'),
(6, 'Max', 'Verstappen', 'MVerstappen@F1.com', '$2y$10$n18d/Kb60ZtHX.ZlrKr7I.G2DhLVzvWp4gFmCvgtbhghKfMkkZY5G', 3, 'Activo', '2025-10-27 17:30:43', NULL),
(7, 'Gerrardo', 'Ibarra', 'coordinador@tecnoinsumos.com', '$2y$10$gyT.h0qqdhCXEDpY8Z82FuH0Jq4B2.9DrH7OjnoZM1kAFMtB3NG1K', 4, 'Activo', '2025-10-27 17:31:25', 'perfil_7_1764015275.jpg'),
(8, 'Carlos', 'Saint', 'CSaint@F1.com', '$2y$10$EXdFBysRihRWaB4nI3e2M.m6Fn9enWKgZgwwGj.6uy/qO.hUCjcQq', 3, 'Activo', '2025-10-27 17:32:34', NULL),
(9, 'Lewis', 'Hamilton', 'LHamilton@F1.com', '$2y$10$I5GodNMDe94wHmARM7Vr3ulumsImXsd6aN6h.ia976WMpRFw.HD6e', 4, 'Activo', '2025-10-27 17:33:12', NULL),
(10, 'Landon', 'Norris', 'LNorris@F1.com', '$2y$10$mmLI249bOwCkYBq5/nJzHuo2RIgd6Ihvh4WeaGyZ4Xj/0kAMm//C.', 2, 'Activo', '2025-10-27 17:33:43', NULL),
(11, 'Valteri', 'Botta', 'VBotta@F1.com', '$2y$10$DN2nWgGC1Hit3Uy/ml.EXOm/B8UjQAlxjivDJ1T.0DZjiHZa.d1fq', 4, 'Activo', '2025-10-27 17:34:23', NULL),
(12, 'Charles', 'LeClerc', 'CLeClerc@F1.com', '$2y$10$9Cy7cFsg7.J4ArhBF5Bue.OXwcQ8./Es1hS.L2yJxdZUmxmM7dV7m', 4, 'Activo', '2025-10-27 18:12:00', NULL),
(13, 'Franco', 'Colapinto', 'FColapinto@F1.com', '$2y$10$hehzTQn/Br/Mpi/Tf6Zx0uam/7CmyZ5nxLwN3CYybiK9k70ydzY0i', 4, 'Inactivo', '2025-11-10 15:39:23', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_inventario` (`codigo_inventario`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `fk_equipo_categoria` (`id_categoria`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`) USING BTREE;

--
-- Indices de la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rol_permiso` (`idRol`,`idPermiso`),
  ADD KEY `idPermiso` (`idPermiso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idRol` (`idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipo_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  ADD CONSTRAINT `rolespermisos_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rolespermisos_ibfk_2` FOREIGN KEY (`idPermiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
