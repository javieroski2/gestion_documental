<?php $titulo = 'Gestión de Categorías'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tags"></i> Gestión de Categorías</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Categorías</li>
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
                    <h3 class="card-title">Categorías de Documentos</h3>
                    <div class="card-tools">
                        <a href="<?php echo URL_BASE; ?>categoria/crear" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Categoría
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th width="100">Estado</th>
                                <th width="120">Fecha Creación</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay categorías registradas</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?php echo $categoria['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($categoria['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($categoria['descripcion'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php if ($categoria['estado'] == 1): ?>
                                        <span class="badge badge-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($categoria['fecha_creacion'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo URL_BASE; ?>categoria/editar/<?php echo $categoria['id']; ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="eliminarCategoria(<?php echo $categoria['id']; ?>)" 
                                                class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
function eliminarCategoria(id) {
    if (confirm('¿Está seguro de eliminar esta categoría? Esta acción no se puede deshacer.')) {
        fetch('<?php echo URL_BASE; ?>categoria/eliminar/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Categoría eliminada correctamente');
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar categoría');
            }
        })
        .catch(error => {
            alert('Error de conexión');
            console.error(error);
        });
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
