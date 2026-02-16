<?php $titulo = 'Reporte por Usuario'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Documentos por Usuario</h1>
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
                            <h3 class="card-title">Ranking de Usuarios</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>reporte/exportar/usuarios" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Usuario</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pendientes</th>
                                        <th class="text-center">Aprobados</th>
                                        <th class="text-center">Rechazados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $pos = 1; foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td><?php echo $pos++; ?></td>
                                        <td><?php echo htmlspecialchars($u['nombre'] . ' ' . ($u['apellidos'] ?? '')); ?></td>
                                        <td class="text-center"><strong><?php echo $u['total_documentos']; ?></strong></td>
                                        <td class="text-center"><span class="badge badge-warning"><?php echo $u['pendientes']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-success"><?php echo $u['aprobados']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-danger"><?php echo $u['rechazados']; ?></span></td>
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
