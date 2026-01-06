<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $_SESSION['error_password'] = 'Todos los campos son obligatorios';
        header('Location: perfil.php');
        exit();
    }
    
    if ($password_nueva !== $password_confirmar) {
        $_SESSION['error_password'] = 'Las contraseñas nuevas no coinciden';
        header('Location: perfil.php');
        exit();
    }
    
    if (strlen($password_nueva) < 6) {
        $_SESSION['error_password'] = 'La contraseña debe tener al menos 6 caracteres';
        header('Location: perfil.php');
        exit();
    }
    
    // Verificar contraseña actual
    $query = "SELECT password FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if (!$usuario) {
        $_SESSION['error_password'] = 'Usuario no encontrado';
        header('Location: perfil.php');
        exit();
    }
    
    // Verificar contraseña actual
    if (!password_verify($password_actual, $usuario['password'])) {
        $_SESSION['error_password'] = 'La contraseña actual es incorrecta';
        header('Location: perfil.php');
        exit();
    }
    
    // Encriptar nueva contraseña
    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    
    // Actualizar contraseña
    $query_update = "UPDATE usuarios SET password = ?, fecha_cambio_password = NOW() WHERE id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("si", $password_hash, $usuario_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['success_password'] = 'Contraseña actualizada correctamente';
        header('Location: perfil.php?success=1');
    } else {
        $_SESSION['error_password'] = 'Error al actualizar la contraseña';
        header('Location: perfil.php');
    }
    
} else {
    header('Location: perfil.php');
}

$conn->close();
?>