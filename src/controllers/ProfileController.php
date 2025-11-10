<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/session.php';

// Verificar sesión
if (!isUserLoggedIn()) {
    echo json_encode(["success" => false, "message" => "Sesión no iniciada."]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$userModel = new UserModel();
$user = currentUser();
$idUsuario = $user['id'] ?? null;

// ==============================
// GET → Obtener datos del usuario
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $userDb = $userModel->findById($idUsuario);

        if (!$userDb) {
            echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
            exit;
        }

        // Verificar si la foto existe físicamente
        if (!empty($userDb['fotoPerfil'])) {
            $fotoPath = __DIR__ . '/../../public/assets/uploads/' . $userDb['fotoPerfil'];
            if (!file_exists($fotoPath)) {
                $userDb['fotoPerfil'] = null;
            }
        }

        // Refrescar la sesión con los datos actuales
        $_SESSION['user'] = buildUserSessionArray($userDb);

        echo json_encode(["success" => true, "user" => $_SESSION['user']]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al obtener el perfil: " . $e->getMessage()]);
    }
    exit;
}

// ========================================
// POST → Subir nueva foto de perfil
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fotoPerfil'])) {
    $file = $_FILES['fotoPerfil'];
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extensionesPermitidas)) {
        echo json_encode(["success" => false, "message" => "Formato de imagen no permitido."]);
        exit;
    }

    $nombreArchivo = 'perfil_' . $idUsuario . '_' . time() . '.' . $ext;
    $rutaDestino = __DIR__ . '/../../public/assets/uploads/' . $nombreArchivo;

    // Eliminar foto anterior si existe
    if (!empty($user['fotoPerfil'])) {
        $fotoAnterior = __DIR__ . '/../../public/assets/uploads/' . $user['fotoPerfil'];
        if (file_exists($fotoAnterior)) {
            unlink($fotoAnterior);
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        echo json_encode(["success" => false, "message" => "Error al subir la imagen."]);
        exit;
    }

    // Actualizar DB y sesión
    $userModel->updatePhoto($idUsuario, $nombreArchivo);
    $_SESSION['user']['fotoPerfil'] = $nombreArchivo;

    echo json_encode(["success" => true, "message" => "Foto de perfil actualizada correctamente."]);
    exit;
}

// ========================================
// PUT → Actualizar datos personales
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);

    $nombre = trim($input['nombre'] ?? '');
    $apellido = trim($input['apellido'] ?? '');
    $email = trim($input['email'] ?? '');

    if ($nombre === '' || $apellido === '' || $email === '') {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    try {
        if ($userModel->updateProfile($idUsuario, $nombre, $apellido, $email)) {
            $updatedUser = $userModel->findById($idUsuario);
            $_SESSION['user'] = buildUserSessionArray($updatedUser);

            echo json_encode(["success" => true, "message" => "Datos actualizados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo actualizar el perfil."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar: " . $e->getMessage()]);
    }
    exit;
}

// ========================================
// PATCH → Cambiar contraseña
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $input = json_decode(file_get_contents("php://input"), true);

    $actualPassword = trim($input['actualPassword'] ?? '');
    $newPassword = trim($input['newPassword'] ?? '');

    if ($actualPassword === '' || $newPassword === '') {
        echo json_encode(["success" => false, "message" => "Complete todos los campos."]);
        exit;
    }

    try {
        // Obtener hash actual
        $userDb = $userModel->findById($idUsuario);
        if (!$userDb || !password_verify($actualPassword, $userDb['password'])) {
            echo json_encode(["success" => false, "message" => "La contraseña actual es incorrecta."]);
            exit;
        }

        // Actualizar nueva contraseña
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $userModel->updatePassword($idUsuario, $newHash);

        echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error del servidor: " . $e->getMessage()]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Petición no válida."]);
