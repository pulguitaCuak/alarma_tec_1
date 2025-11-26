<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: /alarma_tec_1/frontend/login.html");
    exit;
}

require_once "../back-end/db.php";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administración - Alarma</title>
  <!-- Bootstrap local -->
  <link rel="stylesheet" href="/alarma_tec_1/frontend/css/bootstrap.custom.min.css">
  <link rel="stylesheet" href="/alarma_tec_1/frontend/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="/alarma_tec_1/frontend/css/plantillaPrincipal.css">
  <style>
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
      justify-content: center;
      align-items: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 500px;
    }

    .modal-content h5 {
      margin-bottom: 1.5rem;
      font-weight: bold;
    }

    .state-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .state-active {
      background-color: #d4edda;
      color: #155724;
    }

    .state-inactive {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <!-- HEADER -->
  <header class="bg-primary text-white py-3 d-flex align-items-center justify-content-between px-4">
    <div class="d-flex align-items-center">
      <button class="btn btn-outline-light d-lg-none me-3" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <h1 class="h4 m-0"><a href="/alarma_tec_1/frontend/index.html" class="navbar-brand">Alarma</a></h1>
    </div>
    <div class="d-flex align-items-center">
      <h2 class="h5 m-0 me-2">Administración de Usuarios</h2>
      <i class="bi bi-hdd-stack fs-5"></i>
    </div>
  </header>

  <!-- MAIN -->
  <main class="flex-fill">
    <div class="d-flex flex-grow-1">
      <!-- SIDEBAR (oculta en móvil) -->
      <aside id="sidebar" class="sidebar bg-dark d-flex flex-column align-items-center py-4 min-vh-100">
        <a href="/alarma_tec_1/frontend/administracionUsuarios.php" class="my-3 text-white" title="Usuarios"><i class="bi bi-people fs-4 p-4"></i></a>
        <a href="/alarma_tec_1/frontend/dashboardEquipos.html" class="my-3 text-white" title="Equipos"><i class="bi bi-grid fs-4 p-4"></i></a>
        <a href="/alarma_tec_1/frontend/administracionRegistrar.html" class="my-3 text-white" title="Registrar"><i class="bi bi-person-plus fs-4 p-4"></i></a>
        <a href="/alarma_tec_1/frontend/administracionEquipos.html" class="my-3 text-white" title="Listar"><i class="bi bi-list fs-4 p-4"></i></a>
      </aside>

      <!-- CONTENIDO CON USUARIOS -->
      <div class="flex-grow-1 ms-3">
        <div class="container-fluid py-4">
          <div class="d-flex align-items-center mb-3">
            <input type="text" class="form-control me-2" id="searchInput" placeholder="Buscar usuario por nombre...">
            <div class="dropdown">
              <a class="btn btn-info rounded-5" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel"></i>
              </a>
              <ul class="dropdown-menu">
                <li><h6 class="dropdown-header text-body">Filtrar por Estado</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li class="p-2">
                  <select class="form-select form-select-sm" id="filterEstado">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                </li>
              </ul>
            </div>
          </div>

          <!-- Lista de usuarios -->
          <div class="list-group shadow-sm" id="usuariosContainer">
            <div class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-primary text-white text-center py-2 mt-auto">
    <small class="footer-text">2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.</small>
  </footer>

  <!-- Bootstrap scripts -->
  <script src="/alarma_tec_1/frontend/js/bootstrap.bundle.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    const usuariosContainer = document.getElementById('usuariosContainer');
    const searchInput = document.getElementById('searchInput');
    const filterEstado = document.getElementById('filterEstado');

    let todosUsuarios = [];

    // Cargar usuarios dinámicamente
    async function cargarUsuarios() {
      try {
        const res = await fetch('/alarma_tec_1/back-end/obtener_usuarios.php');
        const data = await res.json();

        if (!data || data.length === 0) {
          usuariosContainer.innerHTML = '<p class="text-muted text-center py-4">No hay usuarios disponibles.</p>';
          return;
        }

        todosUsuarios = data;
        mostrarUsuarios(data);

      } catch (err) {
        console.error('Error:', err);
        usuariosContainer.innerHTML = '<p class="text-danger text-center py-4">Error al cargar usuarios.</p>';
      }
    }

    function mostrarUsuarios(usuarios) {
      usuariosContainer.innerHTML = '';

      usuarios.forEach(usuario => {
        const estadoClass = usuario.estado == 1 ? 'state-active' : 'state-inactive';
        const estadoText = usuario.estado == 1 ? 'Activo' : 'Inactivo';
        const fechaCreacion = new Date(usuario.fecha_creacion).toLocaleDateString('es-AR');

        const item = document.createElement('div');
        item.className = 'list-group-item d-flex justify-content-between align-items-center p-3';
        item.innerHTML = `
          <div>
            <h6 class="mb-1"><strong>${usuario.nombre} ${usuario.apellido}</strong></h6>
            <small class="text-muted">Cargo: ${usuario.cargo || 'N/A'} | Creado: ${fechaCreacion}</small>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <span class="state-badge ${estadoClass}">${estadoText}</span>
            <button class="btn btn-sm btn-outline-primary" onclick="editarUsuario(${usuario.id_user})">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${usuario.id_user}, '${usuario.nombre} ${usuario.apellido}')">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        `;

        usuariosContainer.appendChild(item);
      });
    }

    // Buscar y filtrar
    function filtrarUsuarios() {
      const searchTerm = searchInput.value.toLowerCase();
      const filterValue = filterEstado.value;

      const usuariosFiltrados = todosUsuarios.filter(usuario => {
        const coincideTexto = usuario.nombre.toLowerCase().includes(searchTerm) || 
                             usuario.apellido.toLowerCase().includes(searchTerm);
        const coincideEstado = filterValue === '' || usuario.estado == filterValue;
        return coincideTexto && coincideEstado;
      });

      mostrarUsuarios(usuariosFiltrados);
    }

    searchInput.addEventListener('input', filtrarUsuarios);
    filterEstado.addEventListener('change', filtrarUsuarios);

    function editarUsuario(id) {
      alert('Edición de usuario ' + id + ' (próximamente)');
      // Aquí irá la lógica de edición
    }

    async function eliminarUsuario(id, nombre) {
      if (confirm(`¿Estás seguro de que quieres eliminar a ${nombre}?`)) {
        try {
          const formData = new FormData();
          formData.append('id_user', id);

          const response = await fetch('/alarma_tec_1/back-end/eliminar_usuario.php', {
            method: 'POST',
            body: formData
          });

          const result = await response.json();

          if (result.ok) {
            alert('Usuario eliminado correctamente');
            cargarUsuarios();
          } else {
            alert('Error: ' + result.mensaje);
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error al eliminar usuario');
        }
      }
    }

    // Cargar usuarios al iniciar
    cargarUsuarios();
  </script>
</body>
</html>
