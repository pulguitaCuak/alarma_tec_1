<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.html");
    exit;
}

require_once "../../back-end/db.php";

if (!isset($_GET['id_zona'])) {
    die("Falta id_zona en la URL");
}

$id_zona = intval($_GET['id_zona']);
$id_equipo = isset($_GET['id_equipo']) ? intval($_GET['id_equipo']) : 0;
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Sensores</title>

  <!-- Bootstrap local -->
  <link rel="stylesheet" href="/alarma_tec_1/frontend/css/bootstrap.custom.min.css">
  <link rel="stylesheet" href="/alarma_tec_1/frontend/bootstrap-icons-1.13.1/bootstrap-icons.css">
  <!-- Estilos del dashboard principal -->
  <link rel="stylesheet" href="/alarma_tec_1/frontend/css/plantillaPrincipal.css">
  <link rel="stylesheet" href="/alarma_tec_1/frontend/css/estructuraDasboardSensores.css">

  <style>
    .blur-active main,
    .blur-active header,
    .blur-active footer,
    .blur-active aside {
      filter: blur(5px);
      pointer-events: none;
    }

    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1050;
      overflow-y: auto;
    }

    .modal-content-custom {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      width: 90%;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      margin: auto;
    }

    .modal-content-custom i {
      font-size: 40px;
      color: #0d6efd;
      margin-bottom: 10px;
    }

    .modal-content-custom h5 {
      margin-bottom: 15px;
    }

    .modal-content-custom .btn {
      min-width: 100px;
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
      <h1 class="h4 m-0">Sensores</h1>
    </div>
    <div class="d-flex align-items-center">
      <h2 class="h5 m-0 me-2">Zona <?php echo $id_zona; ?></h2>
      <i class="bi bi-broadcast fs-5"></i>
    </div>
  </header>

  <!-- MAIN -->
  <main class="flex-fill">
    <div class="dashboard-layout">

      <!-- SIDEBAR -->
      <aside id="sidebar" class="sidebar bg-dark d-flex flex-column align-items-center py-4 min-vh-100" data-included-sidebar>
        <!-- sidebar content loaded from /frontend/includes/sidebar.html -->
      </aside>

      <!-- CONTENEDOR PRINCIPAL -->
      <div class="cards-section py-4 px-4">
        <div class="container-fluid">
          <div class="row g-3 card-container" id="sensoresContainer">
            <div class="text-center text-muted">Cargando sensores...</div>
          </div>
        </div>
      </div>

      <!-- CONTENEDOR DE ACTIVIDAD -->
      <div class="activity-section py-4 px-3">
        <div class="activity-container" id="activityContainer">
          <div class="activity-card borde">
            <div class="d-flex justify-content-between">
              <h6 class="mb-1">Actividad</h6>
              <div class="d-flex gap-4">
                <small class="mb-1">Fecha</small>
                <small class="text-muted">Hora</small>
              </div>
            </div>
          </div>
          <!-- Las actividades se insertan dinámicamente -->
        </div>
      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-primary text-white text-center py-2 mt-auto">
    <small class="footer-text">2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.</small>
  </footer>

  <!-- ====== MODAL DETALLES SENSOR ====== -->
  <div id="modalDetalles" class="modal-overlay">
    <div class="modal-content-custom">
      <i class="bi bi-info-circle" style="color: black;"></i>
      <h5>Detalles del Sensor</h5>
      <div id="detallesSensor" class="text-start">
        <!-- Se rellena dinámicamente -->
      </div>
      <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-primary" id="cerrarDetalles">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- ====== MODAL EDITAR SENSOR ====== -->
  <div id="modalEditar" class="modal-overlay">
    <div class="modal-content-custom">
      <i class="bi bi-pencil-square" style="color: black;"></i>
      <h5>Editar Sensor</h5>
      <form id="editarForm">
        <div class="mb-3 text-start">
          <label class="form-label fw-bold">Nombre del sensor</label>
          <input type="text" id="sensorNombre" style="border: solid 1px;" class="form-control" placeholder="Nombre del sensor">
        </div>
        <div class="mb-3 text-start">
          <label class="form-label fw-bold">Descripción</label>
          <textarea id="sensorDescripcion" style="border: solid 1px;" class="form-control" rows="3" placeholder="Descripción del sensor"></textarea>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label fw-bold">Tipo de Sensor</label>
          <select id="sensorTipo" style="border: solid 1px;" class="form-control">
            <option value="">Seleccione un tipo</option>
            <option value="1">Movimiento</option>
            <option value="2">Puerta</option>
            <option value="3">Humo</option>
          </select>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label fw-bold">Estado de Asignación</label>
          <select id="sensorEstado" style="border: solid 1px;" class="form-control">
            <option value="">Seleccione un estado</option>
            <option value="1">Asignado</option>
            <option value="0">No Asignado</option>
          </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-primary" id="cancelarEdicion">Cancelar</button>
          <button type="submit" class="btn btn-secondary bg-secondary text-body">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap scripts -->
  <script src="/alarma_tec_1/frontend/js/bootstrap.bundle.js"></script>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));

    const sensoresContainer = document.getElementById('sensoresContainer');
    const idZona = <?php echo $id_zona; ?>;

    // Elementos del modal de detalles
    const modalDetalles = document.getElementById('modalDetalles');
    const cerrarDetalles = document.getElementById('cerrarDetalles');
    const detallesSensor = document.getElementById('detallesSensor');

    // Elementos del modal de editar
    const modalEditar = document.getElementById('modalEditar');
    const cancelarEdicion = document.getElementById('cancelarEdicion');
    const editarForm = document.getElementById('editarForm');
    const sensorNombre = document.getElementById('sensorNombre');
    const sensorDescripcion = document.getElementById('sensorDescripcion');
    const sensorTipo = document.getElementById('sensorTipo');
    const sensorEstado = document.getElementById('sensorEstado');

    let sensorActual = null;

    // Función: Cargar sensores dinámicamente
    async function cargarSensores() {
      try {
        const res = await fetch(`/alarma_tec_1/back-end/obtener_sensores.php?id_zona=${idZona}`);
        const data = await res.json();

        if (!data || data.length === 0) {
          sensoresContainer.innerHTML = '<p class="text-muted text-center">No hay sensores en esta zona.</p>';
          return;
        }

        sensoresContainer.innerHTML = '';

        data.forEach(sensor => {
          const activo = sensor.estado_asignacion == 1 && sensor.estado_sensor == 1;

          const col = document.createElement('div');
          col.className = 'col-6';

          col.innerHTML = `
            <div class="card ${activo ? '' : 'luzMala'}">
              <div class="card-body">
                <div class="d-flex gap-4 align-items-center mb-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-cpu me-2 fs-5"></i>
                    <h5 class="card-title mb-0">${sensor.nombre}</h5>
                  </div>
                  <div class="text-muted d-flex align-items-center">
                    <i class="bi ${activo ? 'bi-wifi' : 'bi-wifi-off'} me-2 fs-6"></i>
                    <small>${activo ? 'Conectado' : 'Sin acceso'}</small>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex gap-4">
                    <button class="btn btn-secondary bg-secondary text-body btn-sm btn-detalles" data-sensor='${JSON.stringify(sensor)}'>Detalles</button>
                    <button class="btn btn-outline-secondary btn-sm btn-editar-sensor" data-sensor='${JSON.stringify(sensor)}'>
                      <i class="bi bi-pencil me-1"></i>Editar
                    </button>
                  </div>
                  <div class="d-flex align-items-center gap-4">
                    <i class="bi bi-battery-full fs-5"></i>
                    <button class="rounded-circle bg-secondary btn-toggle" title="Encender/Apagar" data-id="${sensor.id_zona_sensor}">
                      <i class="bi bi-power fs-5 rounded-circle text-white"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `;

          sensoresContainer.appendChild(col);
        });

        agregarEventos();

      } catch (err) {
        console.error(err);
        sensoresContainer.innerHTML = '<p class="text-danger text-center">Error al cargar sensores.</p>';
      }
    }

    // Cambiar estado (activar/desactivar)
    function agregarEventos() {
      // Botón toggle
      document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.dataset.id;
          try {
            const res = await fetch('/alarma_tec_1/back-end/archivosensor.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({ id_zona_sensor: id })
            });
            const result = await res.json();
            if (result.success) cargarSensores();
            else alert('Error al cambiar estado del sensor.');
          } catch (e) {
            console.error(e);
            alert('Error al comunicarse con el servidor.');
          }
        });
      });

      // Botón detalles
      document.querySelectorAll('.btn-detalles').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const sensor = JSON.parse(btn.dataset.sensor);
          mostrarDetalles(sensor);
        });
      });

      // Botón editar
      document.querySelectorAll('.btn-editar-sensor').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const sensor = JSON.parse(btn.dataset.sensor);
          abrirModalEditar(sensor);
        });
      });
    }

    // Mostrar detalles del sensor
    function mostrarDetalles(sensor) {
      const formatearFecha = (fecha) => {
        if (!fecha) return 'No especificada';
        return new Date(fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
      };

      detallesSensor.innerHTML = `
        <p><strong>ID del Sensor:</strong> ${sensor.id_sensor}</p>
        <p><strong>Nombre:</strong> ${sensor.nombre}</p>
        <p><strong>Descripción:</strong> ${sensor.descripcion || 'No especificada'}</p>
        <p><strong>Tipo de Sensor:</strong> ${sensor.tipo_sensor || 'No especificado'}</p>
        <p><strong>Descripción del Tipo:</strong> ${sensor.descripcion_tipo || 'No especificada'}</p>
        <p><strong>Estado del Sensor:</strong> <span style="color: ${sensor.estado_sensor == 1 ? '#28a745' : '#dc3545'}; font-weight: bold;">${sensor.estado_sensor == 1 ? 'Activo' : 'Inactivo'}</span></p>
        <p><strong>Estado de Asignación:</strong> <span style="color: ${sensor.estado_asignacion == 1 ? '#28a745' : '#ffc107'}; font-weight: bold;">${sensor.estado_asignacion == 1 ? 'Asignado' : 'No Asignado'}</span></p>
        <p><strong>Fecha de Instalación:</strong> ${formatearFecha(sensor.fecha_instalacion)}</p>
        <p><strong>Fecha de Asignación:</strong> ${formatearFecha(sensor.fecha_asignacion)}</p>
      `;
      document.body.classList.add('blur-active');
      modalDetalles.style.display = 'flex';
    }

    // Abrir modal de edición
    function abrirModalEditar(sensor) {
      sensorActual = sensor;
      sensorNombre.value = sensor.nombre;
      sensorDescripcion.value = sensor.descripcion || '';
      sensorTipo.value = sensor.id_tipo_sensor || '';
      sensorEstado.value = sensor.estado_asignacion;

      document.body.classList.add('blur-active');
      modalEditar.style.display = 'flex';
    }

    // Cerrar modal de detalles
    cerrarDetalles.addEventListener('click', () => {
      modalDetalles.style.display = 'none';
      document.body.classList.remove('blur-active');
    });

    modalDetalles.addEventListener('click', (e) => {
      if (e.target === modalDetalles) {
        modalDetalles.style.display = 'none';
        document.body.classList.remove('blur-active');
      }
    });

    // Cerrar modal de edición
    cancelarEdicion.addEventListener('click', () => {
      modalEditar.style.display = 'none';
      document.body.classList.remove('blur-active');
      sensorActual = null;
    });

    modalEditar.addEventListener('click', (e) => {
      if (e.target === modalEditar) {
        modalEditar.style.display = 'none';
        document.body.classList.remove('blur-active');
        sensorActual = null;
      }
    });

    // Guardar cambios del sensor
    editarForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      if (!sensorActual) return;

      const nombre = sensorNombre.value.trim();
      const descripcion = sensorDescripcion.value.trim();
      const tipo = sensorTipo.value.trim();
      const estado = sensorEstado.value.trim();

      if (!nombre || tipo === '' || estado === '') {
        alert('Por favor complete todos los campos requeridos');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('id_zona_sensor', sensorActual.id_zona_sensor);
        formData.append('id_sensor', sensorActual.id_sensor);
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('id_tipo_sensor', tipo);
        formData.append('estado', estado);

        const response = await fetch('/alarma_tec_1/back-end/actualizar_sensor.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.ok) {
          alert('Sensor actualizado correctamente');
          modalEditar.style.display = 'none';
          document.body.classList.remove('blur-active');
          cargarSensores();
        } else {
          alert('Error: ' + result.mensaje);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar los cambios');
      }
    });

    cargarSensores();
  </script>

</body>
</html>
