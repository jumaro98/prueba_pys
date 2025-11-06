$(document).ready(function () {
    // Inicializar DataTable
    const tabla = $('#tablaViajes').DataTable({
        ajax: {
            url: 'http://localhost:8080/viajes/lista',
            dataSrc: 'data'
        },
        columns: [
            { data: 'idviaje' },
            { data: 'placa' },
            { data: 'ciudad_origen' },
            { data: 'ciudad_destino' },
            { data: 'tiempo_horas' },
            { data: 'fecha' },
            {
                data: null,
                render: function (data) {
                    return `<button class="btn btn-warning btn-sm btnEditar" data-id="${data.idviaje}">Modificar</button>`;
                }
            }
        ]
    });

    // Cargar ciudades al abrir modal
    function cargarCiudades() {
        fetch('http://localhost:8080/ciudades/lista')
            .then(res => res.json())
            .then(data => {
                const origen = $('#origen');
                const destino = $('#destino');
                origen.empty();
                destino.empty();
                data.data.forEach(c => {
                    origen.append(`<option value="${c.idciudad}">${c.nombre}</option>`);
                    destino.append(`<option value="${c.idciudad}">${c.nombre}</option>`);
                });
            });
    }

    // Abrir modal crear
    $('#btnCrear').click(() => {
        $('#modalViajeLabel').text('Crear Viaje');
        $('#formViaje')[0].reset();
        $('#idviaje').val('');
        cargarCiudades();
        $('#modalViaje').modal('show');
    });

    // Guardar o actualizar viaje
    $('#btnGuardar').click(() => {
        const datos = {
            idviaje: $('#idviaje').val(),
            idcarro: $('#idcarro').val(),
            idciudad_origen: $('#origen').val(),
            idciudad_destino: $('#destino').val(),
            tiempo_horas: $('#tiempo').val(),
            fecha: $('#fecha').val()
        };

        const url = datos.idviaje
            ? `http://localhost:8080/viajes/actualizar`
            : `http://localhost:8080/viajes/crear`;

        const method = datos.idviaje ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
            .then(res => res.json())
            .then(data => {
                alert(data.msg);
                $('#modalViaje').modal('hide');
                tabla.ajax.reload();
            })
            .catch(err => console.error('Error:', err));
    });

    // Editar viaje
    $('#tablaViajes tbody').on('click', '.btnEditar', function () {
        const data = tabla.row($(this).parents('tr')).data();
        $('#modalViajeLabel').text('Modificar Viaje');
        $('#idviaje').val(data.idviaje);
        $('#idcarro').val(data.idcarro);
        cargarCiudades(); // refresca ciudades
        setTimeout(() => { // espera a que se carguen opciones
            $('#origen').val(data.idciudad_origen);
            $('#destino').val(data.idciudad_destino);
        }, 100);
        $('#tiempo').val(data.tiempo_horas);
        $('#fecha').val(data.fecha.replace(' ', 'T'));
        $('#modalViaje').modal('show');
    });
});

  document.getElementById('btnRegresar').addEventListener('click', function() {
    // Si quieres regresar a la página anterior del navegador
    window.history.back();

    // O si quieres redirigir a una página específica:
    // window.location.href = 'bienvenida.html';
  });
