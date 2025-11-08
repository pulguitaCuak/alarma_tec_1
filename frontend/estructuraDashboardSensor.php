<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.html");
    exit;
}

require_once "../back-end/db.php";

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
  <link rel="stylesheet" href="css/bootstrap.custom.min.css">
  <link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
  <!-- Estilos del dashboard principal -->
  <link rel="stylesheet" href="css/plantillaPrincipal.css">
  <link rel="stylesheet" href="css/estructuraDasboardSensores.css">
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
      <aside id="sidebar" class="sidebar bg-dark d-flex flex-column align-items-center py-4 min-vh-100">
        <a href="estructuraDashboardZonas.php?id_equipo=<?php echo $id_equipo; ?>" class="my-3" title="Volver">
          <i class="bi bi-arrow-left-circle fs-4 p-4"></i>
        </a>
        <a href="#" class="my-3"><i class="bi bi-house fs-4 p-4"></i></a>
        <a href="#" class="my-3"><i class="bi bi-person-gear fs-4 p-4"></i></a>
        <a href="#" class="my-3"><i class="bi bi-telephone fs-4 p-4"></i></a>
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

  <!-- Bootstrap scripts -->
  <script src="js/bootstrap.bundle.min.js"></script>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));

    const sensoresContainer = document.getElementById('sensoresContainer');
    const idZona = <?php echo $id_zona; ?>;

    // Función: Cargar sensores dinámicamente
    async function cargarSensores() {
      try {
        const res = await fetch(`../back-end/obtener_sensores.php?id_zona=${idZona}`);
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
                    <button class="btn btn-secondary bg-secondary text-body btn-sm">Detalles</button>
                    <button class="btn btn-outline-secondary btn-sm editar-sensor" data-id="${sensor.id_zona_sensor}">
                      Editar
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
      document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.dataset.id;
          try {
            const res = await fetch('../back-end/archivosensor.php', {
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
    }

    cargarSensores();
  </script>

</body>
</html>
