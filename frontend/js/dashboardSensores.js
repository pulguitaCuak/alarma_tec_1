// Elementos DOM
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const sensoresContainer = document.getElementById('sensoresContainer');
const actividadesContainer = document.getElementById('actividadesContainer');
const modalOverlay = document.getElementById('modalOverlay');
const cancelEdit = document.getElementById('cancelEdit');
const editSensorForm = document.getElementById('editSensorForm');
const sensorOldInput = document.getElementById('sensor-old');
const sensorNewInput = document.getElementById('sensor-new');
const sensorIdInput = document.getElementById('sensorId');

let selectedCard = null;
let selectedSensorId = null;

// Sidebar toggle
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Cargar sensores dinámicamente
async function cargarSensores() {
    try {
        // Obtener ID de la zona desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        const idZona = urlParams.get('id_zona');

        if (!idZona) {
            sensoresContainer.innerHTML = '<div class="alert alert-danger col-12">ID de zona no especificado</div>';
            return;
        }

        const res = await fetch(`/alarma_tec_1/back-end/obtener_sensores_zona.php?id_zona=${idZona}`);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();

        // Verificar si hay error en la respuesta
        if (data.error) {
            throw new Error(data.error);
        }

        if (!data || data.length === 0) {
            sensoresContainer.innerHTML = '<p class="text-muted text-center" style="width: 100%;">No hay sensores disponibles para esta zona.</p>';
            return;
        }

        sensoresContainer.innerHTML = '';

data.forEach(sensor => {
    const col = document.createElement('div');
    col.className = 'card-item';

    // Determinar clase CSS según el estado - ESTO ES LO IMPORTANTE
    const estadoClase = sensor.estado == 1 ? 'sensor-activo' : 'sensor-inactivo';
    const estadoIcono = sensor.estado == 1 ? 'bi-wifi' : 'bi-wifi-off';
    const estadoTexto = sensor.estado == 1 ? 'Conectado' : 'Desconectado';
    const estadoColor = sensor.estado == 1 ? 'text-success' : 'text-danger';

    col.innerHTML = `
        <div class="card h-100 ${estadoClase}" data-id-sensor="${sensor.id_sensor}">
            <div class="card-body">
                <!-- El punto se genera automáticamente con CSS gracias a la clase sensor-activo/sensor-inactivo -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-radar me-2 fs-4"></i>
                        <div>
                            <h5 class="card-title mb-1">${sensor.nombre}</h5>
                            <div class="d-flex align-items-center ${estadoColor}">
                                <i class="bi ${estadoIcono} me-1 fs-6"></i>
                                <small class="fw-medium">${estadoTexto}</small>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-battery-full fs-5 text-muted"></i>
                </div>
                
                <p class="card-text">${sensor.descripcion || 'Este sensor no tiene descripción adicional.'}</p>
                
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <div class="d-flex gap-2">
                        <button class="btn btn-secondary bg-secondary text-body btn-sm btn-entrar" data-id="${sensor.id_sensor}">
                            <i class="bi bi-arrow-right me-1"></i>Entrar
                        </button>
                        <button class="btn btn-outline-secondary btn-sm btn-editar">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </button>
                    </div>
                    <button class="btn bg-secondary btn-power btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 36px; height: 36px;">
                        <i class="bi bi-power fs-6 text-white"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    sensoresContainer.appendChild(col);
});

        agregarEventosSensores();
        cargarActividades(idZona);

    } catch (err) {
        console.error('Error al cargar sensores:', err);
        sensoresContainer.innerHTML = '<div class="alert alert-danger col-12">Error al cargar los sensores: ' + err.message + '</div>';
    }
}

// Cargar actividades (placeholder)
async function cargarActividades(idZona) {
    try {
        // Por ahora, mostramos actividades de ejemplo
        const actividades = [
            { tipo: 'Movimiento', fecha: '19/8/2021', hora: '14:32', sensor: 'Sensor 1' },
            { tipo: 'Desactivación', fecha: '19/8/2021', hora: '15:32', sensor: 'Sensor 2' },
            { tipo: 'Activación', fecha: '20/8/2021', hora: '09:15', sensor: 'Sensor 1' }
        ];

        // Limpiar contenedor excepto el header
        const headerCard = actividadesContainer.querySelector('.activity-card');
        actividadesContainer.innerHTML = '';
        if (headerCard) {
            actividadesContainer.appendChild(headerCard);
        }

        if (!actividades || actividades.length === 0) {
            const noActivityCard = document.createElement('div');
            noActivityCard.className = 'activity-card';
            noActivityCard.innerHTML = '<p class="text-muted text-center">No hay actividades registradas.</p>';
            actividadesContainer.appendChild(noActivityCard);
            return;
        }

        actividades.forEach(actividad => {
            const activityCard = document.createElement('div');
            activityCard.className = 'activity-card';
            
            activityCard.innerHTML = `
                <div class="d-flex justify-content-between">
                    <h6 class="mb-1">${actividad.tipo}</h6>
                    <div class="d-flex gap-4">
                        <small class="mb-1">${actividad.fecha}</small>  
                        <small class="text-muted">${actividad.hora}</small>
                    </div>
                </div>
                <small class="text-muted">${actividad.sensor}</small>
            `;

            actividadesContainer.appendChild(activityCard);
        });

    } catch (err) {
        console.error('Error al cargar actividades:', err);
    }
}

// Agregar eventos a los sensores dinámicos
function agregarEventosSensores() {
    // Botón entrar
    document.querySelectorAll('.btn-entrar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const idSensor = btn.dataset.id;
            entrarSensor(idSensor);
        });
    });

    // Botón editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const card = btn.closest('.card');
            const idSensor = card.dataset.idSensor;
            const nombre = card.querySelector('.card-title').textContent.trim();
            
            abrirModalEditar(idSensor, nombre, card);
        });
    });
}

// Función para entrar a un sensor
function entrarSensor(idSensor) {
    // Aquí puedes redirigir a una página específica del sensor
    window.location.href = `dashboardSensor.html?id_sensor=${idSensor}`;
}

// === LÓGICA DEL MODAL EDITAR ===
function abrirModalEditar(idSensor, nombre, card) {
    selectedSensorId = idSensor;
    selectedCard = card;
    
    sensorIdInput.value = idSensor;
    sensorOldInput.value = nombre;
    sensorNewInput.value = nombre;

    document.body.classList.add('blur-active');
    modalOverlay.style.display = 'flex';
}

// Cerrar modal
function cerrarModal() {
    modalOverlay.style.display = 'none';
    document.body.classList.remove('blur-active');
    selectedCard = null;
    selectedSensorId = null;
}

cancelEdit.addEventListener('click', cerrarModal);

modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) cerrarModal();
});

// Guardar cambios en el sensor
editSensorForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nuevoNombre = sensorNewInput.value.trim();

    if (!nuevoNombre) {
        alert('Por favor ingrese un nombre para el sensor');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_sensor', selectedSensorId);
        formData.append('nombre', nuevoNombre);
        
        const response = await fetch('/alarma_tec_1/back-end/actualizar_sensor.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.ok) {
            // Actualizar la tarjeta en el frontend
            if (selectedCard) {
                selectedCard.querySelector('.card-title').textContent = nuevoNombre;
            }
            cerrarModal();
            alert('Sensor actualizado correctamente');
        } else {
            alert('Error: ' + result.mensaje);
        }
    } catch (error) {
        console.error('Error al guardar:', error);
        alert('Error al guardar los cambios');
    }
});

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarSensores();
});