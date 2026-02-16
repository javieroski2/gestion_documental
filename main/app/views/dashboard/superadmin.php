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
                    <h1 class="m-0">Dashboard - Super Administrador</h1>
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
                            <h3><?php echo $stats['usuarios'] ?? 0; ?></h3>
                            <p>Total Usuarios</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>usuario" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $stats['documentos'] ?? 0; ?></h3>
                            <p>Documentos</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-document"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['pendientes'] ?? 0; ?></h3>
                            <p>Pendientes</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clock"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento/pendientes" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $stats['aprobados'] ?? 0; ?></h3>
                            <p>Aprobados</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-checkmark-circled"></i>
                        </div>
                        <a href="<?php echo URL_BASE; ?>documento" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-7">
                    <!-- Documentos Recientes -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Documentos Recientes</h3>
                            <div class="card-tools">
                                <a href="<?php echo URL_BASE; ?>documento" class="btn btn-tool btn-sm">
                                    <i class="fas fa-arrow-right"></i> Ver Todos
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($stats['documentos_recientes'])): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            No hay documentos recientes
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($stats['documentos_recientes'] as $doc): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo URL_BASE; ?>documento">
                                                    <?php echo htmlspecialchars($doc['titulo']); ?>
                                                </a>
                                            </td>
                                            <td><small><?php echo htmlspecialchars($doc['usuario']); ?></small></td>
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
                <section class="col-lg-5">
                    <!-- Gráfico Documentos por Mes -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Documentos por Mes</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartDocumentosMes" height="200"></canvas>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Gráfico de Documentos por Mes
const ctx = document.getElementById('chartDocumentosMes');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php 
                if (!empty($stats['documentos_por_mes'])) {
                    echo implode(',', array_map(function($d) { 
                        return "'" . $d['mes'] . "'"; 
                    }, $stats['documentos_por_mes']));
                }
            ?>],
            datasets: [{
                label: 'Documentos',
                data: [<?php 
                    if (!empty($stats['documentos_por_mes'])) {
                        echo implode(',', array_column($stats['documentos_por_mes'], 'total'));
                    }
                ?>],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
