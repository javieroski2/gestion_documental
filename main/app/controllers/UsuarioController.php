<?php
/**
 * Usuario Controller
 * Gestión de usuarios del sistema
 */

class UsuarioController extends Controller {
    
    private $usuarioModel;
    
    public function __construct() {
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN]);
        $this->usuarioModel = $this->model('User');
    }
    
    /**
     * Listar usuarios
     */
    public function index() {
        $usuarios = $this->usuarioModel->getAllWithRole();
        $this->view('usuarios/index', ['usuarios' => $usuarios]);
    }
    
    /**
     * Crear usuario
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = $this->validarDatos($_POST, $_FILES);
            
            if (empty($errors)) {
                $data = [
                    'rol_id' => $_POST['rol_id'],
                    'nombre' => trim($_POST['nombre']),
                    'apellidos' => trim($_POST['apellidos']),
                    'cargo' => trim($_POST['cargo'] ?? ''),
                    'email' => trim($_POST['email']),
                    'password' => $_POST['password'],
                    'telefono' => trim($_POST['telefono'] ?? ''),
                    'estado' => $_POST['estado'] ?? 1
                ];
                
                // Procesar foto si se subió
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $foto = $this->subirFoto($_FILES['foto']);
                    if ($foto) {
                        $data['foto'] = $foto;
                    }
                }
                
                if ($this->usuarioModel->createUser($data)) {
                    // Registrar en auditoría
                    $auditoriaModel = $this->model('Auditoria');
                    $auditoriaModel->registrar(
                        'Creó usuario: ' . $data['nombre'] . ' ' . $data['apellidos'], 
                        'usuarios', 
                        null,
                        null,
                        json_encode($data)
                    );
                    
                    $this->setFlash('success', 'Usuario creado exitosamente');
                    $this->redirect('usuario');
                } else {
                    $this->setFlash('error', 'Error al crear usuario');
                }
            } else {
                $this->view('usuarios/crear', [
                    'errors' => $errors,
                    'data' => $_POST,
                    'roles' => $this->usuarioModel->getRoles()
                ]);
                return;
            }
        }
        
        $this->view('usuarios/crear', [
            'roles' => $this->usuarioModel->getRoles()
        ]);
    }
    
    /**
     * Editar usuario
     */
    public function editar($id) {
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect('usuario');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = $this->validarDatos($_POST, $_FILES, $id);
            
            if (empty($errors)) {
                $data = [
                    'rol_id' => $_POST['rol_id'],
                    'nombre' => trim($_POST['nombre']),
                    'apellidos' => trim($_POST['apellidos']),
                    'cargo' => trim($_POST['cargo'] ?? ''),
                    'email' => trim($_POST['email']),
                    'telefono' => trim($_POST['telefono'] ?? ''),
                    'estado' => $_POST['estado']
                ];
                
                // Procesar nueva contraseña si se proporcionó
                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                
                // Procesar nueva foto si se subió
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    // Eliminar foto anterior si existe
                    if (!empty($usuario['foto'])) {
                        $rutaFotoAnterior = '../public/uploads/usuarios/' . $usuario['foto'];
                        if (file_exists($rutaFotoAnterior)) {
                            unlink($rutaFotoAnterior);
                        }
                    }
                    
                    $foto = $this->subirFoto($_FILES['foto']);
                    if ($foto) {
                        $data['foto'] = $foto;
                    }
                }
                
                if ($this->usuarioModel->update($id, $data)) {
                    // Registrar en auditoría
                    $auditoriaModel = $this->model('Auditoria');
                    $auditoriaModel->registrar(
                        'Editó usuario: ' . $data['nombre'] . ' ' . $data['apellidos'], 
                        'usuarios', 
                        $id,
                        json_encode($usuario),
                        json_encode($data)
                    );
                    
                    $this->setFlash('success', 'Usuario actualizado exitosamente');
                    $this->redirect('usuario');
                } else {
                    $this->setFlash('error', 'Error al actualizar usuario');
                }
            } else {
                $this->view('usuarios/editar', [
                    'errors' => $errors,
                    'usuario' => array_merge($usuario, $_POST),
                    'roles' => $this->usuarioModel->getRoles()
                ]);
                return;
            }
        }
        
        $this->view('usuarios/editar', [
            'usuario' => $usuario,
            'roles' => $this->usuarioModel->getRoles()
        ]);
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        if ($id == $_SESSION['user_id']) {
            $this->json(['success' => false, 'message' => 'No puedes eliminarte a ti mismo'], 400);
        }
        
        if ($this->usuarioModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar usuario'], 500);
        }
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado($id) {
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
        
        $nuevoEstado = $usuario['estado'] == 1 ? 0 : 1;
        
        if ($this->usuarioModel->update($id, ['estado' => $nuevoEstado])) {
            $this->json(['success' => true, 'estado' => $nuevoEstado]);
        } else {
            $this->json(['success' => false, 'message' => 'Error al cambiar estado'], 500);
        }
    }
    
    /**
     * Validar datos
     */
    private function validarDatos($data, $files = [], $id = null) {
        $errors = [];
        
        // Validar nombre
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }
        
        // Validar apellidos
        if (empty($data['apellidos'])) {
            $errors['apellidos'] = 'Los apellidos son requeridos';
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } else {
            $existente = $this->usuarioModel->findByEmail($data['email']);
            if ($existente && (!$id || $existente['id'] != $id)) {
                $errors['email'] = 'El email ya está registrado';
            }
        }
        
        // Validar contraseña
        if (!$id && empty($data['password'])) {
            $errors['password'] = 'La contraseña es requerida';
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar rol
        if (empty($data['rol_id'])) {
            $errors['rol_id'] = 'El rol es requerido';
        }
        
        // Validar foto si se subió
        if (!empty($files) && isset($files['foto']) && $files['foto']['error'] == 0) {
            $foto = $files['foto'];
            $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($extension, $permitidas)) {
                $errors['foto'] = 'Solo se permiten imágenes JPG, JPEG, PNG o GIF';
            }
            
            if ($foto['size'] > 2097152) { // 2 MB
                $errors['foto'] = 'La imagen no debe superar los 2 MB';
            }
        }
        
        return $errors;
    }
    
    /**
     * Subir foto de perfil
     */
    private function subirFoto($archivo) {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreArchivo = 'user_' . uniqid() . '.' . $extension;
        $rutaDestino = '../public/uploads/usuarios/' . $nombreArchivo;
        
        // Crear directorio si no existe
        if (!file_exists('../public/uploads/usuarios')) {
            mkdir('../public/uploads/usuarios', 0777, true);
        }
        
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $nombreArchivo;
        }
        
        return false;
    }
}
