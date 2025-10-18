<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.html");
    exit;
}

require_once "../back-end/db.php"; // Conexión PDO

if (!isset($_GET['id_zona'])) {
    die("Falta id_zona en la URL");
}
$id_zona = intval($_GET['id_zona']);
$id_equipo = isset($_GET['id_equipo']) ? intval($_GET['id_equipo']) : 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Sensores</title>
<link rel="stylesheet" href="css/bootstrap.custom.min.css">
<link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
<style>
    body { min-height: 100vh; display: flex; flex-direction: column; }
    .sidebar { width: 80px; min-height: 100vh; }
    #sensoresContainer {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        max-height: 650px;
        overflow-y: auto;
        padding: 1rem;
    }
    .card { cursor: pointer; transition: transform 0.2s; }
    .card:hover { transform: translateY(-3px); }
    .btn-toggle { min-width: 80px; }
    #sensoresContainer::-webkit-scrollbar { width: 8px; }
    #sensoresContainer::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 4px; }
</style>
</head>
<body>

<header class="bg-primary text-white py-3 d-flex justify-content-between px-4">
  <h1 class="h4 m-0">Sensores de la Zona</h1>
  <div>Usuario: <?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?></div>
</header>

<div class="d-flex flex-grow-1">
  <aside class="sidebar bg-secondary d-flex flex-column align-items-center py-3">
    <a href="estructuraDashboardZonas.php?id_equipo=<?php echo $id_equipo; ?>" class="mb-3 text-white">
    <i class="bi bi-arrow-left fs-4"></i>
    </a>
    <a href="#" class="mb-3 text-white"><i class="bi bi-house fs-4"></i></a>
    <a href="#" class="mb-3 text-white"><i class="bi bi-gear fs-4"></i></a>
  </aside>

  <main class="flex-grow-1">
    <div class="container-fluid">
      <h3 class="my-3">Sensores asignados a la zona</h3>
      <div id="sensoresContainer"></div>
    </div>
  </main>
</div>

<footer class="bg-primary text-white text-center py-2 mt-auto">
  <small>&copy; 2025 Escuela Técnica N°1 Gral. San Martín Inc.</small>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
const sensoresContainer = document.getElementById('sensoresContainer');
const idZona = <?php echo $id_zona; ?>;

// Función para cargar sensores
async function cargarSensores() {
    try {
        const res = await fetch(`../back-end/obtener_sensores.php?id_zona=${idZona}`);
        const data = await res.json();

        if (!data || data.length === 0) {
            sensoresContainer.innerHTML = '<p class="text-muted text-center">No hay sensores asignados a esta zona.</p>';
            return;
        }

        sensoresContainer.innerHTML = '';

        data.forEach(sensor => {
            const card = document.createElement('div');
            card.className = 'card shadow-sm p-3';
            card.innerHTML = `
                <h5 class="card-title mb-2">${sensor.nombre}</h5>
                <p class="card-text">Estado: <span class="fw-bold">${sensor.estado_asignacion == 1 && sensor.estado_sensor == 1 ? 'Activo' : 'Inactivo'}</span></p>
                <button class="btn btn-sm btn-toggle ${sensor.estado_asignacion == 1 && sensor.estado_sensor == 1 ? 'btn-danger' : 'btn-success'}">
                    ${sensor.estado_asignacion == 1 && sensor.estado_sensor == 1 ? 'Desactivar' : 'Activar'}
                </button>
            `;

            // Botón toggle
            const btn = card.querySelector('.btn-toggle');
            btn.addEventListener('click', async () => {
                try {
                    const resToggle = await fetch(`../back-end/archivosensor.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_zona_sensor: sensor.id_zona_sensor })
                    });
                    const result = await resToggle.json();
                    if(result.success){
                        cargarSensores(); // recarga los sensores
                    } else {
                        alert('Error al cambiar estado del sensor');
                    }
                } catch(err) {
                    console.error(err);
                    alert('Error al comunicarse con el servidor');
                }
            });

            sensoresContainer.appendChild(card);
        });

    } catch(err) {
        console.error(err);
        sensoresContainer.innerHTML = '<p class="text-danger text-center">Error al cargar los sensores.</p>';
    }
}

cargarSensores();
</script>
</body>
</html>
