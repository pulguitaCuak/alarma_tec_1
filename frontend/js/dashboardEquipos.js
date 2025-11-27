// Ocultar/mostrar enlace de administración según id_cargo en sesión (1 o 2)
    async function checkAdminLinkVisibility() {
      try {
        const res = await fetch('/alarma_tec_1/back-end/get_session_user.php');
        const data = await res.json();
        const link = document.getElementById('linkAdminUsuarios');

        if (!link) return;

        // Si el usuario tiene cargo 1 o 2, mostrar el enlace
        if (data && (data.cargo === 1 || data.cargo === 2 || data.cargo === '1' || data.cargo === '2')) {
          link.style.display = '';
        } else {
          link.style.display = 'none';
        }
      } catch (err) {
        // En caso de error, ocultar el enlace por seguridad
        const link = document.getElementById('linkAdminUsuarios');
        if (link) link.style.display = 'none';
        console.error('Error al obtener la sesión del usuario', err);
      }
    }
    checkAdminLinkVisibility();

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    const equiposContainer = document.getElementById('equiposContainer');

    // Cargar equipos dinámicamente
    async function cargarEquipos() {
      try {
        const res = await fetch('/alarma_tec_1/back-end/obtener_equipos.php');
        const data = await res.json();

        if (!data || data.length === 0) {
          equiposContainer.innerHTML = '<p class="text-muted text-center">No hay equipos disponibles.</p>';
          return;
        }

        equiposContainer.innerHTML = '';

        data.forEach(equipo => {
          const col = document.createElement('div');
          col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';

          col.innerHTML = `
            <div class="card h-100" data-id-equipo="${equipo.id_equipo}">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-house-door me-2 fs-5"></i>
                  <h5 class="card-title mb-0">${equipo.nombre}</h5>
                </div>
                <div class="d-flex align-items-center text-muted">
                  <i class="bi ${equipo.estado == 1 ? 'bi-wifi' : 'bi-wifi-off'} me-2 fs-5"></i>
                  <small>${equipo.estado == 1 ? 'Conectado' : 'Desconectado'}</small>
                </div>
                <p class="card-text">${equipo.descripcion || 'Sin descripción'}</p>
                <div class="d-flex gap-2">
                  <button class="btn btn-secondary bg-secondary text-body btn-sm btn-entrar" data-id="${equipo.id_equipo}">Entrar</button>
                  <button class="btn btn-outline-secondary btn-sm btn-editar"><i class="bi bi-pencil me-1"></i>Editar</button>
                </div>
              </div>
            </div>
          `;

          equiposContainer.appendChild(col);
        });

        agregarEventos();

      } catch (err) {
        console.error(err);
        equiposContainer.innerHTML = '<p class="text-danger text-center">Error al cargar equipos.</p>';
      }
    }

    // Agregar eventos
    function agregarEventos() {
      // Botón entrar
      document.querySelectorAll('.btn-entrar').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const idEquipo = btn.dataset.id;
          window.location.href = `dashboardZonas.html?id_equipo=${idEquipo}`;
        });
      });

      // Botón editar
      document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const card = btn.closest('.card');
          const idEquipo = card.dataset.idEquipo;
          const nombre = card.querySelector('.card-title').textContent.trim();
          const descripcion = card.querySelector('.card-text').textContent.trim();
          abrirModalEditar(idEquipo, nombre, descripcion, card);
        });
      });
    }

    // === LÓGICA DEL MODAL EDITAR ===
    const modalOverlay = document.getElementById('modalOverlay');
    const cancelEdit = document.getElementById('cancelEdit');
    const form = document.getElementById('editForm');
    const oldNameInput = document.getElementById('oldName');
    const newNameInput = document.getElementById('newName');
    const newDescriptionInput = document.getElementById('newDescription');

    let selectedCard = null;
    let selectedEquipoId = null;

    function abrirModalEditar(idEquipo, nombre, descripcion, card) {
      selectedEquipoId = idEquipo;
      selectedCard = card;
      oldNameInput.value = nombre;
      newNameInput.value = '';
      newDescriptionInput.value = descripcion;

      document.body.classList.add('blur-active');
      modalOverlay.style.display = 'flex';
    }

    // Cerrar modal
    function closeEditModal() {
      modalOverlay.style.display = 'none';
      document.body.classList.remove('blur-active');
      selectedCard = null;
      selectedEquipoId = null;
    }
    cancelEdit.addEventListener('click', closeEditModal);
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeEditModal();
    });

    // Guardar cambios
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const newName = newNameInput.value.trim();
      const newDesc = newDescriptionInput.value.trim();

      if (!newName && !newDesc) {
        alert('Por favor ingrese al menos un nombre o descripción');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('id_equipo', selectedEquipoId);
        
        if (newName) {
          formData.append('nombre', newName);
        }
        if (newDesc) {
          formData.append('descripcion', newDesc);
        }

        const response = await fetch('/alarma_tec_1/back-end/actualizar_equipo.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.ok) {
          // Actualizar la tarjeta en el frontend
          if (newName) {
            selectedCard.querySelector('.card-title').textContent = newName;
          }
          if (newDesc) {
            selectedCard.querySelector('.card-text').textContent = newDesc;
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

    cargarEquipos();