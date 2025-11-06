document.addEventListener('DOMContentLoaded', () => {
  // Obtener usuario del almacenamiento local
  const usuario = localStorage.getItem('usuario');

  const mensaje = document.getElementById('mensajeBienvenida');
  if (usuario) {
    mensaje.textContent = `Hola , gracias por visitarnos.`;
  } else {
    mensaje.textContent = `Hola, bienvenido al sistema.`;
  }
});

function redirigir(modulo) {
/*   alert(`Aquí redirigirás al módulo de ${modulo}`); */
  // Por ejemplo:
   window.location.href = `${modulo}.html`;
}