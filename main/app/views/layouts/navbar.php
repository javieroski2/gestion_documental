<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo URL_BASE; ?>dashboard" class="nav-link">Inicio</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <?php if (!empty($_SESSION['foto'])): ?>
                    <img src="<?php echo URL_BASE; ?>public/uploads/usuarios/<?php echo $_SESSION['foto']; ?>" 
                         class="img-circle" alt="<?php echo $_SESSION['nombre']; ?>" 
                         style="width: 25px; height: 25px; object-fit: cover;">
                <?php else: ?>
                    <i class="far fa-user"></i>
                <?php endif; ?>
                <span class="d-none d-md-inline ml-2">
                    <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
                    <?php if (!empty($_SESSION['apellidos'])): ?>
                        <?php echo $_SESSION['apellidos']; ?>
                    <?php endif; ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    <?php if (!empty($_SESSION['foto'])): ?>
                        <img src="<?php echo URL_BASE; ?>public/uploads/usuarios/<?php echo $_SESSION['foto']; ?>" 
                             class="img-circle mb-2" alt="<?php echo $_SESSION['nombre']; ?>" 
                             style="width: 60px; height: 60px; object-fit: cover;"><br>
                    <?php endif; ?>
                    <strong>
                        <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
                        <?php if (!empty($_SESSION['apellidos'])): ?>
                            <?php echo $_SESSION['apellidos']; ?>
                        <?php endif; ?>
                    </strong><br>
                    <small class="text-muted"><?php echo $_SESSION['rol_nombre'] ?? ''; ?></small>
                    <?php if (!empty($_SESSION['cargo'])): ?>
                        <br><small class="text-info"><?php echo $_SESSION['cargo']; ?></small>
                    <?php endif; ?>
                </span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Mi Perfil
                </a>
                <a href="<?php echo URL_BASE; ?>configuracion" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Configuración
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo URL_BASE; ?>auth/logout" class="dropdown-item dropdown-footer text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </li>
        
        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
