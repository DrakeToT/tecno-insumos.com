-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-12-2025 a las 18:59:16
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
  `prefijo` varchar(5) DEFAULT NULL,
  `stock_minimo` int(11) DEFAULT 0 COMMENT 'Umbral para generar alertas',
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `prefijo`, `stock_minimo`, `descripcion`, `estado`) VALUES
(1, 'Notebooks', 'NB', 5, 'Laptops corporativas para uso de staff y gerencia', 'Activo'),
(2, 'Monitores', 'MON', 3, 'Pantallas LED/IPS de 22 a 27 pulgadas', 'Activo'),
(3, 'Periféricos', 'PER', 10, 'Teclados, mouses, auriculares y webcams', 'Activo'),
(4, 'Servidores', 'SRV', 1, 'Equipos de infraestructura y rack', 'Activo'),
(5, 'Impresoras', 'PRT', 2, 'Impresoras láser y multifunción', 'Activo'),
(6, 'Redes', 'RED', 5, 'Routers, Switches y Access Points', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `legajo` varchar(20) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `apellido`, `legajo`, `puesto`, `estado`) VALUES
(1, 'Roberto', 'Gomez', 'L-100', 'Operario Depósito', 'Activo'),
(2, 'Maria', 'Lopez', 'L-102', 'Recepcionista', 'Activo'),
(3, 'Carlos', 'Tevez', 'L-103', 'Chofer', 'Activo');

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
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `asignado_tipo` enum('usuario','area','empleado') DEFAULT NULL COMMENT 'Tipo de entidad asignada',
  `asignado_id` int(11) DEFAULT NULL COMMENT 'ID de la entidad (id_usuario, id_area o id_empleado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `codigo_inventario`, `id_categoria`, `marca`, `modelo`, `numero_serie`, `estado`, `ubicacion_detalle`, `fecha_adquisicion`, `proveedor`, `valor_compra`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`, `asignado_tipo`, `asignado_id`) VALUES
(1, 'NB-0001', 1, 'Dell', 'Latitude 3520', 'SR-DELL-001', 'Disponible', 'Depósito Central - Estante A', '2025-11-01', 'Dell Direct', 1100.00, 'Equipo nuevo.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(2, 'PRT-0004', 5, 'Samsung', 'T350 24\"', 'SR-SAM-001', 'Disponible', 'Depósito Central - Estante B', '2025-10-15', 'Samsung Store', 200.00, 'Monitor stock.', '2025-12-04 15:39:10', '2025-12-05 14:27:04', NULL, NULL),
(3, 'PER-0001', 3, 'Logitech', 'MK270 Combo', 'SR-LOG-001', 'Disponible', 'Armario IT - Cajón 2', '2025-11-20', 'MercadoLibre', 35.00, 'Kit inalámbrico.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(4, 'RED-0001', 6, 'Ubiquiti', 'UniFi AP AC', 'SR-UBI-001', 'Disponible', 'Depósito IT', '2025-09-10', 'USA Tech', 150.00, 'Access Point.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(5, 'NB-0002', 1, 'HP', 'ProBook 450', 'SR-HP-002', 'Asignado', 'Oficina Gerencia', '2024-05-20', 'HP Store', 1300.00, 'Gerencia.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', 'usuario', 1),
(6, 'MON-0002', 2, 'LG', '27MK600', 'SR-LG-002', 'Asignado', 'Recepción', '2024-03-10', 'Amazon', 250.00, 'Recepción.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', 'empleado', 2),
(7, 'PRT-0001', 5, 'Epson', 'L3150', 'SR-EPS-002', 'Asignado', 'Área Contable', '2023-12-01', 'Office Depot', 300.00, 'Compartida.', '2025-12-04 15:39:10', '2025-12-05 12:12:49', 'area', 3),
(8, 'PER-0002', 3, 'HyperX', 'Cloud Flight', 'SR-HYP-002', 'Asignado', 'Soporte IT', '2025-01-15', 'Compra Gamer', 100.00, 'Headset.', '2025-12-04 15:39:10', '2025-12-05 12:12:57', 'usuario', 5),
(9, 'NB-0003', 1, 'Lenovo', 'ThinkPad E14', 'SR-LEN-003', 'En reparacion', 'Laboratorio Externo', '2023-08-15', 'Lenovo Corp', 900.00, 'Falla placa.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(10, 'MON-0003', 2, 'ViewSonic', 'VA2405', 'SR-VIEW-003', 'En reparacion', 'Mesa de Trabajo IT', '2022-11-30', 'Local PC', 180.00, 'No enciende.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(11, 'SRV-0001', 4, 'Dell', 'PowerEdge T40', 'SR-SRV-003', 'En reparacion', 'Sala Servidores', '2021-06-20', 'Dell Ent', 2500.00, 'Fallo RAID.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(12, 'PRT-0002', 5, 'HP', 'LaserJet M428', 'SR-HPP-003', 'En reparacion', 'Taller Propio', '2022-04-10', 'HP Store', 450.00, 'Cambio fusor.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(13, 'NB-0004', 1, 'Compaq', 'Presario CQ56', 'SR-OLD-004', 'Baja', 'Depósito Basura Elec.', '2015-01-01', 'Desconocido', 500.00, 'Obsoleto.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(14, 'MON-0004', 2, 'Samsung', 'SyncMaster 17\"', 'SR-OLD-005', 'Baja', 'Donación Escuela', '2014-05-20', 'Compumundo', 150.00, 'Donado.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(15, 'PER-0003', 3, 'Genius', 'Mouse PS/2', 'SR-OLD-006', 'Baja', 'Reciclaje', '2012-10-10', 'Librería', 10.00, 'Roto.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(16, 'RED-0002', 6, 'Cisco', 'Linksys WRT54G', 'SR-OLD-007', 'Baja', 'Depósito Residuos', '2010-03-15', 'Cisco', 80.00, 'Viejo.', '2025-12-04 15:39:10', '2025-12-04 15:39:10', NULL, NULL),
(17, 'MON-0005', 2, 'ViewSonic', 'VX2728J-2K', 'VS19278', 'Asignado', 'Usuario: Ezequiel Fernandez', NULL, '', NULL, 'Pulgadas: 27’’. Resolución: 2560x1440. Frecuencia: 180Hz.', '2025-12-05 13:32:38', '2025-12-05 13:40:46', 'usuario', 1),
(18, 'PRT-0003', 5, 'Epson', 'EcoTank', 'ET-1828', 'Disponible', 'Depósito IT', NULL, '', NULL, 'No imprime a color', '2025-12-05 14:12:55', '2025-12-05 14:12:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_equipos`
--

CREATE TABLE `movimientos_equipos` (
  `id` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que realizó la acción',
  `tipo_movimiento` enum('Alta','Asignacion','Devolucion','Baja','Reparacion','Ajuste') NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_equipos`
--

INSERT INTO `movimientos_equipos` (`id`, `id_equipo`, `id_usuario`, `tipo_movimiento`, `fecha`, `observaciones`) VALUES
(1, 5, 1, 'Asignacion', '2025-12-04 15:39:10', 'Ingreso directo con asignación. Usuario: Ezequiel Fernandez.'),
(2, 6, 1, 'Asignacion', '2025-12-04 15:39:10', 'Ingreso directo con asignación. Empleado: Maria Lopez.'),
(3, 7, 1, 'Asignacion', '2025-12-04 15:39:10', 'Ingreso directo con asignación. Área: Contabilidad.'),
(4, 8, 1, 'Asignacion', '2025-12-04 15:39:10', 'Ingreso directo con asignación. Usuario: Nicolas Garrido.'),
(5, 1, 1, 'Alta', '2025-12-04 15:39:10', 'Alta inicial. Equipo nuevo disponible en Depósito Central.'),
(6, 2, 1, 'Alta', '2025-12-04 15:39:10', 'Alta inicial. Monitor de stock disponible en Depósito Central.'),
(7, 3, 1, 'Alta', '2025-12-04 15:39:10', 'Alta inicial. Periférico disponible en Armario IT.'),
(8, 4, 1, 'Alta', '2025-12-04 15:39:10', 'Alta inicial. Equipo de red disponible en Depósito IT.'),
(9, 9, 1, 'Reparacion', '2025-12-04 15:39:10', 'Ingreso directo a reparación. Detalle: Falla placa. Ubicación: Laboratorio Externo.'),
(10, 10, 1, 'Reparacion', '2025-12-04 15:39:10', 'Ingreso directo a reparación. Detalle: No enciende. Ubicación: Mesa de Trabajo IT.'),
(11, 11, 1, 'Reparacion', '2025-12-04 15:39:10', 'Ingreso directo a reparación. Detalle: Fallo RAID. Ubicación: Sala Servidores.'),
(12, 12, 1, 'Reparacion', '2025-12-04 15:39:10', 'Ingreso directo a reparación. Detalle: Cambio fusor. Ubicación: Taller Propio.'),
(13, 13, 1, 'Baja', '2025-12-04 15:39:10', 'Registro histórico de baja. Motivo: Obsoleto. Destino: Depósito Basura Elec.'),
(14, 14, 1, 'Baja', '2025-12-04 15:39:10', 'Registro histórico de baja. Motivo: Donado. Destino: Donación Escuela.'),
(15, 15, 1, 'Baja', '2025-12-04 15:39:10', 'Registro histórico de baja. Motivo: Roto. Destino: Reciclaje.'),
(16, 16, 1, 'Baja', '2025-12-04 15:39:10', 'Registro histórico de baja. Motivo: Viejo. Destino: Depósito Residuos.'),
(17, 17, 2, 'Alta', '2025-12-05 13:32:38', 'Alta inicial. Equipo disponible en stock. Resolución: 2560x1440Frecuencia: 180Hz'),
(21, 18, 2, 'Alta', '2025-12-05 14:12:55', 'Alta inicial en stock. No imprime a color'),
(22, 2, 2, 'Ajuste', '2025-12-05 14:27:04', 'Motivo: Ajuste de categoría - Cambios: Código: \'MON-0001\' -> \'PRT-0004\'.');

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
(1, 'acceder_usuarios', 'Permite acceder a la vista de gestión de usuarios.'),
(2, 'crear_usuario', 'Permite registrar un nuevo usuario.'),
(3, 'editar_usuario', 'Permite modificar datos de un usuario existente.'),
(4, 'eliminar_usuario', 'Permite eliminar un usuario del sistema.'),
(5, 'ver_reportes', 'Puede ver reportes del sistema'),
(6, 'gestionar_stock', 'Puede crear o modificar insumos'),
(7, 'gestionar_mantenimientos', 'Puede registrar y actualizar reparaciones'),
(14, 'ver_roles', 'Permite visualizar la lista de roles'),
(15, 'crear_roles', 'Permite crear nuevos roles'),
(16, 'editar_roles', 'Permite editar roles existentes'),
(17, 'eliminar_roles', 'Permite eliminar roles'),
(18, 'asignar_permisos', 'Permite asignar permisos a los roles'),
(19, 'acceder_inventario', 'Permite acceder a la vista de gestión de inventario.\r\n'),
(20, 'crear_equipo', 'Permite registrar un nuevo equipo en el inventario.'),
(21, 'editar_equipo', 'Permite modificar los datos de un equipo existente.'),
(22, 'eliminar_equipo', 'Permite eliminar un equipo del sistema.'),
(23, 'editar_perfil', 'Permite modificar nombre, apellido y email del propio perfil'),
(24, 'listar_usuarios', 'Permite obtener la lista completa de usuarios.'),
(25, 'listar_equipos', 'Permite acceder al listado de equipos registrados en el sistema.'),
(26, 'consultar_usuario', 'Permite consultar la información de un usuario específico.'),
(27, 'consultar_equipo', 'Permite consultar la información detallada de un equipo específico mediante su identificador.'),
(28, 'listar_categorias', 'Permite obtener el listado completo de categorías registradas en el sistema.'),
(29, 'listar_empleados', 'Permite obtener el listado completo de empleados registrados en el sistema.'),
(30, 'listar_areas', 'Permite obtener el listado completo de áreas o sectores registrados en la empresa.'),
(31, 'consultar_historial_equipo', 'Permite consultar el historial de movimientos asociados a un equipo específico.'),
(32, 'listar_categorias_activas', 'Permite obtener únicamente las categorías activas.'),
(33, 'listar_empleados_activos', 'Permite obtener únicamente los empleados activos.'),
(34, 'listar_usuarios_activos', 'Permite obtener únicamente los usuarios activos.'),
(35, 'listar_areas_activas', 'Permite obtener únicamente las áreas activas.'),
(36, 'consultar_categoria', 'Permite consultar la información de una categoría específica.'),
(37, 'consultar_categoria_activa', 'Permite consultar la información de solo una categoría activa.');

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
(142, 1, 1),
(145, 1, 2),
(148, 1, 3),
(150, 1, 4),
(152, 1, 5),
(153, 1, 14),
(144, 1, 15),
(147, 1, 16),
(149, 1, 17),
(143, 1, 18),
(146, 1, 23),
(151, 1, 24),
(154, 1, 26),
(190, 2, 5),
(184, 2, 6),
(179, 2, 19),
(181, 2, 20),
(182, 2, 21),
(183, 2, 22),
(188, 2, 25),
(180, 2, 27),
(186, 2, 32),
(187, 2, 33),
(189, 2, 34),
(185, 2, 35),
(191, 2, 37),
(12, 3, 5),
(11, 3, 7),
(194, 4, 1),
(195, 4, 5),
(198, 4, 32),
(197, 4, 33),
(196, 4, 34),
(199, 4, 35);

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
(2, 'Gonzalo', 'Fernandez', 'stock@tecnoinsumos.com', '$2y$10$FjBsrB9auEY74UFNYaz5AO0uV38vxwLR4.HF.p8QGmD9Lc9VcpxZK', 2, 'Activo', '2025-10-25 20:19:07', 'perfil_2_1764789623.png'),
(5, 'Nicolas', 'Garrido', 'soporte@tecnoinsumos.com', '$2y$10$Kq0pdeTugWhYzxMflXYcduT2JDhA/KlYIhFM19OlouNV0DdPDP3EO', 3, 'Activo', '2025-10-26 18:10:40', 'perfil_5_1764015127.png'),
(6, 'Max', 'Verstappen', 'MVerstappen@F1.com', '$2y$10$n18d/Kb60ZtHX.ZlrKr7I.G2DhLVzvWp4gFmCvgtbhghKfMkkZY5G', 3, 'Inactivo', '2025-10-27 17:30:43', NULL),
(7, 'Gerrardo', 'Ibarra', 'coordinador@tecnoinsumos.com', '$2y$10$gyT.h0qqdhCXEDpY8Z82FuH0Jq4B2.9DrH7OjnoZM1kAFMtB3NG1K', 4, 'Activo', '2025-10-27 17:31:25', 'perfil_7_1764015275.jpg'),
(8, 'Carlos', 'Saint', 'CSaint@F1.com', '$2y$10$EXdFBysRihRWaB4nI3e2M.m6Fn9enWKgZgwwGj.6uy/qO.hUCjcQq', 3, 'Inactivo', '2025-10-27 17:32:34', NULL),
(9, 'Lewis', 'Hamilton', 'LHamilton@F1.com', '$2y$10$I5GodNMDe94wHmARM7Vr3ulumsImXsd6aN6h.ia976WMpRFw.HD6e', 4, 'Inactivo', '2025-10-27 17:33:12', NULL),
(10, 'Landon', 'Norris', 'LNorris@F1.com', '$2y$10$mmLI249bOwCkYBq5/nJzHuo2RIgd6Ihvh4WeaGyZ4Xj/0kAMm//C.', 2, 'Inactivo', '2025-10-27 17:33:43', NULL),
(11, 'Valteri', 'Botta', 'VBotta@F1.com', '$2y$10$DN2nWgGC1Hit3Uy/ml.EXOm/B8UjQAlxjivDJ1T.0DZjiHZa.d1fq', 4, 'Inactivo', '2025-10-27 17:34:23', NULL),
(12, 'Charles', 'LeClerc', 'CLeClerc@F1.com', '$2y$10$9Cy7cFsg7.J4ArhBF5Bue.OXwcQ8./Es1hS.L2yJxdZUmxmM7dV7m', 4, 'Inactivo', '2025-10-27 18:12:00', NULL),
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
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_inventario` (`codigo_inventario`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `fk_equipo_categoria` (`id_categoria`);

--
-- Indices de la tabla `movimientos_equipos`
--
ALTER TABLE `movimientos_equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_moveq_equipo` (`id_equipo`),
  ADD KEY `fk_moveq_usuario` (`id_usuario`);

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
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `movimientos_equipos`
--
ALTER TABLE `movimientos_equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

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
-- Filtros para la tabla `movimientos_equipos`
--
ALTER TABLE `movimientos_equipos`
  ADD CONSTRAINT `fk_moveq_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_moveq_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

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
