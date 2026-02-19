<?php $titulo = 'Subir Documento'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-upload"></i> Subir Documento</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Subir Documento</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Cargar Nuevo Documento</h3>
                        </div>
                        <form action="<?php echo URL_BASE; ?>documento/subir" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                
                                <?php if (isset($errors['general'])): ?>
                                <div class="alert alert-danger">
                                    <?php echo $errors['general']; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="titulo">Título del Documento <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" id="titulo" 
                                           class="form-control <?php echo isset($errors['titulo']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo $data['titulo'] ?? ''; ?>" 
                                           placeholder="Ej: Informe Mensual Enero 2026" required>
                                    <?php if (isset($errors['titulo'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['titulo']; ?></div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">
                                        Si subes varios archivos, este título se usará como base (ejemplo: "Informe (1)", "Informe (2)").
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="categoria_id">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" 
                                            class="form-control <?php echo isset($errors['categoria_id']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Seleccione una categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id']; ?>" 
                                                <?php echo (isset($data['categoria_id']) && $data['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['categoria_id'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['categoria_id']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3"
                                              placeholder="Descripción breve del documento (opcional)"><?php echo $data['descripcion'] ?? ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="archivo">Archivos <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" name="archivo[]" id="archivo" 
                                               class="custom-file-input <?php echo isset($errors['archivo']) ? 'is-invalid' : ''; ?>" 
                                               multiple
                                               required>
                                        <label class="custom-file-label" for="archivo">Seleccionar archivo(s)...</label>
                                        <?php if (isset($errors['archivo'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['archivo']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">
                                        <strong>Máximo por envío:</strong> 5 archivos<br>
                                        <strong>Tamaño máximo:</strong> <?php echo number_format(MAX_FILE_SIZE / 1048576, 2); ?> MB<br>
                                        <strong>Extensiones permitidas:</strong> 
                                        <?php foreach (ALLOWED_EXTENSIONS as $ext): ?>
                                            <span class="badge badge-info">.<?php echo $ext; ?></span>
                                        <?php endforeach; ?>
                                    </small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="icon fas fa-info-circle"></i>
                                    <strong>Nota:</strong> El documento quedará en estado "Pendiente" hasta que sea validado por un administrador o validador.
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-upload"></i> Subir Documento
                                </button>
                                <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Mostrar archivo(s) seleccionado(s)
$('#archivo').on('change', function() {
    var totalArchivos = this.files.length;
    var texto = 'Seleccionar archivo(s)...';

    if (totalArchivos === 1) {
        texto = this.files[0].name;
    } else if (totalArchivos > 1) {
        texto = totalArchivos + ' archivos seleccionados';
    }

    $(this).next('.custom-file-label').html(texto);
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
