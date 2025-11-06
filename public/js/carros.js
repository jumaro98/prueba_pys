$(document).ready(function () {
    const tabla = $('#tablaCarros').DataTable({
        ajax: {
            url: 'http://localhost:8080/carros/lista',
            dataSrc: 'data'
        },
        columns: [
            { data: 'idcarro' },
            { data: 'placa' },
            { data: 'color' },
            { data: 'fecha_ingreso' },
            {
                data: null,
                render: function (data) {
                    return `
                        <button class="btn btn-warning btn-sm btnEditar" data-id="${data.idcarro}">Editar</button>
                        <button class="btn btn-danger btn-sm btnEliminar" data-id="${data.idcarro}">Eliminar</button>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });

    // Modal Crear
    $('#btnCrear').click(() => {
        $('#modalCarroLabel').text('Crear Carro');
        $('#formCarro')[0].reset();
        $('#idcarro').val('');
        $('#modalCarro').modal('show');
    });

    // Guardar o actualizar
    $('#btnGuardar').click(() => {
        const datos = {
            idcarro: $('#idcarro').val(),
            placa: $('#placa').val(),
            color: $('#color').val(),
            fecha_ingreso: $('#fecha').val()
        };

        const url = datos.idcarro ? 
            `http://localhost:8080/carros/actualizar` : 
            `http://localhost:8080/carros/crear`;

        fetch(url, {
            method: datos.idcarro ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.msg);
            $('#modalCarro').modal('hide');
            tabla.ajax.reload();
        })
        .catch(err => console.error('Error:', err));
    });

    // Editar
    $('#tablaCarros tbody').on('click', '.btnEditar', function () {
        const data = tabla.row($(this).parents('tr')).data();
        $('#modalCarroLabel').text('Editar Carro');
        $('#idcarro').val(data.idcarro);
        $('#placa').val(data.placa);
        $('#color').val(data.color);
        $('#fecha').val(data.fecha_ingreso.split('T')[0]);
        $('#modalCarro').modal('show');
    });

    // Eliminar
    $('#tablaCarros tbody').on('click', '.btnEliminar', function () {
        if (!confirm('¿Desea eliminar este carro?')) return;
        const id = $(this).data('id');

        fetch(`http://localhost:8080/carros/eliminar/${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(data => {
            alert(data.msg);
            tabla.ajax.reload();
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
