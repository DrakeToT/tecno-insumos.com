document.addEventListener("DOMContentLoaded", () => {
    
    // Referencias DOM
    const selectTipo = document.getElementById("selectTipo");
    const selectEntidad = document.getElementById("selectEntidad");
    const formReporte = document.getElementById("formReporte");
    const btnConsultar = document.getElementById("btnConsultar");
    
    // Contenedores de resultados
    const cardResultados = document.getElementById("cardResultados");
    const lblNombre = document.getElementById("lblNombre");
    const lblTipo = document.getElementById("lblTipo");
    const lblExtra = document.getElementById("lblExtra");
    const lblTotal = document.getElementById("lblTotal");
    const tbody = document.getElementById("tbodyResultados");

    // Modal Historial
    const modalHistorialEl = document.getElementById("modalHistorial");
    const modalHistorial = new bootstrap.Modal(modalHistorialEl);
    const listaHistorial = document.getElementById("listaHistorial");
    const modalEquipoTitulo = document.getElementById("modalEquipoTitulo");

    // Modal Mensajes
    const modalMessage = new bootstrap.Modal(document.getElementById("modalMessage"));

    // ----------------------------------------------------------------
    // CARGA DINÁMICA DE SELECTORES
    // ----------------------------------------------------------------
    selectTipo.addEventListener("change", async (e) => {
        const tipo = e.target.value;
        selectEntidad.innerHTML = '<option value="" selected>Cargando...</option>';
        selectEntidad.disabled = true;
        cardResultados.classList.add("d-none"); // Ocultar resultados anteriores

        let url = "";
        // Mapear el tipo a la API existente correspondiente
        if (tipo === 'usuario') url = './api/index.php?users'; 
        if (tipo === 'empleado') url = './api/index.php?empleados';
        if (tipo === 'area') url = './api/index.php?areas';

        try {
            const response = await fetch(url);
            const result = await response.json();

            selectEntidad.innerHTML = '<option value="" selected disabled>Seleccione...</option>';

            if (result.success && result.data) {
                result.data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.id;
                    
                    // Formatear texto según lo que devuelva cada API
                    if(tipo === 'area') {
                        option.textContent = item.nombre;
                    } else {
                        // Usuarios y Empleados (mostrar nombre completo)
                        option.textContent = `${item.nombre} ${item.apellido || ''}`.trim();
                        // Agregar rol o puesto si existe
                        if(item.rol) option.textContent += ` (${item.rol})`;
                        if(item.puesto) option.textContent += ` (${item.puesto})`;
                    }
                    selectEntidad.appendChild(option);
                });
                selectEntidad.disabled = false;
            } else {
                mostrarError("No se pudieron cargar los datos.");
            }
        } catch (error) {
            console.error(error);
            mostrarError("Error de conexión al cargar listas.");
            selectEntidad.innerHTML = '<option value="">Error</option>';
        }
    });

    // ----------------------------------------------------------------
    // GENERACIÓN DEL REPORTE
    // ----------------------------------------------------------------
    formReporte.addEventListener("submit", async (e) => {
        e.preventDefault();

        const tipo = selectTipo.value;
        const id = selectEntidad.value;

        if(!tipo || !id) return;

        // UI Loading
        const txtOriginal = btnConsultar.innerHTML;
        btnConsultar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
        btnConsultar.disabled = true;
        cardResultados.classList.add("d-none");

        try {
            // Llamada al endpoint de Reportes
            const url = `./api/index.php?reportes&accion=asignaciones&tipo=${tipo}&id=${id}`;
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                renderizarReporte(result.data);
            } else {
                mostrarError(result.message || "No se encontraron datos.");
            }

        } catch (error) {
            console.error(error);
            mostrarError("Error al generar el reporte.");
        } finally {
            btnConsultar.innerHTML = txtOriginal;
            btnConsultar.disabled = false;
        }
    });

    function renderizarReporte(data) {
        // Datos de Cabecera
        const resp = data.responsable;
        lblNombre.textContent = resp.nombre_completo;
        lblTipo.textContent = resp.tipo_entidad;
        lblExtra.textContent = resp.dato_extra || '-';
        lblTotal.textContent = data.cantidad_equipos;

        // Tabla de Equipos
        tbody.innerHTML = "";
        
        if (data.equipos.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No tiene equipos asignados actualmente.</td></tr>`;
        } else {
            data.equipos.forEach(eq => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="fw-bold text-primary">${eq.codigo_inventario}</td>
                    <td>${eq.categoria}</td>
                    <td>${eq.marca} ${eq.modelo}</td>
                    <td><small class="text-muted">${eq.numero_serie || '-'}</small></td>
                    <td>${eq.fecha_adquisicion || '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info text-white btn-historial" title="Ver Movimientos">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </td>
                `;
                
                // Evento click para ver historial (Pasamos el objeto 'historial' que ya viene en el JSON)
                tr.querySelector(".btn-historial").addEventListener("click", () => {
                    mostrarModalHistorial(eq);
                });

                tbody.appendChild(tr);
            });
        }

        // Mostrar tarjeta
        cardResultados.classList.remove("d-none");
    }

    // ----------------------------------------------------------------
    // 3. MODAL DE HISTORIAL
    // ----------------------------------------------------------------
    function mostrarModalHistorial(equipo) {
        modalEquipoTitulo.textContent = `${equipo.categoria}: ${equipo.marca} ${equipo.modelo} (${equipo.codigo_inventario})`;
        listaHistorial.innerHTML = "";

        if (!equipo.historial || equipo.historial.length === 0) {
            listaHistorial.innerHTML = '<div class="list-group-item text-center text-muted">Sin movimientos registrados.</div>';
        } else {
            equipo.historial.forEach(mov => {
                // Definir color según tipo movimiento
                let colorIcon = "text-secondary";
                let icon = "bi-arrow-left-right"; // Default ajuste

                if(mov.tipo_movimiento === 'Alta') { colorIcon = "text-success"; icon = "bi-plus-circle"; }
                if(mov.tipo_movimiento === 'Asignacion') { colorIcon = "text-primary"; icon = "bi-person-check"; }
                if(mov.tipo_movimiento === 'Reparacion') { colorIcon = "text-warning"; icon = "bi-tools"; }
                if(mov.tipo_movimiento === 'Baja') { colorIcon = "text-danger"; icon = "bi-trash"; }

                const item = document.createElement("div");
                item.className = "list-group-item list-group-item-action";
                item.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 ${colorIcon}">
                            <i class="bi ${icon}"></i> ${mov.tipo_movimiento}
                        </h6>
                        <small class="text-muted">${mov.fecha}</small>
                    </div>
                    <p class="mb-1 small">${mov.observaciones || ''}</p>
                    <small class="text-muted fst-italic">Por: ${mov.usuario_nombre || 'Sistema'} ${mov.usuario_apellido || ''}</small>
                `;
                listaHistorial.appendChild(item);
            });
        }

        modalHistorial.show();
    }

    // Helper Mensajes
    function mostrarError(msg) {
        document.getElementById("modalMessageTitle").textContent = "Atención";
        document.getElementById("modalMessageBody").textContent = msg;
        document.getElementById("modalMessageHeader").className = "modal-header bg-warning text-dark";
        modalMessage.show();
    }
});