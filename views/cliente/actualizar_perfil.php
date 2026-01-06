<?php
session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Obtener ID del cliente
    $query = "SELECT id FROM clientes WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();
    
    if (!$cliente) {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
        exit();
    }
    
    $cliente_id = $cliente['id'];
    
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $celular = trim($_POST['celular']);
    $provincia = $_POST['provincia'];
    $direccion = trim($_POST['direccion']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Los campos nombre, apellido y email son obligatorios']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit();
    }
    
    // Verificar si el email ya está en uso por otro usuario
    $query_check = "SELECT u.id FROM usuarios u 
                    INNER JOIN clientes c ON u.id = c.usuario_id 
                    WHERE u.email = ? AND c.id != ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("si", $email, $cliente_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
        exit();
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Actualizar tabla clientes
        $query_cliente = "UPDATE clientes SET 
                          nombre = ?, 
                          apellido = ?, 
                          fecha_nacimiento = ?, 
                          telefono = ?, 
                          celular = ?, 
                          provincia = ?, 
                          direccion = ?
                          WHERE id = ?";
        $stmt_cliente = $conn->prepare($query_cliente);
        $stmt_cliente->bind_param("sssssssi", $nombre, $apellido, $fecha_nacimiento, 
                                   $telefono, $celular, $provincia, $direccion, $cliente_id);
        $stmt_cliente->execute();
        
        // Actualizar tabla usuarios (email)
        $query_usuario = "UPDATE usuarios SET email = ? WHERE id = ?";
        $stmt_usuario = $conn->prepare($query_usuario);
        $stmt_usuario->bind_param("si", $email, $usuario_id);
        $stmt_usuario->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente']);
        
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conn->close();
?>