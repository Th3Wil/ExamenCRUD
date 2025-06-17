<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $id]);
        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
} else {
    echo "Acceso no permitido.";
}
?>