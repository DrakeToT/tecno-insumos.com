document.addEventListener("DOMContentLoaded", () => {

    // ========================================================================
    // CONFIGURACIÓN Y REFERENCIAS DOM
    // ========================================================================
    
    // URLs de la API
    const API_URL_EQUIPOS = `./api/index.php?equipos`; 
    const API_URL_CATEGORIAS = `./api/index.php?categorias`;

    // Elementos del DOM - Pestaña Equipos
    const tbodyEquipos = document.querySelector("#tablaEquipos tbody");
    const inputBuscar = document.querySelector("#buscarEquipo");
    const btnNewEquipo = document.querySelector("#btnNewEquipo");
    const paginationEquipos = document.querySelector("#paginationEquipos");

    // Elementos del Modal Formulario
    const modalElement = document.querySelector("#modalEquipoForm");
    const modalEquipoForm = new bootstrap.Modal(modalElement);
    const formEquipo = document.querySelector("#formEquipo");
    const modalTitle = document.querySelector("#modalTitle span");
    const modalTitleIcon = document.querySelector("#modalTitleIcon");
    const selectCategoria = formEquipo.querySelector("select[name='id_categoria']");

    // Elementos de Modales Genéricos (Confirmación y Mensajes)
    const modalConfirmElement = document.querySelector("#modalConfirm");
    const modalConfirm = new bootstrap.Modal(modalConfirmElement);
    const modalMessageElement = document.querySelector("#modalMessage");
    const modalMessage = new bootstrap.Modal(modalMessageElement);

    // Templates
    const templateRow = document.querySelector("#equipoRowTemplate");
    const templateNull = document.querySelector("#equipoRowNullTemplate");

    // Estado de la aplicación
    let estadoApp = {
        accion: null, // 'crear' | 'editar'
        idEdicion: null,
        paginaActual: 1,
        limite: 10,
        busqueda: "",
        orden: "ASC",
        columnaOrden: "codigo_inventario"
    };

    let debounceTimer;

    // ========================================================================
    // INICIALIZACIÓN
    // ========================================================================
    
    init();

    function init() {
        cargarCategorias();
        cargarEquipos();
        setupListeners();
    }

    function setupListeners() {
        // Búsqueda con Debounce (espera a que dejes de escribir)
        inputBuscar.addEventListener("input", (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                estadoApp.busqueda = e.target.value.trim();
                estadoApp.paginaActual = 1; // Reset a página 1
                cargarEquipos();
            }, 400);
        });

        // Botón Nuevo Equipo
        btnNewEquipo.addEventListener("click", abrirModalCrear);

        // Guardar Formulario
        formEquipo.addEventListener("submit", manejarGuardado);

        // Ordenamiento en columnas (Sortable)
        document.querySelectorAll(".sortable").forEach(th => {
            th.addEventListener("click", () => {
                const columna = th.dataset.sort;
                // Alternar orden
                if (estadoApp.columnaOrden === columna) {
                    estadoApp.orden = estadoApp.orden === "ASC" ? "DESC" : "ASC";
                } else {
                    estadoApp.columnaOrden = columna;
                    estadoApp.orden = "ASC";
                }
                estadoApp.paginaActual = 1; // Reset a página 1
                cargarEquipos();
            });
        });
    }

    // ========================================================================
    // LÓGICA DE NEGOCIO (CRUD)
    // ========================================================================

    /**
     * Obtiene listado de equipos desde la API
     */
    async function cargarEquipos() {
        // Construir URL con parámetros query string
        const params = new URLSearchParams({
            "search": estadoApp.busqueda,
            "page": estadoApp.paginaActual,
            "limit": estadoApp.limite,
            "sort": estadoApp.columnaOrden,
            "order": estadoApp.orden
        });

        try {
            const response = await fetch(`${API_URL_EQUIPOS}&${params.toString()}`);
            const result = await response.json();
            renderizarTabla(result, paginationEquipos);
        } catch (error) {
            console.error("Error:", error);
            mostrarMensaje("Error de conexión al cargar equipos.", "Error", "danger");
        }
    }

    /**
     * Carga las categorías para el Select del formulario
     */
    function cargarCategorias() {
        fetch(API_URL_CATEGORIAS)
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    let option = new Option("Seleccione una categoría", "", true, true);
                    option.disabled = true;
                    selectCategoria.innerHTML = '';
                    selectCategoria.add(option);
                    result.data.forEach(cat => {
                        selectCategoria.add(new Option(cat.nombre, cat.id));
                    });
                }
            })
            .catch(err => console.error("Error cargando categorías", err));
    }

    /**
     * Maneja el evento Submit del formulario (Crear o Editar)
     */
    async function manejarGuardado(e) {
        e.preventDefault();
        limpiarValidaciones();

        // Validación HTML5 básica
        if (!formEquipo.checkValidity()) {
            e.stopPropagation();
            formEquipo.classList.add('was-validated');
            return;
        }

        const formData = new FormData(formEquipo);
        const dataObj = Object.fromEntries(formData.entries());

        // Determinar Verbo HTTP y URL
        const method = estadoApp.accion === "editar" ? "PUT" : "POST";
        
        try {
            const response = await fetch(API_URL_EQUIPOS, {
                method: method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dataObj)
            });

            const result = await response.json();

            if (result.success) {
                modalEquipoForm.hide();
                cargarEquipos(); // Recargar tabla
                mostrarMensaje(result.message, "Éxito", "success");
            } else {
                mostrarMensaje(result.message || "Error al guardar.", "Atención", "warning");
            }

        } catch (error) {
            console.error(error);
            mostrarMensaje("Error crítico al intentar guardar.", "Error", "danger");
        }
    }

    /**
     * Elimina un equipo
     */
    function eliminarEquipo(equipo) {
        // Configurar Modal de Confirmación
        document.querySelector("#modalConfirmMensaje").textContent = 
            `¿Confirma que desea eliminar el equipo "${equipo.marca} ${equipo.modelo}" (Cod: ${equipo.codigo_inventario})?`;
        
        modalConfirm.show();

        // Configurar botón "Aceptar" (clonarlo para limpiar listeners viejos)
        const btnAceptar = document.querySelector("#modalConfirmBtnAceptar");
        const nuevoBtn = btnAceptar.cloneNode(true);
        btnAceptar.parentNode.replaceChild(nuevoBtn, btnAceptar);

        // Acción al confirmar
        nuevoBtn.addEventListener("click", async () => {
            modalConfirm.hide();
            
            try {
                const response = await fetch(API_URL_EQUIPOS, {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: equipo.id })
                });
                
                const result = await response.json();

                if (result.success) {
                    cargarEquipos();
                    mostrarMensaje(result.message, "Eliminado", "success");
                } else {
                    mostrarMensaje(result.message, "Error", "danger");
                }
            } catch (error) {
                mostrarMensaje("No se pudo conectar con el servidor.", "Error", "danger");
            }
        });
    }

    // ========================================================================
    // RENDERIZADO UI
    // ========================================================================

    function renderizarTabla(inventario, paginationContainer) {
        tbodyEquipos.innerHTML = "";
        actualizarIndicadoresOrden();

        // Caso tabla vacía o error
        if (!inventario.success || !inventario.data || inventario.data.length === 0) {
            tbodyEquipos.appendChild(templateNull.content.cloneNode(true));
            paginationEquipos.innerHTML = "";
            return;
        }

        // Renderizar filas
        inventario.data.forEach(equipo => {
            const clone = templateRow.content.cloneNode(true);
            
            // Llenar datos
            clone.querySelector(".codigo").textContent = equipo.codigo_inventario;
            clone.querySelector(".categoria").textContent = equipo.categoria;
            clone.querySelector(".marca-modelo").textContent = `${equipo.marca} ${equipo.modelo}`;
            clone.querySelector(".serie").textContent = equipo.numero_serie || '-';
            clone.querySelector(".ubicacion").textContent = equipo.ubicacion_detalle || '-';

            // Badge Estado
            const badge = clone.querySelector(".estado span");
            badge.textContent = equipo.estado;
            badge.className = "badge"; // Reset
            
            const clasesMap = {
                'Disponible': 'bg-success',
                'Asignado': 'bg-primary',
                'En reparacion': 'bg-warning text-dark',
                'Baja': 'bg-danger'
            };
            // Obtenemos la clase o usamos el default
            const claseEstado = clasesMap[equipo.estado] || 'bg-secondary';
            
            // Asignamos el string completo a className
            badge.className = `badge ${claseEstado}`;

            // Botones de acción
            clone.querySelector(".btn-editar").addEventListener("click", () => abrirModalEditar(equipo.id));
            clone.querySelector(".btn-eliminar").addEventListener("click", () => eliminarEquipo(equipo));

            tbodyEquipos.appendChild(clone);
        });

        renderizarPaginacion(inventario.pagination, paginationContainer);
    }

    function renderizarPaginacion(pagination, paginationContainer) {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = "";        
        
        const { page, pages } = pagination;
        if (pages <= 1) return;

        // Generar botones (Anterior, Números, Siguiente)
        const crearBoton = (texto, iconClass, pagDestino, disabled = false, active = false) => {
            const btn = document.createElement("button");
            btn.className = `btn btn-sm ${active ? 'btn-dark' : 'btn-outline-dark'} mx-1 border-0`;
            btn.disabled = disabled;
            btn.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : texto;
            
            if (!disabled && !active) {
                btn.addEventListener("click", () => {
                    estadoApp.paginaActual = pagDestino;
                    cargarEquipos();
                });
            }
            return btn;
        };

        // Botón Anterior
        paginationContainer.appendChild(crearBoton("", "bi bi-chevron-left", page - 1, page <= 1));

        // Páginas
        for (let i = 1; i <= pages; i++) {
            paginationContainer.appendChild(crearBoton(i, null, i, false, i === page));
        }

        // Botón Siguiente
        paginationContainer.appendChild(crearBoton("", "bi bi-chevron-right", page + 1, page >= pages));
    }

    function actualizarIndicadoresOrden() {
        // Resetear todos los iconos a gris/invisible
        document.querySelectorAll(".sortable i").forEach(i => i.className = "bi bi-caret-up-fill opacity-0");
        
        // Activar el actual
        const thActivo = document.querySelector(`.sortable[data-sort="${estadoApp.columnaOrden}"] i`);
        if (thActivo) {
            thActivo.className = estadoApp.orden === "ASC" 
                ? "bi bi-caret-up-fill text-warning" 
                : "bi bi-caret-down-fill text-warning";
        }
    }

    // ========================================================================
    // MODALES AUXILIARES
    // ========================================================================

    function abrirModalCrear() {
        estadoApp.accion = "crear";
        estadoApp.idEdicion = null;
        
        formEquipo.reset();
        formEquipo.classList.remove('was-validated');
        limpiarValidaciones();
        
        // Títulos
        modalTitle.textContent = "Nuevo Equipo";
        modalTitleIcon.className = "bi bi-plus-circle";
        
        // Valor por defecto
        if(formEquipo.estado) formEquipo.estado.value = "Disponible";

        modalEquipoForm.show();
    }

    function abrirModalEditar(id) {
        // Fetch individual para asegurar datos frescos
        fetch(`${API_URL_EQUIPOS}&id=${id}`)
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    const eq = res.data;
                    estadoApp.accion = "editar";
                    estadoApp.idEdicion = eq.id;

                    // Llenar campos
                    formEquipo.id.value = eq.id;
                    formEquipo.codigo_inventario.value = eq.codigo_inventario;
                    formEquipo.id_categoria.value = eq.id_categoria;
                    formEquipo.marca.value = eq.marca;
                    formEquipo.modelo.value = eq.modelo;
                    formEquipo.numero_serie.value = eq.numero_serie;
                    formEquipo.estado.value = eq.estado;
                    formEquipo.ubicacion_detalle.value = eq.ubicacion_detalle;
                    
                    formEquipo.fecha_adquisicion.value = eq.fecha_adquisicion;
                    formEquipo.proveedor.value = eq.proveedor;
                    formEquipo.valor_compra.value = eq.valor_compra;
                    formEquipo.observaciones.value = eq.observaciones;

                    // Títulos
                    modalTitle.textContent = "Editar Equipo";
                    modalTitleIcon.className = "bi bi-pencil-square";
                    
                    formEquipo.classList.remove('was-validated');
                    limpiarValidaciones();
                    modalEquipoForm.show();
                } else {
                    mostrarMensaje("No se encontró el equipo.", "Error", "danger");
                }
            })
            .catch(err => console.error(err));
    }

    function limpiarValidaciones() {
        formEquipo.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    }

    function mostrarMensaje(texto, titulo, tipo) {
        document.querySelector("#modalMessageTitle").textContent = titulo;
        document.querySelector("#modalMessageBody").textContent = texto;
        
        const header = document.querySelector("#modalMessageHeader");
        header.className = `modal-header text-white bg-${tipo}`;
        
        modalMessage.show();
    }
});