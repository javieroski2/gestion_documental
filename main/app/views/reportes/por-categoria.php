<?php $titulo = 'Reporte por Categoría'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tags"></i> Documentos por Categoría</h1>
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
                            <h3 class="card-title">Distribución por Categoría</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>reporte/exportar/categorias" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pendientes</th>
                                        <th class="text-center">Aprobados</th>
                                        <th class="text-center">Rechazados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td><i class="fas fa-tag"></i> <?php echo htmlspecialchars($cat['categoria']); ?></td>
                                        <td class="text-center"><strong><?php echo $cat['total']; ?></strong></td>
                                        <td class="text-center"><span class="badge badge-warning"><?php echo $cat['pendientes']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-success"><?php echo $cat['aprobados']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-danger"><?php echo $cat['rechazados']; ?></span></td>
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
