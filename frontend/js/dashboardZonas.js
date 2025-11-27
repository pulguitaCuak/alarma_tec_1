// Configuración inicial y elementos DOM
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const zonasContainer = document.getElementById('zonasContainer');
const modal = document.getElementById('editModal');
const cancelEdit = document.getElementById('cancelEdit');
const form = document.getElementById('editForm');
const currentZoneId = document.getElementById('currentZoneId');
const currentZoneName = document.getElementById('currentZoneName');
const newZoneName = document.getElementById('newZoneName');
const zoneDescription = document.getElementById('zoneDescription');

let selectedCard = null;
let selectedZonaId = null;

// Sidebar toggle
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Cargar zonas dinámicamente
async function cargarZonas() {
    try {
        // Obtener ID del equipo desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        const idEquipo = urlParams.get('id_equipo');

        if (!idEquipo) {
            zonasContainer.innerHTML = '<div class="alert alert-danger col-12">ID de equipo no especificado</div>';
            return;
        }

        const res = await fetch(`/alarma_tec_1/back-end/obtener_zonas.php?id_equipo=${idEquipo}`);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();

        // Verificar si hay error en la respuesta
        if (data.error) {
            throw new Error(data.error);
        }

        if (!data || data.length === 0) {
            zonasContainer.innerHTML = '<p class="text-muted text-center" style="width: 100%;">No hay zonas disponibles para este equipo.</p>';
            return;
        }

        zonasContainer.innerHTML = '';

        data.forEach(zona => {
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';

            // Determinar clase CSS según el estado
            const estadoClase = zona.estado_general === 'Zona en Peligro' ? 'zona-peligro' : 'zona-normal';
            const estadoIcono = zona.estado_general === 'Zona en Peligro' ? 'bi-exclamation-triangle' : 'bi-check-circle';
            const estadoColor = zona.estado_general === 'Zona en Peligro' ? 'text-danger' : 'text-success';

            col.innerHTML = `
                <div class="card h-100 ${estadoClase}" data-id-zona="${zona.id_zona}">
                    <div class="card-body">
                        <div class="d-flex gap-4 align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-columns-gap me-2 fs-5"></i>
                                <h5 class="card-title mb-0">${zona.nombre_zona}</h5>
                            </div>
                            <div class="d-flex align-items-center ${estadoColor}">
                                <i class="bi ${estadoIcono} me-2 fs-6"></i>
                                <small>${zona.estado_general}</small>
                            </div>
                        </div>
                        <p class="card-text">${zona.descripcion || 'Sin descripción adicional'}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-4">
                                <button class="btn btn-secondary bg-secondary text-body btn-sm btn-entrar" data-id="${zona.id_zona}">
                                    Entrar
                                </button>
                                <button class="btn btn-outline-secondary btn-sm btn-editar">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <i class="bi bi-battery-full fs-5 text-muted"></i>
                                <button class="rounded-circle bg-secondary btn-power" style="width: 35px; height: 35px; border: none;">
                                    <i class="bi bi-power fs-6 text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            zonasContainer.appendChild(col);
        });

        agregarEventosZonas();

    } catch (err) {
        console.error('Error al cargar zonas:', err);
        zonasContainer.innerHTML = '<div class="alert alert-danger col-12">Error al cargar las zonas: ' + err.message + '</div>';
    }
}

// Agregar eventos a las zonas dinámicas
function agregarEventosZonas() {
    // Botón entrar
    document.querySelectorAll('.btn-entrar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const idZona = btn.dataset.id;
            entrarZona(idZona);
        });
    });

    // Botón editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const card = btn.closest('.card');
            const idZona = card.dataset.idZona;
            const nombre = card.querySelector('.card-title').textContent.trim();
            const descripcion = card.querySelector('.card-text').textContent.trim();
            
            abrirModalEditar(idZona, nombre, descripcion, card);
        });
    });

    // Botón power (encendido/apagado)
    document.querySelectorAll('.btn-power').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const card = btn.closest('.card');
            const idZona = card.dataset.idZona;
            toggleZona(idZona, btn);
        });
    });
}

// Función para entrar a una zona
function entrarZona(idZona) {
    // Aquí puedes redirigir a una página específica de la zona
    window.location.href = `dashboardSensores.html?id_zona=${idZona}`;
}

// Función para alternar estado de zona (encendido/apagado)
async function toggleZona(idZona, boton) {
    try {
        const icono = boton.querySelector('i');
        
        // Cambiar visualmente (esto sería temporal hasta confirmar con el backend)
        if (icono.classList.contains('text-white')) {
            icono.classList.remove('text-white');
            icono.classList.add('text-danger');
            boton.classList.remove('bg-secondary');
            boton.classList.add('bg-success');
        } else {
            icono.classList.remove('text-danger');
            icono.classList.add('text-white');
            boton.classList.remove('bg-success');
            boton.classList.add('bg-secondary');
        }

        console.log(`Zona ${idZona} toggleada`);

    } catch (err) {
        console.error('Error al cambiar estado de zona:', err);
        alert('Error al cambiar el estado de la zona');
    }
}

// === LÓGICA DEL MODAL EDITAR ===
function abrirModalEditar(idZona, nombre, descripcion, card) {
    selectedZonaId = idZona;
    selectedCard = card;
    
    currentZoneId.value = idZona;
    currentZoneName.value = nombre;
    newZoneName.value = nombre; // Por defecto, mantener el mismo nombre
    zoneDescription.value = descripcion;

    document.body.classList.add('blur-active');
    modal.style.display = 'flex';
}

// Cerrar modal
function cerrarModal() {
    modal.style.display = 'none';
    document.body.classList.remove('blur-active');
    selectedCard = null;
    selectedZonaId = null;
}

cancelEdit.addEventListener('click', cerrarModal);

modal.addEventListener('click', (e) => {
    if (e.target === modal) cerrarModal();
});

// Guardar cambios en la zona - USANDO EL PHP REAL
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nuevoNombre = newZoneName.value.trim();
    const nuevaDescripcion = zoneDescription.value.trim();

    // Validaciones
    if (!nuevoNombre && !nuevaDescripcion) {
        alert('Por favor ingrese al menos un nombre o descripción para actualizar');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_zona', selectedZonaId);
        
        // Solo enviar nombre_zona si se proporcionó un nuevo nombre
        if (nuevoNombre) {
            formData.append('nombre_zona', nuevoNombre);
        }
        
        // IMPORTANTE: El PHP actual no maneja la descripción por separado
        // Si necesitas guardar la descripción, deberías modificar el PHP
        // Por ahora, el campo 'nombre_zona' se usa para actualizar la descripción en la BD
        
        console.log('Enviando datos:', {
            id_zona: selectedZonaId,
            nombre_zona: nuevoNombre
        });

        const response = await fetch('/alarma_tec_1/back-end/actualizar_zona.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.ok) {
            // Actualizar la tarjeta en el frontend
            if (selectedCard) {
                if (nuevoNombre) {
                    selectedCard.querySelector('.card-title').textContent = nuevoNombre;
                }
                // La descripción no se actualiza porque el PHP no la maneja actualmente
                // selectedCard.querySelector('.card-text').textContent = nuevaDescripcion;
            }
            cerrarModal();
            
            // Mostrar mensaje de éxito
            mostrarMensaje('success', result.mensaje || 'Zona actualizada correctamente');
            
        } else {
            throw new Error(result.mensaje || 'Error desconocido al actualizar la zona');
        }
    } catch (error) {
        console.error('Error al guardar:', error);
        mostrarMensaje('error', 'Error al guardar los cambios: ' + error.message);
    }
});

// Función para mostrar mensajes al usuario
function mostrarMensaje(tipo, mensaje) {
    // Crear elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '1060';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Función para eliminar zona (opcional - si quieres agregar esta funcionalidad)
async function eliminarZona(idZona) {
    if (!confirm('¿Está seguro de que desea eliminar esta zona?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_zona', idZona);
        formData.append('accion', 'eliminar');

        const response = await fetch('/alarma_tec_1/back-end/actualizar_zona.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.ok) {
            mostrarMensaje('success', result.mensaje || 'Zona eliminada correctamente');
            // Recargar las zonas después de eliminar
            setTimeout(() => {
                cargarZonas();
            }, 1000);
        } else {
            throw new Error(result.mensaje || 'Error al eliminar la zona');
        }
    } catch (error) {
        console.error('Error al eliminar zona:', error);
        mostrarMensaje('error', 'Error al eliminar la zona: ' + error.message);
    }
}

// Inicializar la carga de zonas cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarZonas();
});