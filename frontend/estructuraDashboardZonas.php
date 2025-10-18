<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.html");
    exit;
}

require_once "../back-end/db.php"; // Conexión PDO

if (!isset($_GET['id_equipo'])) {
    die("Falta id_equipo en la URL");
}

$id_equipo = intval($_GET['id_equipo']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Zonas</title>
<link rel="stylesheet" href="css/bootstrap.custom.min.css">
<link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
<style>
    body { min-height: 100vh; display: flex; flex-direction: column; }
    .sidebar { width: 80px; min-height: 100vh; }
    #zonasContainer {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        max-height: 650px;
        overflow-y: auto;
        padding: 1rem;
    }
    .card { cursor: pointer; transition: transform 0.2s; }
    .card:hover { transform: translateY(-3px); }
    .sensor { display: inline-flex; align-items: center; gap: 0.3rem; margin-right: 0.5rem; margin-bottom: 0.3rem; }
    #zonasContainer::-webkit-scrollbar { width: 8px; }
    #zonasContainer::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 4px; }
</style>
</head>
<body>

<header class="bg-primary text-white py-3 d-flex justify-content-between px-4">
  <h1 class="h4 m-0">Dashboard Zonas</h1>
  <div>Usuario: <?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?></div>
</header>

<div class="d-flex flex-grow-1">
  <aside class="sidebar bg-secondary d-flex flex-column align-items-center py-3">
    <a href="estructuraFinal.php" class="mb-3 text-white"><i class="bi bi-arrow-left fs-4"></i></a>
    <a href="#" class="mb-3 text-white"><i class="bi bi-house fs-4"></i></a>
    <a href="#" class="mb-3 text-white"><i class="bi bi-gear fs-4"></i></a>
  </aside>

  <main class="flex-grow-1">
    <div class="container-fluid">
      <h3 class="my-3">Zonas del Equipo</h3>
      <div id="zonasContainer"></div>
    </div>
  </main>
</div>

<footer class="bg-primary text-white text-center py-2 mt-auto">
  <small>&copy; 2025 Escuela Técnica N°1 Gral. San Martín Inc.</small>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
const zonasContainer = document.getElementById('zonasContainer');
const idEquipo = <?php echo $id_equipo; ?>;

async function cargarZonas() {
    try {
        const res = await fetch(`../back-end/obtener_zonas.php?id_equipo=${idEquipo}`);
        const data = await res.json();

        if (!data || data.length === 0) {
            zonasContainer.innerHTML = '<p class="text-muted text-center">No hay zonas asignadas a este equipo.</p>';
            return;
        }

        zonasContainer.innerHTML = '';

        data.forEach(zona => {
            const card = document.createElement('div');
            card.className = 'card shadow-sm p-3';
            card.innerHTML = `
                <h5 class="card-title mb-2">${zona.nombre_zona}</h5>
                <p class="mb-0"><strong>Estado:</strong> ${zona.estado_general}</p>
            `;

            // Link a la página de sensores pasando el id_zona
            card.addEventListener('click', () => {
                window.location.href = `estructuraDashboardSensor.php?id_zona=${zona.id_zona}`;
            });

            zonasContainer.appendChild(card);
        });
    } catch(err) {
        console.error(err);
        zonasContainer.innerHTML = '<p class="text-danger text-center">Error al cargar las zonas.</p>';
    }
}

cargarZonas();
</script>

</body>
</html>
