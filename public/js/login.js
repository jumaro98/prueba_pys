document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const mensaje = document.getElementById('mensaje');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const usuario = document.getElementById('usuario').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (!usuario || !password) {
      mensaje.innerHTML = '<div class="alert alert-warning">Todos los campos son obligatorios</div>';
      return;
    }

    try {
        
        const prueba=JSON.stringify({ usuario, password });

      const response = await fetch('http://localhost:8080/usuarios/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario, password })
      });
      const data = await response.json();

      console.log("Respuesta"+ data.codigo);
      

      if (data.codigo === 200) {
        mensaje.innerHTML = '<div class="alert alert-success">✅ Inicio de sesión exitoso</div>';

        window.location.href = 'bienvenida.html';

        setTimeout(() => {
          window.location.href = 'dashboard.html'; // Página de destino
        }, 2000);
      } else {
        mensaje.innerHTML = `<div class="alert alert-danger">❌ ${data.msg}</div>`;
      }
    } catch (error) {
      mensaje.innerHTML = '<div class="alert alert-danger">Error de conexión con el servidor</div>';
      console.error('Error:', error);
    }
  });
});