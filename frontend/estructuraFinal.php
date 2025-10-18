<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estructura</title>
  <link rel="stylesheet" href="css/bootstrap.custom.min.css">
  <link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
  <style>
    /* Ajuste de layout general */
    body {
      min-height: 100vh;
      display: grid;
      grid-template-rows: auto 1fr auto;
    }

    .sidebar {
      width: 80px;
      min-height: 100%;
    }

    .card {
      width: 100%;
      max-width: 400px;
    }

    /* Mensaje cuando no hay equipos */
    #noEquipos {
      display: none;
      width: 100%;
      text-align: center;
      color: #6c757d;
      font-size: 1.3rem;
      margin-top: 4rem;
    }

    /* Responsive: sidebar oculta en móvil */
    @media (max-width: 992px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        background-color: #6c757d;
        transition: left 0.3s ease;
        z-index: 1050;
      }
      .sidebar.show {
        left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <header>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <!-- Botón hamburguesa móvil -->
          <button class="btn btn-outline-light d-lg-none me-2" id="toggleSidebar">
            <i class="bi bi-list"></i>
          </button>
          <a class="navbar-brand" href="#">Alarma</a>
        </div>
        <div> 
          <a class="navbar-brand">Dashboard</a>
          <i class="bi bi-ui-checks-grid"></i>
        </div>
      </div>
    </nav>
  </header>

  <!-- MAIN -->
  <main>
    <div class="d-flex h-100">

      <!-- SIDEBAR -->
      <div class="bg-secondary sidebar d-flex flex-column align-items-center py-3">
        <ul class="nav nav-pills flex-column text-center">
          <li class="nav-item mb-3">
            <a href="#" class="nav-link text-white">
              <i class="bi bi-house-door fs-4"></i>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a href="#" class="nav-link text-white">
              <i class="bi bi-box fs-4"></i>
            </a>
          </li>
          <li class="nav-item mb-3">
            <a href="#" class="nav-link text-white">
              <i class="bi bi-record-circle fs-4"></i>
            </a>
          </li>
        </ul>
      </div>

      <!-- CONTENEDOR DE CARDS -->
      <div class="flex-grow-1 d-flex flex-wrap justify-content-center gap-3 p-3" id="cardsContainer">
        <!-- Se insertan dinámicamente -->
      </div>

      <!-- Mensaje si no hay equipos -->
      <div id="noEquipos">Sin Equipos Asignados</div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-primary text-light pt-5 pb-3 mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-md mb-4 text-center">
          <h5>Créditos</h5>
          <ul class="list-unstyled">
            <li class="text-light">Nombre Apellido</li>
          </ul>
        </div>

        <div class="col-md mb-4 text-center">
          <h5>Contacto</h5>
          <address class="text-light">
            Soporte: ejemplo@gmail.com<br>
            Tel: +54 9 11 3333-3333
          </address>
        </div>

        <div class="col-md mb-4 text-center">
          <h5>Síguenos</h5>
          <a href="https://www.facebook.com/tecnica1grandbourg/?locale=es_LA" class="text-light me-3"><i class="bi bi-facebook me-1"></i>Facebook</a><br>
          <a href="https://www.instagram.com/tecnica1malvinasarg/" class="text-light me-3"><i class="bi bi-instagram me-1"></i>Instagram</a><br>
        </div>
      </div>

      <hr class="bg-light">

      <div class="d-flex flex-column flex-md-row justify-content-between text-center">
        <p class="mb-2 mb-md-0">&copy; 2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos reservados.</p>
        <ul class="list-inline mb-0">
          <li class="list-inline-item"><a href="#" class="text-light">Privacidad</a></li>
          <li class="list-inline-item"><a href="#" class="text-light">Términos</a></li>
        </ul>
      </div>
    </div>
  </footer>

  <!-- JS -->
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const cardsContainer = document.getElementById('cardsContainer');
    const noEquiposMsg = document.getElementById('noEquipos');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    async function cargarEquipos() {
      try {
        // 🔹 Reemplazá esta URL con tu endpoint real
        const response = await fetch('../back-end/obtener_equipos.php');
        const data = await response.json();

        cardsContainer.innerHTML = ''; // limpiar
        if (!data || data.length === 0) {
          noEquiposMsg.style.display = 'block';
          return;
        }

        noEquiposMsg.style.display = 'none';

        data.forEach(equipo => {
        const card = document.createElement('div');
        card.className = 'card shadow-sm p-3';
        card.innerHTML = `
          <div class="d-flex align-items-start gap-3">
            <div class="d-flex flex-column gap-2">
              <i class="bi bi-house-door fs-2"></i>
              <i class="bi ${equipo.estado === 'activo' ? 'bi-wifi' : 'bi-wifi-off'} fs-2"></i>
            </div>
            <div class="d-flex flex-column">
              <h5 class="card-title mb-1">${equipo.nombre || 'Sin nombre'}:</h5>
              <p class="card-text text-muted">
                ${equipo.descripcion || 'Sin descripción disponible.'}
              </p>
              <a href="estructuraDashboardZonas.php?id_equipo=${equipo.id_equipo}" 
                class="btn btn-light border align-self-start">Entrar</a>
            </div>
          </div>
        `;
        cardsContainer.appendChild(card);
      });

      } catch (err) {
        console.error('Error al cargar equipos:', err);
        noEquiposMsg.textContent = 'Error al cargar los equipos.';
        noEquiposMsg.style.display = 'block';
      }
    }

    // Cargar al iniciar
    cargarEquipos();
  </script>
</body>
</html>
