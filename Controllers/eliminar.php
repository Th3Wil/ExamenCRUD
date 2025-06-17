<?php
require_once '../config/conexion.php';

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    die("Parámetros inválidos.");
}

$id = $_GET['id'];
$tipo = $_GET['tipo'];

try {
    if ($tipo === 'logico') {
        // Guardar un registro antes de borrar lógicamente
        $stmt = $pdo->prepare("SELECT id, nombre FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $giro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$giro) {
            die("Producto no encontrado para eliminar.");
        }

        // Insertar registro en tabla registro
        $usuario = $_SESSION['usuario'] ?? 'administrador';
        $pid = $giro['id'];
        $pnombre = $giro['nombre'];
        $Etipo = 'logico';

        $insertRegistro = $pdo->prepare("INSERT INTO registro (PID, Pnombre, usuario, Etipo) VALUES (?, ?, ?, ?)");
        $insertRegistro->execute([$pid, $pnombre, $usuario, $Etipo]);



        // Eliminación lógica: marcar como eliminado
        $stmt = $pdo->prepare("UPDATE productos SET eliminado = 1 WHERE id = ?");
        $stmt->execute([$id]);


    } elseif ($tipo === 'fisico') {
        // Guardar un registro antes de borrar Fisicamente
        $stmt = $pdo->prepare("SELECT id, nombre FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $giro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$giro) {
            die("Producto no encontrado para eliminar.");
        }

        // Insertar registro en tabla registro
        $usuario = $_SESSION['usuario'] ?? 'administrador';
        $pid = $giro['id'];
        $pnombre = $giro['nombre'];
        $Etipo = 'fisico';

        $insertRegistro = $pdo->prepare("INSERT INTO registro (PID, Pnombre, usuario, Etipo) VALUES (?, ?, ?, ?)");
        $insertRegistro->execute([$pid, $pnombre, $usuario, $Etipo]);

        // Eliminación física: borrar de la base de datos
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        die("Tipo de eliminación no válido.");
    }

    header("Location: ../index.php");
    exit();
} catch (PDOException $e) {
    die("Error al eliminar: " . $e->getMessage());
}
?>