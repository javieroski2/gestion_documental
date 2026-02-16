<?php $titulo = 'Mis Documentos'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-folder-open"></i> Mis Documentos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Mis Documentos</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Documentos que he Subido</h3>
                    <div class="card-tools">
                        <a href="<?php echo URL_BASE; ?>documento/subir" class="btn btn-success btn-sm">
                            <i class="fas fa-upload"></i> Subir Nuevo Documento
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Archivo</th>
                                <th width="100">Tamaño</th>
                                <th width="120">Estado</th>
                                <th width="120">Fecha Subida</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No has subido ningún documento<br>
                                    <a href="<?php echo URL_BASE; ?>documento/subir" class="btn btn-sm btn-success mt-2">
                                        <i class="fas fa-upload"></i> Subir tu Primer Documento
                                    </a>
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
                                    <?php if (!empty($doc['observaciones'])): ?>
                                    <br><div class="alert alert-<?php echo $doc['estado_validacion'] == 'rechazado' ? 'danger' : 'info'; ?> py-1 px-2 mt-1 mb-0" style="font-size: 0.85rem;">
                                        <i class="fas fa-comment"></i> <strong>Observaciones:</strong> <?php echo htmlspecialchars($doc['observaciones']); ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($doc['categoria_nombre']); ?></td>
                                <td><code>.<?php echo $doc['extension']; ?></code></td>
                                <td><?php echo number_format($doc['tamano'] / 1024, 2); ?> KB</td>
                                <td class="text-center">
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
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['fecha_creacion'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button onclick="verDocumento(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars($doc['titulo']); ?>', '<?php echo $doc['extension']; ?>', '<?php echo $doc['archivo']; ?>')" 
                                                class="btn btn-primary" title="Ver documento">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?php echo URL_BASE; ?>documento/descargar/<?php echo $doc['id']; ?>" 
                                           class="btn btn-info" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <?php if ($doc['estado_validacion'] == 'pendiente'): ?>
                                        <button onclick="eliminarDocumento(<?php echo $doc['id']; ?>)" 
                                                class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
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
// Ver documento en modal
function verDocumento(id, titulo, extension, archivo) {
    $('#tituloDocumento').text(titulo);
    $('#modalVerDocumento').modal('show');
    
    const rutaArchivo = '<?php echo URL_BASE; ?>uploads/documentos/' + archivo;
    $('#btnDescargarModal').attr('href', '<?php echo URL_BASE; ?>documento/descargar/' + id);
    
    let contenido = '';
    
    // Determinar cómo mostrar según la extensión
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
            'xlsx': 'fa-file-excel text-success',
            'ppt': 'fa-file-powerpoint text-danger',
            'pptx': 'fa-file-powerpoint text-danger',
            'txt': 'fa-file-alt text-secondary',
            'zip': 'fa-file-archive text-warning',
            'rar': 'fa-file-archive text-warning'
        };
        
        const icono = iconos[extension.toLowerCase()] || 'fa-file text-muted';
        
        contenido = `
            <div class="text-center py-5">
                <i class="fas ${icono} fa-5x mb-4"></i>
                <h4>${titulo}</h4>
                <p class="text-muted">Archivo .${extension.toUpperCase()}</p>
                <p>Este tipo de archivo no se puede previsualizar en el navegador.</p>
                <a href="${rutaArchivo}" class="btn btn-success btn-lg" download>
                    <i class="fas fa-download"></i> Descargar Archivo
                </a>
                <br><br>
                <small class="text-muted">
                    Descarga el archivo para abrirlo en la aplicación correspondiente
                </small>
            </div>
        `;
    }
    
    $('#contenidoDocumento').html(contenido);
}

// Eliminar documento
function eliminarDocumento(id) {
    if (confirm('¿Está seguro de eliminar este documento? Esta acción no se puede deshacer.')) {
        fetch('<?php echo URL_BASE; ?>documento/eliminar/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documento eliminado correctamente');
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar documento');
            }
        })
        .catch(error => {
            alert('Error de conexión');
            console.error(error);
        });
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
