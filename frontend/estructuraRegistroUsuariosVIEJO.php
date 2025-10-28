<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrarse - Sistema de Alarma</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f6f9fb;
    margin: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
  }

  header {
    background-color: #0e9aa7;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 25px;
    font-size: 20px;
  }

  .sidebar {
    background-color: #ececec;
    width: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 15px;
    border-right: 1px solid #ccc;
  }

  .sidebar button {
    background: none;
    border: none;
    cursor: pointer;
    margin: 10px 0;
  }

  main {
    flex: 1;
    display: flex;
  }

  .container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .form-box {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    width: 300px;
  }

  .form-box h2 {
    text-align: center;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 15px;
  }

  label {
    font-size: 14px;
    display: block;
    margin-bottom: 5px;
  }

  input, select {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
  }

  .password-group {
    position: relative;
  }

  .toggle-pass {
    position: absolute;
    right: 8px;
    top: 8px;
    cursor: pointer;
  }

  button#registrar {
    width: 100%;
    background-color: #0e9aa7;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
  }

  footer {
    background-color: #0e9aa7;
    color: white;
    text-align: center;
    padding: 10px;
    font-size: 13px;
  }
</style>
</head>
<body>

<header>
  <div>Alarma</div>
  <div>Administración</div>
</header>

<main>
  <div class="sidebar">
    <button>🏠</button>
    <button>📊</button>
    <button>👤</button>
  </div>

  <div class="container">
    <div class="form-box">
      <h2>Registrarse</h2>
      <form id="formRegistro">
        <div class="form-group">
          <label>Nombre</label>
          <input type="text" id="nombre" required placeholder="usuario">
        </div>
        <div class="form-group">
          <label>Teléfono</label>
          <input type="tel" id="telefono" placeholder="11234523">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="email" placeholder="email@ejemplo.com">
        </div>
        <div class="form-group">
          <label>Fecha de Nacimiento</label>
          <input type="date" id="fechaNacimiento">
        </div>
        <div class="form-group">
          <label>DNI</label>
          <input type="number" id="dni" placeholder="DNI">
        </div>
        <div class="form-group">
          <label>Cargo</label>
          <select id="cargo" required>
            <option value="">Cargando cargos...</option>
          </select>
        </div>
        <div class="form-group password-group">
          <label>Contraseña</label>
          <input type="password" id="password" placeholder="contraseña">
          <span class="toggle-pass" onclick="togglePass('password')">👁️</span>
        </div>
        <div class="form-group password-group">
          <label>Confirmar Contraseña</label>
          <input type="password" id="confirmPassword" placeholder="contraseña">
          <span class="toggle-pass" onclick="togglePass('confirmPassword')">👁️</span>
        </div>
        <button type="submit" id="registrar">Registrarse</button>
      </form>
    </div>
  </div>
</main>

<footer>
  2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.
</footer>

<script>
  function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
  }

  function cargarCargos() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../back-end/cargos.php', true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        try {
          const data = JSON.parse(xhr.responseText);
          const select = document.getElementById('cargo');
          select.innerHTML = '<option value="">Seleccione un cargo</option>';
          data.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id_cargo;
            opt.textContent = c.nombre;
            select.appendChild(opt);
          });
        } catch (error) {
          console.error('Error al procesar respuesta JSON:', error);
        }
      }
    };
    xhr.send();
  }

  document.addEventListener('DOMContentLoaded', cargarCargos);

  // Validar y enviar formulario
  document.getElementById('formRegistro').addEventListener('submit', function(e) {
    e.preventDefault();

    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (pass !== confirm) {
      alert('Las contraseñas no coinciden.');
      return;
    }

    alert('Formulario validado correctamente con AJAX listo para envío.');
  });
</script>

</body>
</html>
