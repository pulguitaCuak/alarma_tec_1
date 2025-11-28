// Elementos DOM
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const sensorContainer = document.getElementById('sensorContainer');
const actividadesContainer = document.getElementById('actividadesContainer');

// Sidebar toggle
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Cargar información del sensor específico
async function cargarSensor() {
    try {
        // Obtener ID del sensor desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        const idSensor = urlParams.get('id_sensor');

        if (!idSensor) {
            sensorContainer.innerHTML = '<div class="alert alert-danger">ID de sensor no especificado en la URL</div>';
            return;
        }

        const res = await fetch(`/alarma_tec_1/back-end/obtener_sensor.php?id=${idSensor}`);
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();

        // Verificar si hay error en la respuesta
        if (data.error) {
            throw new Error(data.error);
        }

        if (!data || data.length === 0) {
            sensorContainer.innerHTML = '<div class="alert alert-warning">Sensor no encontrado</div>';
            return;
        }

        const sensor = data[0];
        mostrarSensor(sensor);
        cargarActividadesSensor(idSensor);

    } catch (err) {
        console.error('Error al cargar sensor:', err);
        sensorContainer.innerHTML = `
            <div class="alert alert-danger">
                <h5>Error al cargar el sensor</h5>
                <p class="mb-0">${err.message}</p>
            </div>
        `;
    }
}

// Mostrar información del sensor en la interfaz
function mostrarSensor(sensor) {
    // Determinar clase CSS según el estado
    const estadoClase = sensor.estado == 1 ? 'sensor-activo' : 'sensor-inactivo';
    const estadoIcono = sensor.estado == 1 ? 'bi-wifi' : 'bi-wifi-off';
    const estadoTexto = sensor.estado == 1 ? 'Conectado' : 'Desconectado';
    const estadoColor = sensor.estado == 1 ? 'text-success' : 'text-danger';
    
    // Formatear fecha de instalación
    const fechaInstalacion = sensor.fecha_instalacion 
        ? new Date(sensor.fecha_instalacion).toLocaleDateString('es-ES')
        : 'No especificada';

    sensorContainer.innerHTML = `
        <div class="card sensor-card h-100 ${estadoClase}">
            <div class="card-body">
                <!-- Header con información básica -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-radar me-3 fs-2 text-primary"></i>
                        <div>
                            <h3 class="card-title mb-1">${sensor.nombre}</h3>
                            <div class="d-flex align-items-center ${estadoColor}">
                                <i class="bi ${estadoIcono} me-2 fs-5"></i>
                                <span class="fw-bold">${estadoTexto}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-secondary fs-6">ID: ${sensor.id_sensor}</span>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Descripción</h6>
                    <p class="card-text fs-6">${sensor.descripcion || 'Este sensor no tiene descripción adicional.'}</p>
                </div>

                <!-- Información detallada en grid -->
                <div class="sensor-info-grid">
                    <div class="info-item">
                        <div class="info-label">Tipo de Sensor</div>
                        <div class="info-value">${sensor.tipo_sensor || 'No especificado'}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Estado</div>
                        <div class="info-value ${estadoColor}">
                            <i class="bi ${estadoIcono} me-1"></i>${estadoTexto}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha de Instalación</div>
                        <div class="info-value">${fechaInstalacion}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ID Tipo</div>
                        <div class="info-value">${sensor.id_tipo_sensor || 'N/A'}</div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="editarSensor(${sensor.id_sensor})">
                            <i class="bi bi-pencil me-1"></i>Editar Sensor
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="verHistorial(${sensor.id_sensor})">
                            <i class="bi bi-clock-history me-1"></i>Ver Historial
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted">
                            <i class="bi bi-battery-full me-1"></i>
                            <small>Batería</small>
                        </div>
                        <button class="btn btn-outline-${sensor.estado == 1 ? 'danger' : 'success'} btn-sm" 
                                onclick="toggleSensor(${sensor.id_sensor}, ${sensor.estado})">
                            <i class="bi bi-power me-1"></i>
                            ${sensor.estado == 1 ? 'Desactivar' : 'Activar'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Cargar actividades del sensor específico
async function cargarActividadesSensor(idSensor) {
    try {
        // Por ahora, mostramos actividades de ejemplo específicas del sensor
        // En el futuro, podrías crear un endpoint como obtener_actividades_sensor.php
        const actividades = [
            { tipo: 'Activación del sensor', fecha: new Date().toLocaleDateString('es-ES'), hora: '14:32', descripcion: 'Sensor activado correctamente' },
            { tipo: 'Lectura de datos', fecha: new Date().toLocaleDateString('es-ES'), hora: '14:30', descripcion: 'Lectura de temperatura: 23.5°C' },
            { tipo: 'Verificación', fecha: new Date().toLocaleDateString('es-ES'), hora: '14:25', descripcion: 'Verificación automática completada' },
            { tipo: 'Inicialización', fecha: new Date().toLocaleDateString('es-ES'), hora: '14:20', descripcion: 'Sistema inicializado' }
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
            noActivityCard.innerHTML = '<p class="text-muted text-center">No hay actividades registradas para este sensor.</p>';
            actividadesContainer.appendChild(noActivityCard);
            return;
        }

        actividades.forEach(actividad => {
            const activityCard = document.createElement('div');
            activityCard.className = 'activity-card';
            
            activityCard.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${actividad.tipo}</h6>
                        <small class="text-muted">${actividad.descripcion}</small>
                    </div>
                    <div class="d-flex flex-column gap-1 text-end">
                        <small class="mb-1">${actividad.fecha}</small>  
                        <small class="text-muted">${actividad.hora}</small>
                    </div>
                </div>
            `;

            actividadesContainer.appendChild(activityCard);
        });

    } catch (err) {
        console.error('Error al cargar actividades:', err);
        const errorCard = document.createElement('div');
        errorCard.className = 'activity-card';
        errorCard.innerHTML = '<p class="text-danger">Error al cargar actividades</p>';
        actividadesContainer.appendChild(errorCard);
    }
}

// Funciones de acción (placeholder)
function editarSensor(idSensor) {
    alert(`Función en desarrollo: Editar sensor ${idSensor}`);
    // window.location.href = `editar_sensor.html?id=${idSensor}`;
}

function verHistorial(idSensor) {
    alert(`Función en desarrollo: Ver historial del sensor ${idSensor}`);
    // window.location.href = `historial_sensor.html?id=${idSensor}`;
}

async function toggleSensor(idSensor, estadoActual) {
    try {
        const nuevoEstado = estadoActual == 1 ? 0 : 1;
        const formData = new FormData();
        formData.append('id_sensor', idSensor);
        formData.append('estado', nuevoEstado);

        const response = await fetch('/alarma_tec_1/back-end/actualizar_sensor.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.ok) {
            alert(`Sensor ${nuevoEstado == 1 ? 'activado' : 'desactivado'} correctamente`);
            // Recargar la página para mostrar el nuevo estado
            cargarSensor();
        } else {
            alert('Error: ' + result.mensaje);
        }
    } catch (error) {
        console.error('Error al cambiar estado:', error);
        alert('Error al cambiar el estado del sensor');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarSensor();
});