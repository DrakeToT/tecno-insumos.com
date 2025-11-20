<?php

require_once __DIR__ . '/../config/database.php';

class Permisos
{
    /**
     * Función que verifica si el usuario en sesión tiene todos los permisos especificados. 
    */
    public static function tienePermiso($permisos)
    {
        if (is_null($permisos)) return false;

        if (!isset($_SESSION['user']['permisos']) || empty($_SESSION['user']['permisos']) || !is_array($_SESSION['user']['permisos'])) return false;

        $userPerms = $_SESSION['user']['permisos'];

        // Normalizar entrada a array
        if (!is_array($permisos)) {
            // soporta: "perm1 perm2", "perm1,perm2", "perm1, perm2"
            $permisos = preg_split('/[\s,]+/', trim($permisos));
        }

        // Eliminar entradas vacías por si vienen "perm1  , ,  perm2"
        $permisos = array_filter($permisos, fn($p) => trim($p) !== '');

        // Verificar que tenga TODOS
        foreach ($permisos as $permiso) {
            if (!in_array($permiso, $userPerms)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Función que verifica si el usuario en sesión tiene al menos uno de los permisos especificados.
    */
    public static function tieneAlgunPermiso($permisos)
    {
        if (is_null($permisos)) return false;

        // Validar permisos del usuario en sesión
        if (
            !isset($_SESSION['user']['permisos']) ||
            !is_array($_SESSION['user']['permisos'])
        ) {
            return false;
        }

        $userPerms = $_SESSION['user']['permisos'];

        // Normalizar entrada a array
        if (!is_array($permisos)) {
            // soporta: "perm1 perm2", "perm1,perm2", "perm1, perm2"
            $permisos = preg_split('/[\s,]+/', trim($permisos));
        }

        // Eliminar entradas vacías por si vienen "perm1  , ,  perm2"
        $permisos = array_filter($permisos, fn($p) => trim($p) !== '');

        // Verificar que tenga AL MENOS UNO
        foreach ($permisos as $permiso) {
            if (in_array($permiso, $userPerms)) {
                return true;
            }
        }

        return false;
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
