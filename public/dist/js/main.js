
    $(document).ready(function() {
        $('#buscar-cliente').on('input', function() {
            var searchTerm = $(this).val().toLowerCase(); // Obtener el término de búsqueda en minúsculas
            $('.table tbody tr').each(function() {
                var textoFila = $(this).text().toLowerCase(); // Obtener el texto de la fila en minúsculas
                // Si el término de búsqueda está contenido en el texto de la fila, mostrar la fila, de lo contrario, ocultarla
                $(this).toggle(textoFila.indexOf(searchTerm) > -1);
            });
        });
    });
