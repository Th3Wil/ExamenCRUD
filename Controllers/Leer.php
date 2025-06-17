<?php
require_once __DIR__ . '/../config/conexion.php';


try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE eliminado = 0 ORDER BY Fecha_creacion DESC");
    $stmt->execute();
    $giros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar Productos: " . $e->getMessage());
}
?>