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

<!-- Modal Ver Información -->
<div class="modal fade" id="modalVerInfo" tabindex="-1" aria-labelledby="modalVerInfoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalVerInfoLabel">Información del Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Nombre:</strong> <span id="infoNombre"></span></p>
        <p><strong>Cargo:</strong> <span id="infoCargo"></span></p>
        <p><strong>Estado:</strong> <span id="infoEstado"></span></p>
        <p><strong>Fecha creación:</strong> <span id="infoFecha"></span></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="modalEditarLabel">Modificar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditar">
          <input type="hidden" id="editId">
          <div class="mb-3">
            <label for="editNombre" class="form-label">Nombre</label>
            <input type="text" id="editNombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="editApellido" class="form-label">Apellido</label>
            <input type="text" id="editApellido" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="editCargo" class="form-label">Cargo</label>
            <input type="text" id="editCargo" class="form-control">
          </div>
          <div class="mb-3">
            <label for="editEstado" class="form-label">Estado</label>
            <select id="editEstado" class="form-select">
              <option value="1">Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarLabel">Eliminar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p>¿Estás seguro de eliminar este usuario?</p>
        <button id="btnConfirmEliminar" class="btn btn-danger me-2">Sí, eliminar</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap scripts -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let usuariosOriginales = [];
  let idEliminar = null;

  document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
    document.getElementById('busqueda').addEventListener('input', filtrarUsuarios);

    document.getElementById('formEditar').addEventListener('submit', e => {
      e.preventDefault();
      guardarCambios();
    });

    document.getElementById('btnConfirmEliminar').addEventListener('click', eliminarUsuario);
  });

  function cargarUsuarios() {
    fetch('../back-end/obtener_usuarios.php')
      .then(res => res.json())
      .then(data => {
        usuariosOriginales = data;
        mostrarUsuarios(data);
      });
  }

  function normalizar(txt) {
    return txt.normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
  }

  function filtrarUsuarios() {
    const valor = normalizar(document.getElementById('busqueda').value);
    const filtrados = usuariosOriginales.filter(u =>
      normalizar(u.nombre).includes(valor) ||
      normalizar(u.apellido).includes(valor) ||
      normalizar(u.cargo || '').includes(valor)
    );
    mostrarUsuarios(filtrados);
  }
function mostrarUsuarios(usuarios) {
    const lista = document.getElementById('userList');
    lista.innerHTML = '';

    if (!usuarios.length) {
        lista.innerHTML = '<p class="text-center text-muted">No hay usuarios encontrados.</p>';
        return;
    }

    usuarios.forEach(u => {
        const div = document.createElement('div');
        div.className = 'list-group-item d-flex justify-content-between align-items-center';
        div.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-person fs-2 me-3"></i>
                <div>
                    <strong>${u.nombre} ${u.apellido}</strong><br>
                    <small class="text-muted">${u.cargo || 'Sin cargo'}</small>
                </div>
            </div>
            <div>
                <button class="btn btn-danger btn-sm me-2" onclick="confirmarEliminar(${u.id_user})">Eliminar</button>
                <button class="btn btn-success btn-sm me-2" onclick="darDeAlta(${u.id_user})">Dar de alta</button>
                <button class="btn btn-outline-secondary btn-sm me-2" onclick="abrirEditar(${u.id_user})">Modificar</button>
                <button class="btn btn-info btn-sm text-white" onclick="verInfo(${u.id_user})">Ver Info</button>
            </div>
        `;
        lista.appendChild(div);
    });
}


  // ✅ Ver información
  function verInfo(id) {
    fetch(`../back-end/obtener_usuario.php?id=${id}`)
      .then(res => res.json())
      .then(u => {
        document.getElementById('infoNombre').textContent = `${u.nombre} ${u.apellido}`;
        document.getElementById('infoCargo').textContent = u.cargo || 'Sin cargo';
        document.getElementById('infoEstado').textContent = u.estado_descripcion;
        document.getElementById('infoFecha').textContent = u.fecha_creacion;
        new bootstrap.Modal('#modalVerInfo').show();
      });
  }

  // ✏️ Abrir modal de edición
  function abrirEditar(id) {
    fetch(`../back-end/obtener_usuario.php?id=${id}`)
      .then(res => res.json())
      .then(u => {
        document.getElementById('editId').value = u.id_user;
        document.getElementById('editNombre').value = u.nombre;
        document.getElementById('editApellido').value = u.apellido;
        document.getElementById('editCargo').value = u.cargo;
        document.getElementById('editEstado').value = u.id_estado;
        new bootstrap.Modal('#modalEditar').show();
      });
  }

  // 💾 Guardar cambios
  function guardarCambios() {
    const formData = new FormData(document.getElementById('formEditar'));
    fetch('../back-end/actualizar_usuario.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(resp => {
        alert(resp.mensaje);
        if (resp.ok) {
          bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
          cargarUsuarios();
        }
      });
  }

  // 🗑️ Confirmar eliminación
  function confirmarEliminar(id) {
    idEliminar = id;
    new bootstrap.Modal('#modalEliminar').show();
  }

  // ❌ Eliminar usuario
  function eliminarUsuario() {
    fetch('../back-end/eliminar_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_user: idEliminar }) // <-- cambio aquí
    })
    .then(res => res.json())
    .then(resp => {
        alert(resp.message); // usar 'message' porque tu PHP devuelve 'message'
        if (resp.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
            cargarUsuarios();
        }
    });
}
function darDeAlta(id) {
    fetch('../back-end/dar_de_alta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_user: id })
    })
    .then(res => res.json())
    .then(resp => {
        alert(resp.message);
        if (resp.status === 'success') {
            cargarUsuarios();
        }
    });
}


</script>

</body>

</html>