<?php

require_once __DIR__ . '/../config/database.php';

class Permisos
{

    public static function tienePermiso($permiso, $idUsuario)
    {
        if (is_null($permiso) || is_null($idUsuario)) {
            return false;
        }
        if (!is_array($permiso)) {
            $permisos = [$permiso];
        } else {
            $permisos = $permiso;
        }
        return self::tieneAlgunPermiso($permisos, $idUsuario);
    }

    public static function tieneAlgunPermiso($permisos, $idUsuario)
    {
        if (is_null($permisos) || !is_array($permisos) || empty($permisos) || is_null($idUsuario)) {
            return false;
        }
        $db = new Database();
        $conn = $db->getConnection();
        $bindPermisos = implode(',', array_map(function ($p, $k) {
            return ":permiso$k";
        }, $permisos, array_keys($permisos)));
        $sql = "SELECT 
                    1
                FROM 
                    permisos
                INNER JOIN 
                    rolesPermisos 
                        ON 
                            rolesPermisos.idPermiso = permisos.id
                INNER JOIN 
                    usuarios 
                        ON 
                            usuarios.idRol = rolesPermisos.idRol
                WHERE 
                    usuarios.id = :idUsuario
                AND permisos.nombre IN ($bindPermisos)
                LIMIT 1;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario);
        array_walk($permisos, function ($p, $k) use ($stmt) {
            $stmt->bindValue(":permiso$k", $p);
        });
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return !empty($result);
    }

    public static function getPermisos($idUsuario)
    {
        if (is_null($idUsuario)) {
            return [];
        }
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT 
                    permisos.nombre
                FROM 
                    permisos
                INNER JOIN
                    rolesPermisos
                        ON
                            rolesPermisos.idPermiso = permisos.id
                INNER JOIN
                    usuarios
                        ON
                            usuarios.idRol = rolesPermisos.idRol
                WHERE 
                    usuarios.id = :idUsuario;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($result, 'nombre');
    }

    public static function getRoles($idUsuario)
    {
        if (is_null($idUsuario)) {
            return [];
        }
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT 
                    roles.nombre
                FROM 
                    roles
                INNER JOIN
                    usuarios
                        ON
                            usuarios.idRol = roles.id
                WHERE 
                    usuarios.id = :idUsuario;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public static function esRol($rol, $idUsuario)
    {
        if (is_null($rol) || is_null($idUsuario)) {
            return false;
        }
        if (!is_array($rol)) {
            $roles = [$rol];
        } else {
            $roles = $rol;
        }
        return self::esAlgunRol($roles, $idUsuario);
    }
    public static function esAlgunRol($roles, $idUsuario)
    {
        if (is_null($roles) || !is_array($roles) || empty($roles) || is_null($idUsuario)) {
            return false;
        }
        $db = new Database();
        $conn = $db->getConnection();
        $bindRoles = implode(',', array_map(function ($p, $k) {
            return ":rol$k";
        }, $roles, array_keys($roles)));
        $sql = "SELECT 
                    1 
                FROM 
                    roles
                INNER JOIN
                    usuarios
                        ON
                            usuarios.idRol = roles.id
                WHERE 
                        usuarios.id = :idUsuario 
                    AND roles.nombre IN ($bindRoles)
                LIMIT 1;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario);
        array_walk($roles, function ($p, $k) use ($stmt) {
            $stmt->bindValue(":rol$k", $p);
        });
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return !empty($result);
    }
}
