$(document).ready(function() {
    let tabla;

    $('.list-group-item').click(function() {
        const consulta = $(this).data('consulta');
        let url = `http://localhost:8080/consultas/${consulta}`;

        // Destruir DataTable anterior si existe
        if ($.fn.DataTable.isDataTable('#tablaConsultas')) {
            $('#tablaConsultas').DataTable().destroy();
            $('#tablaConsultas tbody').empty();
            $('#theadConsultas').empty();
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.codigo === 200) {
                    // Generar encabezado dinámico
                    if (data.data.length > 0) {
                        const keys = Object.keys(data.data[0]);
                        keys.forEach(k => $('#theadConsultas').append(`<th>${k}</th>`));

                        // Inicializar DataTable
                        tabla = $('#tablaConsultas').DataTable({
                            data: data.data,
                            columns: keys.map(k => ({ data: k })),
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                            }
                        });
                    } else {
                        alert('No hay resultados para esta consulta.');
                    }
                } else {
                    alert(data.msg);
                }
            })
            .catch(err => console.error('Error:', err));
    });

});

  document.getElementById('btnRegresar').addEventListener('click', function() {
    // Si quieres regresar a la página anterior del navegador
    window.history.back();

    // O si quieres redirigir a una página específica:
    // window.location.href = 'bienvenida.html';
  });