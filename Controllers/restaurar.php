<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        die("ID inválido.");
    }

    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE productos SET eliminado = 0 WHERE id = ?");
        $stmt->execute([$id]);

        
        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        die("Error al restaurar el producto: " . $e->getMessage());
    }
} else {
    echo "Acceso no permitido.";
}
?>
