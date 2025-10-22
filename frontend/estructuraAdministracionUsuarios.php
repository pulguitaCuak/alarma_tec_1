<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Alarma</title>
  <!-- Bootstrap local -->
  <link rel="stylesheet" href="css/bootstrap.custom.min.css">
  <link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="css/plantillaPrincipal.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

  <!-- HEADER -->
  <header class="bg-primary text-white py-3 d-flex align-items-center justify-content-between px-4">
    <div class="d-flex align-items-center">
      <!-- Botón hamburguesa móvil -->
      <button class="btn btn-outline-light d-lg-none me-3" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <h1 class="h4 m-0">Alarma</h1>
    </div>
    <div class="d-flex align-items-center">
      <h2 class="h5 m-0 me-2">Administración</h2>
      <i class="bi bi-hdd-stack fs-5"></i>
    </div>
  </header>

  <!-- MAIN -->
  <main class="flex-fill">

    <div class="d-flex flex-grow-1">
      <!-- SIDEBAR (oculta en móvil) -->
      <aside id="sidebar" class="sidebar bg-dark d-flex flex-column align-items-center py-4 min-vh-100">
        <a href="#" class="my-3"><i class="bi bi-house fs-4 p-4"></i></a>
        <a href="#" class="my-3"><i class="bi bi-grid fs-4 p-4"></i></a>
        <a href="estructuraRegistroUsuarios.php" class="my-3"><i class="bi bi-person-plus fs-4 p-4"></i></a>

      </aside>

      <!-- CONTENIDO CON USUARIOS -->
      <div class="flex-grow-1 ms-3">
        <div class="container-fluid py-4">
          <div class="d-flex align-items-center mb-3">
            <input type="text" id="busqueda" class="form-control me-2"
              placeholder="Buscar usuario por nombre o apellido...">
            <button class="btn btn-outline-secondary me-1"><i class="bi bi-x"></i></button>
            <button class="btn btn-outline-secondary me-1"><i class="bi bi-search"></i></button>
            <button class="btn btn-outline-secondary"><i class="bi bi-filter"></i></button>
          </div>

          <!-- Lista de usuarios -->
          <div id="userList" class="list-group shadow-sm">


            <!-- Usuario -->



          </div>
        </div>
      </div>

  </main>

  <!-- FOOTER -->
  <footer class="bg-primary text-white text-center py-2 mt-auto">
    <small class="footer-text">2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.</small>
  </footer>


  <!-- Bootstrap scripts -->
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    let usuariosOriginales = [];

    document.addEventListener('DOMContentLoaded', () => {
      cargarUsuarios();
      document.getElementById('busqueda').addEventListener('input', filtrarUsuarios);
    });

    function cargarUsuarios() {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', '../back-end/obtener_usuarios.php', true);
      xhr.onload = function () {
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
        <div class="list-group-item d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="bi bi-person fs-2 me-3"></i>
                <div>
                  <strong>${u.nombre} ${u.apellido}</strong><br>
                  <small class="text-muted">${u.cargo || 'Sin cargo'}</small>
                </div>
              </div>
              <div>
                <button class="btn btn-danger btn-sm me-2" onclick="eliminarUsuario(${u.id_user})">Eliminar</button>
                <button class="btn btn-outline-secondary btn-sm me-2" onclick="modificarUsuario(${u.id_user})">Modificar</button>
                <button class="btn btn-info btn-sm text-white" onclick="verInfo(${u.id_user})">Ver Info.</button>
              </div>
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