<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administrar Usuarios - Sistema de Alarma</title>
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

  .content {
    flex: 1;
    padding: 20px;
    overflow: hidden;
  }

  .search-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
  }

  .search-bar input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
  }

  .user-list {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    height: 350px;
    overflow-y: auto;
    padding: 10px;
  }

  .user-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding: 10px;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .user-icon {
    font-size: 24px;
  }

  .user-text strong {
    display: block;
    font-size: 15px;
  }

  .user-text small {
    color: gray;
    font-size: 13px;
  }

  .user-actions button {
    margin-left: 5px;
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }

  .btn-eliminar {
    background-color: #e74c3c;
    color: white;
  }

  .btn-modificar {
    background-color: #f1c40f;
    color: black;
  }

  .btn-info {
    background-color: #3498db;
    color: white;
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

  <div class="content">
    <!-- 🔍 Barra de búsqueda -->
    <div class="search-bar">
      <input type="text" id="busqueda" placeholder="Buscar usuario por nombre o apellido...">
    </div>

    <div class="user-list" id="userList">
      <!-- Usuarios cargados desde PHP -->
    </div>
  </div>
</main>

<footer>
  2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.
</footer>

<script>
  let usuariosOriginales = [];

  document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
    document.getElementById('busqueda').addEventListener('input', filtrarUsuarios);
  });

  function cargarUsuarios() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../back-end/obtener_usuarios.php', true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        usuariosOriginales = JSON.parse(xhr.responseText);
        mostrarUsuarios(usuariosOriginales);
      }
    };
    xhr.send();
  }

  // 🔎 Normaliza texto (quita acentos y pasa a minúsculas)
  function normalizar(texto) {
    return texto
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase();
  }

  // 🔍 Filtra usuarios desde el front-end
  function filtrarUsuarios() {
    const valor = normalizar(document.getElementById('busqueda').value);
    const filtrados = usuariosOriginales.filter(u => {
      const nombre = normalizar(u.nombre);
      const apellido = normalizar(u.apellido);
      const cargo = normalizar(u.cargo || '');
      return (
        nombre.includes(valor) ||
        apellido.includes(valor) ||
        `${nombre} ${apellido}`.includes(valor) ||
        cargo.includes(valor)
      );
    });
    mostrarUsuarios(filtrados);
  }

  function mostrarUsuarios(usuarios) {
    const lista = document.getElementById('userList');
    lista.innerHTML = '';

    if (usuarios.length === 0) {
      lista.innerHTML = '<p style="text-align:center; color:gray;">No hay usuarios encontrados.</p>';
      return;
    }

    usuarios.forEach(u => {
      const item = document.createElement('div');
      item.className = 'user-item';
      item.innerHTML = `
        <div class="user-info">
          <div class="user-icon">👤</div>
          <div class="user-text">
            <strong>${u.nombre} ${u.apellido}</strong>
            <small>${u.cargo || 'Sin cargo'}</small>
          </div>
        </div>
        <div class="user-actions">
          <button class="btn-eliminar" onclick="eliminarUsuario(${u.id_user})">Eliminar</button>
          <button class="btn-modificar" onclick="modificarUsuario(${u.id_user})">Modificar</button>
          <button class="btn-info" onclick="verInfo(${u.id_user})">Ver Info.</button>
        </div>
      `;
      lista.appendChild(item);
    });
  }

  // Placeholders
  function eliminarUsuario(id) { alert(`Eliminar usuario con ID ${id}`); }
  function modificarUsuario(id) { alert(`Modificar usuario con ID ${id}`); }
  function verInfo(id) { alert(`Ver información de usuario con ID ${id}`); }

</script>

</body>
</html>
