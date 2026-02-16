<?php $titulo = 'Auditoría del Sistema'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-history"></i> Auditoría del Sistema</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Auditoría</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Filtros -->
            <div class="card card-primary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo URL_BASE; ?>auditoria">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_desde">Fecha Desde</label>
                                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                                           value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_hasta">Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                                           value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="accion">Tipo de Acción</label>
                                    <select name="accion" id="accion" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="login">Login</option>
                                        <option value="logout">Logout</option>
                                        <option value="crear">Crear</option>
                                        <option value="editar">Editar</option>
                                        <option value="eliminar">Eliminar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Auditoría -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registro de Actividades</h3>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" onclick="exportarAuditoria()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="120">Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla</th>
                                <th>Registro ID</th>
                                <th width="120">IP</th>
                                <th width="80">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registros)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No hay registros de auditoría
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?php echo $registro['id']; ?></td>
                                <td>
                                    <small>
                                        <?php echo date('d/m/Y', strtotime($registro['fecha'])); ?><br>
                                        <?php echo date('H:i:s', strtotime($registro['fecha'])); ?>
                                    </small>
                                </td>
                                <td><?php echo htmlspecialchars($registro['usuario_nombre'] ?? 'Sistema'); ?></td>
                                <td>
                                    <?php 
                                    $badges = [
                                        'login' => 'badge-success',
                                        'logout' => 'badge-secondary',
                                        'crear' => 'badge-primary',
                                        'editar' => 'badge-warning',
                                        'eliminar' => 'badge-danger'
                                    ];
                                    $badge = $badges[strtolower($registro['accion'])] ?? 'badge-info';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>">
                                        <?php echo htmlspecialchars($registro['accion']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($registro['tabla'] ?? '-'); ?></td>
                                <td class="text-center"><?php echo $registro['registro_id'] ?? '-'; ?></td>
                                <td><small><?php echo htmlspecialchars($registro['ip'] ?? '-'); ?></small></td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-info" onclick="verDetalles(<?php echo $registro['id']; ?>)"
                                            title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Mostrando <?php echo count($registros); ?> registros
                    </small>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $stats['total'] ?? 0; ?></h3>
                            <p>Total Actividades</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $stats['hoy'] ?? 0; ?></h3>
                            <p>Actividades Hoy</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['semana'] ?? 0; ?></h3>
                            <p>Esta Semana</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $stats['usuarios_activos'] ?? 1; ?></h3>
                            <p>Usuarios Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="modalDetalles">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title"><i class="fas fa-info-circle"></i> Detalles de la Actividad</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detallesContent">
                <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalles(id) {
    $('#modalDetalles').modal('show');
    $('#detallesContent').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando detalles...</p>');
    
    fetch('<?php echo URL_BASE; ?>auditoria/detalles/' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<table class="table table-sm">';
                html += '<tr><th width="30%">Usuario:</th><td>' + (data.registro.usuario_nombre || '-') + '</td></tr>';
                html += '<tr><th>Acción:</th><td>' + data.registro.accion + '</td></tr>';
                html += '<tr><th>Fecha/Hora:</th><td>' + data.registro.fecha + '</td></tr>';
                html += '<tr><th>IP:</th><td>' + (data.registro.ip || '-') + '</td></tr>';
                html += '<tr><th>User Agent:</th><td><small>' + (data.registro.user_agent || '-') + '</small></td></tr>';
                if (data.registro.datos_anteriores) {
                    html += '<tr><th>Datos Anteriores:</th><td><pre>' + data.registro.datos_anteriores + '</pre></td></tr>';
                }
                if (data.registro.datos_nuevos) {
                    html += '<tr><th>Datos Nuevos:</th><td><pre>' + data.registro.datos_nuevos + '</pre></td></tr>';
                }
                html += '</table>';
                $('#detallesContent').html(html);
            } else {
                $('#detallesContent').html('<div class="alert alert-danger">Error al cargar detalles</div>');
            }
        })
        .catch(error => {
            $('#detallesContent').html('<div class="alert alert-danger">Error de conexión</div>');
        });
}

function exportarAuditoria() {
    alert('Función de exportación en desarrollo.\n\nPronto podrás exportar el registro de auditoría a Excel.');
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
