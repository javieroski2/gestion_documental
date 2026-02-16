<?php $titulo = 'Dashboard - Gestor'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard - Gestor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $stats['documentos'] ?? 0; ?></h3>
                            <p>Mis Documentos</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-document"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['pendientes'] ?? 0; ?></h3>
                            <p>En Revisión</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clock"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $stats['aprobados'] ?? 0; ?></h3>
                            <p>Aprobados</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-checkmark"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $stats['rechazados'] ?? 0; ?></h3>
                            <p>Rechazados</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-close"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-8">
                    <!-- Mis Documentos -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Mis Documentos Recientes</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="btn btn-tool btn-sm">
                                    <i class="fas fa-arrow-right"></i> Ver Todos
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($stats['documentos_recientes'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            No has subido documentos aún
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($stats['documentos_recientes'] as $doc): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                                            <td>
                                                <?php 
                                                $badges = [
                                                    'pendiente' => 'badge-warning',
                                                    'aprobado' => 'badge-success',
                                                    'rechazado' => 'badge-danger'
                                                ];
                                                $badge = $badges[$doc['estado_validacion']] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?php echo $badge; ?>">
                                                    <?php echo ucfirst($doc['estado_validacion']); ?>
                                                </span>
                                            </td>
                                            <td><small><?php echo date('d/m/Y', strtotime($doc['fecha_creacion'])); ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Right col -->
                <section class="col-lg-4">
                    <!-- Acción Rápida -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-upload"></i> Acción Rápida</h3>
                        </div>
                        <div class="card-body">
                            <p>Sube tus documentos para que sean validados</p>
                            <a href="<?php echo URL_BASE; ?>documento/subir" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-cloud-upload-alt"></i> Subir Documento
                            </a>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Tu Rol</h3>
                        </div>
                        <div class="card-body">
                            <h5>Gestor Documental</h5>
                            <p>Puedes:</p>
                            <ul class="mb-0">
                                <li>Subir documentos</li>
                                <li>Ver tus documentos</li>
                                <li>Ver documentos aprobados</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
