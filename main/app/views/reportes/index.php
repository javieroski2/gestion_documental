<?php $titulo = 'Reportes'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-bar"></i> Reportes del Sistema</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Reportes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Reportes disponibles -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Por Estado</h3>
                        </div>
                        <div class="card-body">
                            <p>Distribución de documentos por estado de validación</p>
                            <ul>
                                <li>Pendientes</li>
                                <li>Aprobados</li>
                                <li>Rechazados</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/porEstado" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Por Mes</h3>
                        </div>
                        <div class="card-body">
                            <p>Documentos ingresados por mes</p>
                            <ul>
                                <li>Últimos 12 meses</li>
                                <li>Tendencia mensual</li>
                                <li>Comparativa</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/porMes" class="btn btn-success btn-block">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Tiempos de Validación</h3>
                        </div>
                        <div class="card-body">
                            <p>Análisis de tiempos de proceso</p>
                            <ul>
                                <li>Días en proceso</li>
                                <li>Promedio de validación</li>
                                <li>Documentos más lentos</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/tiemposValidacion" class="btn btn-warning btn-block">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users"></i> Por Usuario</h3>
                        </div>
                        <div class="card-body">
                            <p>Actividad de usuarios en el sistema</p>
                            <ul>
                                <li>Documentos por usuario</li>
                                <li>Usuarios más activos</li>
                                <li>Ranking</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/porUsuario" class="btn btn-info btn-block">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tags"></i> Por Categoría</h3>
                        </div>
                        <div class="card-body">
                            <p>Distribución por categorías</p>
                            <ul>
                                <li>Documentos por categoría</li>
                                <li>Categorías más usadas</li>
                                <li>Estadísticas</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/porCategoria" class="btn btn-danger btn-block">
                                <i class="fas fa-eye"></i> Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-export"></i> Exportar Datos</h3>
                        </div>
                        <div class="card-body">
                            <p>Exportar reportes a Excel/CSV</p>
                            <ul>
                                <li>Formato CSV</li>
                                <li>Compatible con Excel</li>
                                <li>Datos completos</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo URL_BASE; ?>reporte/exportar/general" class="btn btn-secondary btn-block">
                                <i class="fas fa-download"></i> Exportar Todo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
