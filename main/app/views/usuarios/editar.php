<?php $titulo = 'Editar Usuario'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>usuario">Usuarios</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Editar Información del Usuario</h3>
                        </div>
                        <form action="<?php echo URL_BASE; ?>usuario/editar/<?php echo $usuario['id']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                    <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['nombre']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" id="apellidos" class="form-control <?php echo isset($errors['apellidos']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($usuario['apellidos'] ?? ''); ?>" required>
                                    <?php if (isset($errors['apellidos'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['apellidos']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="cargo">Cargo/Puesto</label>
                                    <input type="text" name="cargo" id="cargo" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['cargo'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="foto">Foto de Perfil</label>
                                    <?php if (!empty($usuario['foto'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo URL_BASE; ?>public/uploads/usuarios/<?php echo $usuario['foto']; ?>" 
                                             alt="Foto actual" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <?php endif; ?>
                                    <div class="custom-file">
                                        <input type="file" name="foto" id="foto" class="custom-file-input" accept="image/*">
                                        <label class="custom-file-label" for="foto">Cambiar foto...</label>
                                    </div>
                                    <small class="form-text text-muted">Formatos: JPG, PNG. Tamaño máximo: 2 MB. Dejar vacío para mantener la foto actual.</small>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="password">Nueva Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           minlength="6">
                                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                                    <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="rol_id">Rol <span class="text-danger">*</span></label>
                                    <select name="rol_id" id="rol_id" class="form-control <?php echo isset($errors['rol_id']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol['id']; ?>" <?php echo ($usuario['rol_id'] == $rol['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['rol_id'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['rol_id']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="estado" class="form-control">
                                        <option value="1" <?php echo ($usuario['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ($usuario['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Actualizar Usuario
                                </button>
                                <a href="<?php echo URL_BASE; ?>usuario" class="btn btn-secondary">
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
// Mostrar nombre del archivo seleccionado
$('#foto').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
