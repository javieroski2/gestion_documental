<?php $titulo = 'Reporte por Estado'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-pie"></i> Documentos por Estado</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>reporte">Reportes</a></li>
                        <li class="breadcrumb-item active">Por Estado</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pendientes</span>
                            <span class="info-box-number"><?php echo $stats['pendiente']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Aprobados</span>
                            <span class="info-box-number"><?php echo $stats['aprobado']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Rechazados</span>
                            <span class="info-box-number"><?php echo $stats['rechazado']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Gráfico de Distribución</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>reporte/exportar/estados" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartEstados" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumen</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td><i class="fas fa-circle text-warning"></i> Pendientes</td>
                                    <td class="text-right"><strong><?php echo $stats['pendiente']; ?></strong></td>
                                    <td class="text-right">
                                        <?php 
                                        $total = $stats['pendiente'] + $stats['aprobado'] + $stats['rechazado'];
                                        echo $total > 0 ? round(($stats['pendiente'] / $total) * 100, 1) : 0;
                                        ?>%
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-circle text-success"></i> Aprobados</td>
                                    <td class="text-right"><strong><?php echo $stats['aprobado']; ?></strong></td>
                                    <td class="text-right">
                                        <?php echo $total > 0 ? round(($stats['aprobado'] / $total) * 100, 1) : 0; ?>%
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-circle text-danger"></i> Rechazados</td>
                                    <td class="text-right"><strong><?php echo $stats['rechazado']; ?></strong></td>
                                    <td class="text-right">
                                        <?php echo $total > 0 ? round(($stats['rechazado'] / $total) * 100, 1) : 0; ?>%
                                    </td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>TOTAL</td>
                                    <td class="text-right"><?php echo $total; ?></td>
                                    <td class="text-right">100%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const ctx = document.getElementById('chartEstados').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Pendientes', 'Aprobados', 'Rechazados'],
        datasets: [{
            data: [<?php echo $stats['pendiente']; ?>, <?php echo $stats['aprobado']; ?>, <?php echo $stats['rechazado']; ?>],
            backgroundColor: ['#ffc107', '#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
