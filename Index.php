<?php
require_once 'config/conexion.php';
require_once 'controllers/Leer.php';
// Consultas
try {
    $productos = $pdo->query("SELECT * FROM productos WHERE eliminado = 0")->fetchAll(PDO::FETCH_ASSOC);
    $eliminados = $pdo->query("SELECT * FROM productos WHERE eliminado = 1")->fetchAll(PDO::FETCH_ASSOC);
    $registros = $pdo->query("SELECT * FROM registro ORDER BY F_borrado DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Almacén de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .seccion { display: none; }
        .seccion.activa { display: block; }

        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 0.3rem;
            max-width: 500px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px; right: 15px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
            cursor: pointer;
        }
        .close:hover {
            color: red;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4">📦 Sistema de Almacén</h1>

    <!-- Menú -->
    <ul class="nav nav-tabs mb-4" id="menuTabs">
        <li class="nav-item">
            <button class="nav-link active" data-target="productos">Productos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-target="eliminados">Eliminados</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-target="registros">Registros</button>
        </li>
    </ul>

    <!-- Productos -->
    <div id="productos" class="seccion activa">
        <h3>Productos Registrados</h3>
        <button class="btn btn-primary mb-3" id="btnAbrirModal">➕ Agregar nuevo Producto</button>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($giros as $giro): ?>
                    <tr>
                        <td><?= $giro['id'] ?></td>
                        <td><?= htmlspecialchars($giro['nombre']) ?></td>
                        <td><?= htmlspecialchars($giro['descripcion']) ?></td>
                        <td><?= htmlspecialchars($giro['cantidad']) ?></td>
                        <td><?= $giro['Fecha_creacion'] ?></td>
                        <td>
                            <button
                                class="btn btn-sm btn-warning btnEditar"
                                data-id="<?= $giro['id'] ?>"
                                data-nombre="<?= htmlspecialchars($giro['nombre'], ENT_QUOTES) ?>"
                                data-descripcion="<?= htmlspecialchars($giro['descripcion'], ENT_QUOTES) ?>"
                                data-cantidad="<?= htmlspecialchars($giro['cantidad'], ENT_QUOTES) ?>">
                                ✏️ Editar
                            </button>
                            |
                            <a href="controllers/eliminar.php?id=<?= $giro['id'] ?>&tipo=logico" class="btn btn-sm btn-secondary" onclick="return confirm('¿Marcar como Eliminado?')">🗑️ Ocultar</a>
                            |
                            <a href="controllers/eliminar.php?id=<?= $giro['id'] ?>&tipo=fisico" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar completamente de Sistema?')">❌ Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Crear -->
        <div id="modalCrear" class="modal">
            <div class="modal-content">
                <span class="close" id="btnCerrarModal">&times;</span>
                <h2>Agregar Producto</h2>
                <form id="formCrear" method="POST" action="controllers/crear.php">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" min="0" step="1"/>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </form>
            </div>
        </div>

        <!-- Modal Editar -->
        <div id="modalEditar" class="modal">
            <div class="modal-content">
                <span class="close" id="btnCerrarEditar">&times;</span>
                <h2>Editar giro</h2>
                <form id="formEditar" method="POST" action="controllers/editar.php">
                    <input type="hidden" id="editar_id" name="id" />
                    <div class="mb-3">
                        <label for="editar_nombre" class="form-label">Nombre:</label>
                        <input type="text" id="editar_nombre" name="nombre" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="editar_descripcion" class="form-label">Descripción:</label>
                        <textarea id="editar_descripcion" name="descripcion" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editar_cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="editar_cantidad" name="cantidad" class="form-control" min="0" step="1"/>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Eliminados -->
    <div id="eliminados" class="seccion">
        <h3>Productos Eliminados</h3>
        <table class="table table-bordered table-warning">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eliminados as $elim): ?>
                    <tr>
                        <td><?= $elim['id'] ?></td>
                        <td><?= htmlspecialchars($elim['nombre']) ?></td>
                        <td><?= htmlspecialchars($elim['descripcion']) ?></td>
                        <td><?= $elim['cantidad'] ?></td>
                        <td><?= $elim['Fecha_creacion'] ?></td>
                        <td>
                            <button
                                class="btn btn-sm btn-success btnRestaurar"
                                data-id="<?= $elim['id'] ?>"
                                data-nombre="<?= htmlspecialchars($elim['nombre'], ENT_QUOTES) ?>"
                                data-descripcion="<?= htmlspecialchars($elim['descripcion'], ENT_QUOTES) ?>"
                                data-cantidad="<?= htmlspecialchars( $elim['cantidad'], ENT_QUOTES) ?>"
                            >
                                🔄 Restaurar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Restaurar -->
        <div id="modalRestaurar" class="modal">
            <div class="modal-content">
                <span class="close" id="btnCerrarRestaurar">&times;</span>
                <h2>Confirmar Restauración</h2>
                <p>¿Seguro que deseas restaurar este producto?</p>
                <p><strong>Nombre:</strong> <span id="restaurar_nombre"></span></p>
                <p><strong>Descripción:</strong> <span id="restaurar_descripcion"></span></p>
                <p><strong>Cantidad:</strong> <span id="restaurar_cantidad"></span></p>
                <form id="formRestaurar" method="POST" action="controllers/restaurar.php">
                    <input type="hidden" name="id" id="restaurar_id" />
                    <button type="submit" class="btn btn-success">Restaurar</button>
                    <button type="button" class="btn btn-secondary" id="btnCancelarRestaurar">Cancelar</button>
                </form>
            </div>
        </div>
    </div>


    <!-- Registros -->
    <div id="registros" class="seccion">
        <h3>Registros de movimientos en Sistema</h3>
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Nombre</th><th>ID Producto</th><th>Usuario</th><th>Fecha Borrado</th><th>Tipo Eliminación</th></tr></thead>
            <tbody>
                <?php foreach ($registros as $reg): ?>
                    <tr>
                        <td><?= $reg['ID'] ?></td>
                        <td><?= htmlspecialchars($reg['Pnombre']) ?></td>
                        <td><?= $reg['PID'] ?></td>
                        <td><?= $reg['usuario'] ?></td>
                        <td><?= $reg['F_borrado'] ?></td>
                        <td><?= $reg['Etipo'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Cambiar secciones con tabs
    const tabs = document.querySelectorAll("#menuTabs button");
    const secciones = document.querySelectorAll(".seccion");
    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            const destino = tab.getAttribute("data-target");
            secciones.forEach(sec => {
                sec.classList.remove("activa");
                if (sec.id === destino) sec.classList.add("activa");
            });
        });
    });

    // Modal crear
    const btnAbrirModal = document.getElementById('btnAbrirModal');
    const modalCrear = document.getElementById('modalCrear');
    const btnCerrarModal = document.getElementById('btnCerrarModal');
    btnAbrirModal.onclick = () => modalCrear.style.display = 'block';
    btnCerrarModal.onclick = () => modalCrear.style.display = 'none';

    // Modal editar
    const modalEditar = document.getElementById('modalEditar');
    const btnCerrarEditar = document.getElementById('btnCerrarEditar');
    const editarId = document.getElementById('editar_id');
    const editarNombre = document.getElementById('editar_nombre');
    const editarDescripcion = document.getElementById('editar_descripcion');
    const editarCantidad = document.getElementById('editar_cantidad');

    document.querySelectorAll('.btnEditar').forEach(btn => {
        btn.addEventListener('click', () => {
            editarId.value = btn.dataset.id;
            editarNombre.value = btn.dataset.nombre;
            editarDescripcion.value = btn.dataset.descripcion;
            editarCantidad.value = btn.dataset.cantidad;
            modalEditar.style.display = 'block';
        });
    });

    btnCerrarEditar.onclick = () => modalEditar.style.display = 'none';

    window.onclick = e => {
        if (e.target == modalCrear) modalCrear.style.display = 'none';
        if (e.target == modalEditar) modalEditar.style.display = 'none';
    };


    // Modal restaurar
    const modalRestaurar = document.getElementById('modalRestaurar');
    const btnCerrarRestaurar = document.getElementById('btnCerrarRestaurar');
    const btnCancelarRestaurar = document.getElementById('btnCancelarRestaurar');

    const restaurarId = document.getElementById('restaurar_id');
    const restaurarNombre = document.getElementById('restaurar_nombre');
    const restaurarDescripcion = document.getElementById('restaurar_descripcion');
    const restaurarCantidad = document.getElementById('restaurar_cantidad');

    document.querySelectorAll('.btnRestaurar').forEach(btn => {
        btn.addEventListener('click', () => {
            restaurarId.value = btn.dataset.id;
            restaurarNombre.textContent = btn.dataset.nombre;
            restaurarDescripcion.textContent = btn.dataset.descripcion;
            restaurarCantidad.textContent = btn.dataset.cantidad;
            modalRestaurar.style.display = 'block';
        });
    });

    btnCerrarRestaurar.onclick = () => modalRestaurar.style.display = 'none';
    btnCancelarRestaurar.onclick = () => modalRestaurar.style.display = 'none';

    window.onclick = e => {
        if (e.target == modalRestaurar) modalRestaurar.style.display = 'none';
    };

    // Validación de campos solo números  
    const cantidadInput = document.getElementById('cantidad');
    cantidadInput.addEventListener('input', () => {
        cantidadInput.value = cantidadInput.value.replace(/[^0-9]/g, '');
    });

</script>

</body>
</html>