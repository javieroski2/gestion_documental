<?php $titulo = 'Documentos Pendientes'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clock"></i> Documentos Pendientes de Validación</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Pendientes</li>
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

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Documentos que Requieren Validación</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning"><?php echo count($documentos); ?> pendientes</span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Usuario</th>
                                <th>Archivo</th>
                                <th width="120">Fecha Subida</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documentos)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                    ¡Excelente! No hay documentos pendientes de validación
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?php echo $doc['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($doc['titulo']); ?></strong>
                                    <?php if ($doc['descripcion']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($doc['descripcion']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($doc['categoria_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($doc['usuario_nombre']); ?></td>
                                <td>
                                    <code>.<?php echo $doc['extension']; ?></code>
                                    <small>(<?php echo number_format($doc['tamano'] / 1024, 2); ?> KB)</small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_creacion'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button onclick="verDocumento(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars($doc['titulo']); ?>', '<?php echo $doc['extension']; ?>', '<?php echo $doc['archivo']; ?>')" 
                                                class="btn btn-primary" title="Ver documento">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?php echo URL_BASE; ?>documento/descargar/<?php echo $doc['id']; ?>" 
                                           class="btn btn-info" title="Descargar" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button onclick="validarDocumento(<?php echo $doc['id']; ?>, 'aprobado')" 
                                                class="btn btn-success" title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="validarDocumento(<?php echo $doc['id']; ?>, 'rechazado')" 
                                                class="btn btn-danger" title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal de Validación -->
<div class="modal fade" id="modalValidar">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formValidar">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"><i class="fas fa-check-circle"></i> Validar Documento</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="estado" id="estadoValidacion">
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="4"
                                  placeholder="Comentarios sobre la validación (opcional)"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Esta acción notificará al usuario sobre el estado de su documento.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Confirmar Validación
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Documento -->
<div class="modal fade" id="modalVerDocumento" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">
                    <i class="fas fa-file-alt"></i> 
                    <span id="tituloDocumento"></span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="contenidoDocumento" style="min-height: 500px;">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Cargando documento...</p>
                </div>
            </div>
            <div class="modal-footer">
                <a id="btnDescargarModal" href="#" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let documentoIdActual = null;

// Ver documento en modal
function verDocumento(id, titulo, extension, archivo) {
    $('#tituloDocumento').text(titulo);
    $('#modalVerDocumento').modal('show');
    
    const rutaArchivo = '<?php echo URL_BASE; ?>uploads/documentos/' + archivo;
    $('#btnDescargarModal').attr('href', '<?php echo URL_BASE; ?>documento/descargar/' + id);
    
    let contenido = '';
    
    if (extension === 'pdf') {
        contenido = `
            <iframe src="${rutaArchivo}" 
                    style="width: 100%; height: 600px; border: none;"
                    type="application/pdf">
                <p>Tu navegador no puede mostrar PDFs. 
                   <a href="${rutaArchivo}" target="_blank">Haz click aquí para abrirlo</a>
                </p>
            </iframe>
        `;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension.toLowerCase())) {
        contenido = `
            <div class="text-center">
                <img src="${rutaArchivo}" 
                     alt="${titulo}" 
                     style="max-width: 100%; height: auto; max-height: 600px;">
            </div>
        `;
    } else {
        const iconos = {
            'doc': 'fa-file-word text-primary',
            'docx': 'fa-file-word text-primary',
            'xls': 'fa-file-excel text-success',
            'xlsx': 'fa-file-excel text-success'
        };
        const icono = iconos[extension.toLowerCase()] || 'fa-file text-muted';
        contenido = `
            <div class="text-center py-5">
                <i class="fas ${icono} fa-5x mb-4"></i>
                <h4>${titulo}</h4>
                <p class="text-muted">Archivo .${extension.toUpperCase()}</p>
                <p>Este tipo de archivo no se puede previsualizar.</p>
                <a href="${rutaArchivo}" class="btn btn-success btn-lg" download>
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
        `;
    }
    
    $('#contenidoDocumento').html(contenido);
}

function validarDocumento(id, estado) {
    documentoIdActual = id;
    $('#estadoValidacion').val(estado);
    $('#formValidar').attr('action', '<?php echo URL_BASE; ?>documento/validar/' + id);
    
    // Cambiar color del modal según el estado
    if (estado === 'aprobado') {
        $('.modal-header').removeClass('bg-warning bg-danger').addClass('bg-success');
        $('.modal-title').html('<i class="fas fa-check-circle"></i> Aprobar Documento');
    } else {
        $('.modal-header').removeClass('bg-warning bg-success').addClass('bg-danger');
        $('.modal-title').html('<i class="fas fa-times-circle"></i> Rechazar Documento');
    }
    
    $('#modalValidar').modal('show');
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
