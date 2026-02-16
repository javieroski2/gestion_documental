<?php $titulo = 'Reporte por Mes'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-alt"></i> Documentos por Mes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>reporte">Reportes</a></li>
                        <li class="breadcrumb-item active">Por Mes</li>
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
                            <h3 class="card-title">Últimos 12 Meses</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>reporte/exportar/general" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel"></i> Exportar Todo
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartMeses" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detalle Mensual</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pendientes</th>
                                        <th class="text-center">Aprobados</th>
                                        <th class="text-center">Rechazados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos as $dato): ?>
                                    <tr>
                                        <td><?php echo date('F Y', strtotime($dato['mes'] . '-01')); ?></td>
                                        <td class="text-center"><strong><?php echo $dato['total']; ?></strong></td>
                                        <td class="text-center"><span class="badge badge-warning"><?php echo $dato['pendientes']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-success"><?php echo $dato['aprobados']; ?></span></td>
                                        <td class="text-center"><span class="badge badge-danger"><?php echo $dato['rechazados']; ?></span></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const ctx = document.getElementById('chartMeses').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', array_map(function($d) { return "'" . date('M Y', strtotime($d['mes'] . '-01')) . "'"; }, array_reverse($datos))); ?>],
        datasets: [
            {
                label: 'Pendientes',
                data: [<?php echo implode(',', array_map(function($d) { return $d['pendientes']; }, array_reverse($datos))); ?>],
                backgroundColor: '#ffc107'
            },
            {
                label: 'Aprobados',
                data: [<?php echo implode(',', array_map(function($d) { return $d['aprobados']; }, array_reverse($datos))); ?>],
                backgroundColor: '#28a745'
            },
            {
                label: 'Rechazados',
                data: [<?php echo implode(',', array_map(function($d) { return $d['rechazados']; }, array_reverse($datos))); ?>],
                backgroundColor: '#dc3545'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
        }
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
