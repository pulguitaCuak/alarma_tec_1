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
    }

    .modal-content-custom {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      width: 90%;
      max-width: 420px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
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

<!-- ====== MODAL EDITAR ZONA ====== -->
<div id="modalOverlay" class="modal-overlay">
  <div class="modal-content-custom">
    <i class="bi bi-map me-2 fs-1" style="color: black;"></i>
    <h5>Editar Equipos</h5>
    <form id="editForm">
      <div class="mb-3 text-start">
        <label class="form-label fw-bold">Nombre actual</label>
        <input style="border: solid 1px;" type="text" id="oldName" class="form-control" readonly>
      </div>
      <div class="mb-3 text-start">
        <label class="form-label fw-bold">Nuevo nombre</label>
        <input type="text" id="newName" style="border: solid 1px;" class="form-control" placeholder="Ingrese el nuevo nombre">
      </div>
      <div class="mb-3 text-start">
        <label class="form-label fw-bold">Estado</label>
        <select id="newState" style="border: solid 1px;" class="form-control">
          <option value="">Seleccione un estado</option>
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
          <option value="Mantenimiento">Mantenimiento</option>
        </select>
      </div>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-primary" id="cancelEdit" style="margin-right: 60px;">Cancelar</button>
        <button class="btn btn-secondary bg-secondary text-body btn-sm" type="submit" class="btn btn-primary" style="margin-right: 60px;">Aceptar</button>
      </div>
    </form>
  </div>
</div>

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
                <p class="mb-2"><strong>Estado:</strong> ${zona.estado_general}</p>
                <div class="d-flex gap-2">
                  <button class="btn btn-outline-secondary btn-sm btn-editar-zona"><i class="bi bi-pencil me-1"></i>Editar</button>
                </div>
            `;

            // Event listener para editar
            const btnEditar = card.querySelector('.btn-editar-zona');
            btnEditar.addEventListener('click', (e) => {
                e.stopPropagation();
                abrirModalEditar(zona, card);
            });

            // Link a la página de sensores pasando el id_zona (sin incluir el botón)
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

// === LÓGICA DEL MODAL EDITAR ===
const modalOverlay = document.getElementById('modalOverlay');
const cancelEdit = document.getElementById('cancelEdit');
const form = document.getElementById('editForm');
const oldNameInput = document.getElementById('oldName');
const newNameInput = document.getElementById('newName');
const newStateInput = document.getElementById('newState');

let selectedCard = null;
let selectedZona = null;

function abrirModalEditar(zona, card) {
    selectedCard = card;
    selectedZona = zona;
    oldNameInput.value = zona.nombre_zona;
    newNameInput.value = '';
    newStateInput.value = '';
    
    document.body.classList.add('blur-active');
    modalOverlay.style.display = 'flex';
}

// Cerrar modal
function closeEditModal() {
    modalOverlay.style.display = 'none';
    document.body.classList.remove('blur-active');
    selectedCard = null;
    selectedZona = null;
}

cancelEdit.addEventListener('click', closeEditModal);
modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeEditModal();
});

// Guardar cambios
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const newName = newNameInput.value.trim();
    const newState = newStateInput.value.trim();

    // Validar que al menos se ingrese un nombre
    if (!newName && !newState) {
        alert('Por favor ingrese al menos un nombre o seleccione un estado');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_zona', selectedZona.id_zona);
        
        if (newName) {
            formData.append('nombre_zona', newName);
        }
        if (newState) {
            formData.append('estado_zona', newState);
        }

        const response = await fetch('../back-end/actualizar_zona.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.ok) {
            // Actualizar la tarjeta en el frontend
            if (newName) {
                selectedCard.querySelector('.card-title').textContent = newName;
            }
            if (newState) {
                const stateElement = selectedCard.querySelector('p');
                stateElement.innerHTML = `<strong>Estado:</strong> ${newState}`;
            }
            closeEditModal();
        } else {
            alert('Error: ' + result.mensaje);
        }
    } catch (error) {
        console.error('Error al guardar:', error);
        alert('Error al guardar los cambios');
    }
});

cargarZonas();
</script>

</body>
</html>
