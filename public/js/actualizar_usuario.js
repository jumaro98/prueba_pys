document.getElementById('btnBuscar').addEventListener('click', () => {
    const usuario = document.getElementById('usuario').value.trim();
    const mensaje = document.getElementById('mensaje');
    mensaje.innerHTML = '';

    if (!usuario) {
        mensaje.innerHTML = '<div class="alert alert-warning">Debe ingresar el usuario.</div>';
        return;
    }

    fetch(`http://localhost:8080/usuarios/${usuario}`)
        .then(res => res.json())
        .then(data => {
            if (data.codigo === 200) {
                document.getElementById('camposActualizar').style.display = 'block';
                document.getElementById('nombre').value = data.usuario.nombre;
                document.getElementById('password').value = '';
                document.getElementById('btnActualizar').dataset.id_usuario = data.usuario.id_usuario;
            } else {
                mensaje.innerHTML = `<div class="alert alert-danger">${data.msg}</div>`;
                document.getElementById('camposActualizar').style.display = 'none';
            }
        })
        .catch(err => {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
        });
});

document.getElementById('btnActualizar').addEventListener('click', () => {
    const id_usuario = document.getElementById('btnActualizar').dataset.id_usuario;
    const nombre = document.getElementById('nombre').value.trim();
    const password = document.getElementById('password').value.trim();
    const mensaje = document.getElementById('mensaje');
    mensaje.innerHTML = '';

    if (!nombre || !password) {
        mensaje.innerHTML = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
        return;
    }

    fetch(`http://localhost:8080/usuarios/actualizar/${id_usuario}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, password })
        
        
    })
        .then(res => res.json())
        .then(data => {
            if (data.codigo === 200) {
                mensaje.innerHTML = `<div class="alert alert-success">${data.msg}</div>`;
            } else {
                mensaje.innerHTML = `<div class="alert alert-danger">${data.msg}</div>`;
            }
        })
        .catch(err => {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
        });
});

function redirigir(modulo) {
/*   alert(`Aquí redirigirás al módulo de ${modulo}`); */
  // Por ejemplo:
   window.location.href = `${modulo}.html`;
}
