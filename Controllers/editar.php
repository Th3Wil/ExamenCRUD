<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

    if (!$id || !$nombre) {
        die('ID o nombre inválido.');
    }

    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, cantidad = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $cantidad, $id]);

        header('Location: ../index.php');
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar giro: " . $e->getMessage());
    }
} else {
    die('Acceso no permitido.');
}
?>