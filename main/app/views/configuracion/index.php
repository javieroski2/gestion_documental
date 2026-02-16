<?php $titulo = 'Configuración del Sistema'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cogs"></i> Configuración del Sistema</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php $flash = $this->getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="icon fas fa-<?php echo $flash['type'] == 'success' ? 'check' : 'ban'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Configuración General -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo URL_BASE; ?>configuracion/guardar" method="POST">
                                <div class="form-group">
                                    <label for="nombre_sistema">Nombre del Sistema</label>
                                    <input type="text" name="nombre_sistema" id="nombre_sistema" 
                                           class="form-control" value="<?php echo NOMBRE_SISTEMA; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_contacto">Email de Contacto</label>
                                    <input type="email" name="email_contacto" id="email_contacto" 
                                           class="form-control" value="admin@sistema.com">
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono_contacto">Teléfono de Contacto</label>
                                    <input type="text" name="telefono_contacto" id="telefono_contacto" 
                                           class="form-control" value="+56 9 1234 5678">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Archivos -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-upload"></i> Configuración de Archivos</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tamaño Máximo de Archivo</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo number_format(MAX_FILE_SIZE / 1048576, 2); ?> MB" disabled>
                                <small class="form-text text-muted">Configurado en config.php</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Extensiones Permitidas</label>
                                <div class="border p-3 bg-light">
                                    <?php foreach (ALLOWED_EXTENSIONS as $ext): ?>
                                        <span class="badge badge-info mr-1">.<?php echo $ext; ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <small class="form-text text-muted">Configurado en config.php</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Carpeta de Uploads</label>
                                <input type="text" class="form-control" 
                                       value="public/uploads/documentos/" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Información del Sistema -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info"></i> Información del Sistema</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Versión del Sistema:</th>
                                    <td>1.0.0</td>
                                </tr>
                                <tr>
                                    <th>PHP Version:</th>
                                    <td><?php echo phpversion(); ?></td>
                                </tr>
                                <tr>
                                    <th>Base de Datos:</th>
                                    <td><?php echo DB_NAME; ?></td>
                                </tr>
                                <tr>
                                    <th>Servidor:</th>
                                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <th>Modo Desarrollo:</th>
                                    <td>
                                        <?php if (MODO_DESARROLLO): ?>
                                            <span class="badge badge-warning">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Producción</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del Sistema -->
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="60%">Total de Usuarios:</th>
                                    <td><strong><?php echo $stats['usuarios'] ?? 1; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Total de Categorías:</th>
                                    <td><strong><?php echo $stats['categorias'] ?? 6; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Total de Documentos:</th>
                                    <td><strong><?php echo $stats['documentos'] ?? 0; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Documentos Pendientes:</th>
                                    <td><strong class="text-warning"><?php echo $stats['pendientes'] ?? 0; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Espacio Usado:</th>
                                    <td><strong><?php echo $stats['espacio'] ?? '0 MB'; ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones del Sistema -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Acciones del Sistema</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="<?php echo URL_BASE; ?>configuracion/limpiarCache" class="btn btn-warning btn-block" 
                               onclick="return confirm('¿Limpiar caché del sistema?')">
                                <i class="fas fa-broom"></i> Limpiar Caché
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo URL_BASE; ?>configuracion/backup" class="btn btn-info btn-block">
                                <i class="fas fa-database"></i> Backup Base de Datos
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?php echo URL_BASE; ?>configuracion/optimizar" class="btn btn-success btn-block"
                               onclick="return confirm('¿Optimizar sistema? Esto puede tardar unos segundos.')">
                                <i class="fas fa-sync"></i> Optimizar Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
