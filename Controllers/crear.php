<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    try {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, cantidad) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $cantidad]);
        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        die("Error al guardar: " . $e->getMessage());
    }
} else {
    echo "Acceso no permitido.";
}
