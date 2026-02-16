<?php $titulo = 'Gestión de Usuarios'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php $flash = $this->getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="icon fas fa-<?php echo $flash['type'] == 'success' ? 'check' : 'ban'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Usuarios Registrados</h3>
                    <div class="card-tools">
                        <a href="<?php echo URL_BASE; ?>usuario/crear" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tablaUsuarios" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                                <th width="100">Estado</th>
                                <th width="120">Fecha Registro</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay usuarios registrados</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                    <?php if (!empty($usuario['apellidos'])): ?>
                                        <?php echo htmlspecialchars($usuario['apellidos']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <?php 
                                    $badges = [
                                        1 => 'badge-danger',
                                        2 => 'badge-warning',
                                        3 => 'badge-info',
                                        4 => 'badge-success'
                                    ];
                                    $badge = $badges[$usuario['rol_id']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>">
                                        <?php echo htmlspecialchars($usuario['rol_nombre']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['telefono'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php if ($usuario['estado'] == 1): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo URL_BASE; ?>usuario/editar/<?php echo $usuario['id']; ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)" 
                                                class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function eliminarUsuario(id) {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        fetch('<?php echo URL_BASE; ?>usuario/eliminar/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario eliminado correctamente');
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar usuario');
            }
        })
        .catch(error => {
            alert('Error de conexión');
            console.error(error);
        });
    }
}
</script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
