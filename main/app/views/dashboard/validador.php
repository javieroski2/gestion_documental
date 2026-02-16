<?php $titulo = 'Dashboard - Validador'; ?>
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
                    <h1 class="m-0">Dashboard - Validador</h1>
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
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['pendientes'] ?? 0; ?></h3>
                            <p>Pendientes de Validar</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clock"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/pendientes" class="small-box-footer">
                            Validar Ahora <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $stats['aprobados'] ?? 0; ?></h3>
                            <p>Aprobados</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-checkmark"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $stats['rechazados'] ?? 0; ?></h3>
                            <p>Rechazados</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-close"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento" class="small-box-footer">
                            Ver Más <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <!-- Documentos Pendientes -->
                <section class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Documentos Pendientes de Validación</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>documento/pendientes" class="btn btn-warning btn-sm">
                                    <i class="fas fa-tasks"></i> Ver Todos los Pendientes
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Usuario</th>
                                        <th>Categoría</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hayPendientes = false;
                                    if (!empty($stats['documentos_recientes'])):
                                        foreach ($stats['documentos_recientes'] as $doc):
                                            if ($doc['estado_validacion'] == 'pendiente'):
                                                $hayPendientes = true;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($doc['titulo']); ?></td>
                                        <td><small><?php echo htmlspecialchars($doc['usuario']); ?></small></td>
                                        <td><small>General</small></td>
                                        <td><small><?php echo date('d/m/Y H:i', strtotime($doc['fecha_creacion'])); ?></small></td>
                                        <td>
                                            <a href="<?php echo URL_BASE; ?>documento/pendientes" class="btn btn-sm btn-warning">
                                                <i class="fas fa-check"></i> Validar
                                            </a>
                                        </td>
                                    </tr>
                                    <?php 
                                            endif;
                                        endforeach;
                                    endif;
                                    
                                    if (!$hayPendientes):
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-3x mb-2 text-success"></i><br>
                                            <strong>¡Excelente trabajo!</strong><br>
                                            No hay documentos pendientes de validación
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
