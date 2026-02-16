/**
 * JavaScript Personalizado
 * Sistema de Gestión Documental
 */

$(document).ready(function() {
    
    // Auto-dismiss alerts después de 5 segundos
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
    
    // Activar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Activar popovers
    $('[data-toggle="popover"]').popover();
    
    // Confirmación de eliminación
    $('.btn-delete').on('click', function(e) {
        if (!confirm('¿Está seguro de eliminar este registro?')) {
            e.preventDefault();
        }
    });
    
    // Mostrar loading
    $('.btn-submit').on('click', function() {
        $(this).prop('disabled', true);
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
    });
    
});

// Función para mostrar mensajes de alerta
function showAlert(type, message) {
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '</div>';
    
    $('.content-wrapper').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}
