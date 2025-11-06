document.addEventListener('DOMContentLoaded', () => {

  document.getElementById('btnRegresar').addEventListener('click', () => {
    window.history.back();
  });

  document.getElementById('formCrearUsuario').addEventListener('submit', async (e) => {
    e.preventDefault();

    const datos = {
      usuario: document.getElementById('usuario').value,
      nombre: document.getElementById('nombre').value,
      password: document.getElementById('password').value
    };

    try {
      const res = await fetch('http://localhost:8080/usuarios/crear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      });

      const data = await res.json();
      alert(data.msg);

      if (res.ok) {
        document.getElementById('formCrearUsuario').reset();
      }
    } catch (err) {
      console.error('Error:', err);
      alert('Error al crear el usuario.');
    }
  });

});

function redirigir(modulo) {
/*   alert(`Aquí redirigirás al módulo de ${modulo}`); */
  // Por ejemplo:
   window.location.href = `${modulo}.html`;
}
