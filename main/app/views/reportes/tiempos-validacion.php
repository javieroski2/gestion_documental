<?php $titulo = 'Tiempos de Validación'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clock"></i> Tiempos de Validación</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>reporte">Reportes</a></li>
                        <li class="breadcrumb-item active">Tiempos</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Análisis de Tiempos de Validación</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>reporte/exportar/tiempos" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Promedio de Días</span>
                                            <span class="info-box-number"><?php echo $promedio; ?> días</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Validados</span>
                                            <span class="info-box-number"><?php echo count($documentos); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Documento</th>
                                        <th>Usuario</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Fecha Validación</th>
                                        <th>Días en Proceso</th>
                                        <th>Validador</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td><?php echo $doc['id']; ?></td>
                                        <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                                        <td><small><?php echo htmlspecialchars($doc['usuario']); ?></small></td>
                                        <td><?php echo date('d/m/Y', strtotime($doc['fecha_ingreso'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_validacion'])); ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo $doc['dias_proceso'] > $promedio ? 'danger' : 'success'; ?>">
                                                <?php echo $doc['dias_proceso']; ?> días
                                            </span>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($doc['validador'] ?? 'N/A'); ?></small></td>
                                        <td>
                                            <span class="badge badge-<?php echo $doc['estado_validacion'] == 'aprobado' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($doc['estado_validacion']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
