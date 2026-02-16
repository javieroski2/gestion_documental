<?php
/**
 * Categoria Controller
 * Sistema de Gestión Documental
 */

class CategoriaController extends Controller {
    
    private $categoriaModel;
    private $auditoriaModel;
    
    public function __construct() {
        $this->requireRole([ROL_SUPER_ADMIN, ROL_ADMIN]);
        $this->categoriaModel = $this->model('Categoria');
        $this->auditoriaModel = $this->model('Auditoria');
    }
    
    /**
     * Listar categorías
     */
    public function index() {
        $categorias = $this->categoriaModel->getAll();
        $this->view('categorias/index', ['categorias' => $categorias]);
    }
    
    /**
     * Crear categoría
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errores = [];
            
            if (empty($_POST['nombre'])) {
                $errores[] = 'El nombre es requerido';
            }
            
            if (empty($errores)) {
                $data = [
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'] ?? null,
                    'estado' => 1
                ];
                
                if ($this->categoriaModel->insert($data)) {
                    $this->auditoriaModel->registrar('Creó categoría: ' . $data['nombre'], 'categorias', null, null, $data);
                    
                    $this->setFlash('success', 'Categoría creada correctamente');
                    $this->redirect('categoria');
                } else {
                    $this->setFlash('danger', 'Error al crear la categoría');
                }
            } else {
                $this->setFlash('danger', implode('<br>', $errores));
            }
        }
        
        $this->view('categorias/crear');
    }
    
    /**
     * Editar categoría
     */
    public function editar($id) {
        $categoria = $this->categoriaModel->getById($id);
        
        if (!$categoria) {
            $this->setFlash('danger', 'Categoría no encontrada');
            $this->redirect('categoria');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errores = [];
            
            if (empty($_POST['nombre'])) {
                $errores[] = 'El nombre es requerido';
            }
            
            if (empty($errores)) {
                $data = [
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'] ?? null
                ];
                
                if ($this->categoriaModel->update($id, $data)) {
                    $this->auditoriaModel->registrar('Actualizó categoría: ' . $data['nombre'], 'categorias', $id, $categoria, $data);
                    
                    $this->setFlash('success', 'Categoría actualizada correctamente');
                    $this->redirect('categoria');
                } else {
                    $this->setFlash('danger', 'Error al actualizar la categoría');
                }
            } else {
                $this->setFlash('danger', implode('<br>', $errores));
            }
        }
        
        $this->view('categorias/editar', ['categoria' => $categoria]);
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado($id) {
        $categoria = $this->categoriaModel->getById($id);
        
        if (!$categoria) {
            $this->json(['success' => false, 'message' => 'Categoría no encontrada'], 404);
        }
        
        $nuevoEstado = $categoria['estado'] == 1 ? 0 : 1;
        
        if ($this->categoriaModel->cambiarEstado($id, $nuevoEstado)) {
            $accion = $nuevoEstado == 1 ? 'Activó' : 'Desactivó';
            $this->auditoriaModel->registrar($accion . ' categoría: ' . $categoria['nombre'], 'categorias', $id);
            
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al actualizar el estado'], 500);
        }
    }
    
    /**
     * Eliminar categoría
     */
    public function eliminar($id) {
        $categoria = $this->categoriaModel->getById($id);
        
        if (!$categoria) {
            $this->json(['success' => false, 'message' => 'Categoría no encontrada'], 404);
        }
        
        // Verificar si tiene documentos asociados
        if ($this->categoriaModel->tieneDocumentos($id)) {
            $this->json(['success' => false, 'message' => 'No se puede eliminar: la categoría tiene documentos asociados'], 400);
        }
        
        if ($this->categoriaModel->delete($id)) {
            $this->auditoriaModel->registrar('Eliminó categoría: ' . $categoria['nombre'], 'categorias', $id, $categoria);
            
            $this->json(['success' => true, 'message' => 'Categoría eliminada correctamente']);
        } else {
            $this->json(['success' => false, 'message' => 'Error al eliminar la categoría'], 500);
        }
    }
}
