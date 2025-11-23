document.addEventListener("DOMContentLoaded", () => {

    const tbodyRoles = document.querySelector("#tablaRoles tbody");
    const inputBuscarRol = document.querySelector("#buscarRol");

    const modalRoleForm = new bootstrap.Modal("#modalRoleForm");
    const modalRolePerms = new bootstrap.Modal("#modalRolePerms");
    const modalConfirm = new bootstrap.Modal("#modalConfirm");
    const modalMessage = new bootstrap.Modal("#modalMessage");

    const formRol = document.querySelector("#formRol");
    const modalTitle = document.querySelector("#modalTitle");
    const modalTitleIcon = document.querySelector("#modalTitleIcon");

    const templateRow = document.querySelector("#roleRowTemplate");
    const templateNull = document.querySelector("#roleRowNullTemplate");

    const disponibles = document.getElementById("listaDisponibles");
    const asignados = document.getElementById("listaAsignados");

    // Variables de acción (crear/editar)
    let tipoAccion = null;
    let idRolAccion = null;

    // Variables de paginación y orden
    let currentSort = "id";
    let currentOrder = "ASC";
    let currentPage = 1;
    let currentLimit = 10;

    let debounceTimer;

    cargarRoles();

    // Activar búsqueda dinámica en ambos select (Permisos)
    aplicarBusquedaDinamica("searchDisponibles", "listaDisponibles");
    aplicarBusquedaDinamica("searchAsignados", "listaAsignados");

    // Buscar roles al escribir
    inputBuscarRol.addEventListener("input", () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            cargarRoles(inputBuscarRol.value.trim());
        }, 300); // Delay de respuesta
    });

    // Busqueda dínamica de permisos
    function aplicarBusquedaDinamica(inputId, selectId) {
        const input = document.getElementById(inputId);
        const select = document.getElementById(selectId);

        input.addEventListener("input", () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const filtro = input.value.trim().toLowerCase();

                [...select.options].forEach(opt => {
                    // Mostrar/ocultar según el filtro
                    opt.style.display = opt.textContent.toLowerCase().includes(filtro) ? "" : "none";
                });
            }, 300); // Delay de respuesta
        });
    }

    // Ordenamiento por columnas
    document.querySelectorAll(".sortable").forEach(col => {
        col.addEventListener("click", () => {
            const sortField = col.dataset.sort;

            if (currentSort === sortField) {
                currentOrder = currentOrder === "ASC" ? "DESC" : "ASC";
            } else {
                currentSort = sortField;
                currentOrder = "ASC";
            }

            cargarRoles(inputBuscarRol.value.trim());
        });
    });

    // Cargar lista de roles desde la API
    function cargarRoles(search = "", sort = currentSort, order = currentOrder, page = currentPage, limit = currentLimit) {
        const url = `./api/index.php?roles&search=${encodeURIComponent(search)}&sort=${sort}&order=${order}&page=${page}&limit=${limit}`;

        fetch(url)
            .then(response => response.json())
            .then(result => {
                tbodyRoles.innerHTML = "";

                if (!result.success || !result.data || result.data.length === 0) {
                    tbodyRoles.appendChild(templateNull.content.cloneNode(true));
                    document.querySelector("#paginationContainer").innerHTML = "";
                    return;
                }

                result.data.forEach(rol => {
                    const row = templateRow.content.cloneNode(true);

                    row.querySelector(".id").textContent = rol.id;
                    row.querySelector(".nombre").textContent = rol.nombre;
                    row.querySelector(".descripcion").textContent = rol.descripcion ?? "";

                    // Estado
                    const badge = row.querySelector(".estado span");
                    badge.textContent = rol.estado;
                    badge.classList.add(rol.estado === "Activo" ? "bg-success" : "bg-secondary");

                    // Botón editar rol
                    row.querySelector(".btn-editar").addEventListener("click", () => {
                        confirmarEditarRol(rol);
                    });

                    // Botón cambiar estado
                    row.querySelector(".btn-estado").addEventListener("click", () => {
                        confirmarCambioEstado(rol);
                    });

                    row.querySelector(".btn-permisos").addEventListener("click", () => {
                        confirmarCambioPermisos(rol);
                    });

                    // Botón eliminar rol
                    row.querySelector(".btn-eliminar").addEventListener("click", () => {
                        confirmarEliminacionRol(rol);
                    });

                    tbodyRoles.appendChild(row);
                });

                renderPagination(result.pagination);
                actualizarIndicadoresOrden();
            })
            .catch(err => console.error("Error cargando roles:", err));
    }

    // Renderiza las páginas (ROL)
    function renderPagination(pagination) {
        const container = document.querySelector("#paginationContainer");
        container.innerHTML = "";

        const { page, pages } = pagination; // Variables estandarizadas

        if (pages <= 1) return;

        // Función auxiliar para crear botones
        const crearBoton = (texto, iconClass, pagDestino, disabled = false, active = false) => {
            const btn = document.createElement("button");
            btn.className = `btn btn-sm ${active ? 'btn-dark' : 'btn-outline-dark'} mx-1 border-0`;
            btn.disabled = disabled;
            btn.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : texto;
            
            if (!disabled && !active) {
                btn.addEventListener("click", () => {
                    currentPage = pagDestino;
                    cargarRoles(inputBuscarRol.value.trim());
                });
            }
            return btn;
        };

        // Botón Anterior
        container.appendChild(crearBoton("", "bi bi-chevron-left", page - 1, page <= 1));

        // Botones numéricos
        for (let i = 1; i <= pages; i++) {
            container.appendChild(crearBoton(i, null, i, false, i === page));
        }

        // Botón Siguiente
        container.appendChild(crearBoton("", "bi bi-chevron-right", page + 1, page >= pages));
    }


    // Actualiza el icono que indica cómo se está ordenando la lista de roles
    function actualizarIndicadoresOrden() {
        // Resetear todos los íconos
        document.querySelectorAll(".sortable i").forEach(icon => {
            icon.className = "bi bi-caret-up-fill opacity-0"; // invisible, reserva espacio
        });

        // Buscar la columna activa
        const colActiva = document.querySelector(`.sortable[data-sort="${currentSort}"] i`);
        if (colActiva) {
            colActiva.className = (currentOrder === "ASC")
                ? "bi bi-caret-up-fill text-warning"
                : "bi bi-caret-down-fill text-warning";
        }
    }

    // ---------- MODAL CREAR ROL ----------
    document.querySelector("#btnNewRol").addEventListener("click", () => {
        tipoAccion = "nuevo";
        idRolAccion = null;

        const textNode = document.createTextNode('');
        textNode.textContent = " Nuevo Rol";

        const icon = modalTitleIcon;
        icon.classList = "bi bi-plus-circle";

        modalTitle.textContent = "";
        modalTitle.appendChild(icon);
        modalTitle.appendChild(textNode);

        formRol.reset();
        limpiarValidaciones();

        // Eliminar campo estado si existe
        const estadoDiv = document.querySelector("#estadoRol")?.parentElement;
        if (estadoDiv) estadoDiv.remove();

        modalRoleForm.show();
    });

    // ---------- MODAL EDITAR ROL ----------
    function abrirModalEditarRol(rol) {
        tipoAccion = "editar";
        idRolAccion = rol.id;

        const textNode = document.createTextNode('');
        textNode.textContent = " Editar Rol";

        const icon = modalTitleIcon;
        icon.classList = "bi bi-pencil-square";

        modalTitle.textContent = "";
        modalTitle.appendChild(icon);
        modalTitle.appendChild(textNode);

        formRol.nombre.value = rol.nombre;
        formRol.descripcion.value = rol.descripcion;

        // Agregamos el select de estado dinámicamente solo si no existe
        if (!document.querySelector("#estadoRol")) {
            const div = document.createElement("div");
            div.classList.add("mb-3");

            const label = document.createElement('label');
            label.setAttribute("for", "estadoRol");
            label.classList = "form-label";
            label.textContent = "Estado";

            const select = document.createElement('select');
            select.id = "estadoRol";
            select.name = "estado";
            select.classList = "form-select";

            select.add(new Option("Activo", "Activo"));
            select.add(new Option("Inactivo", "Inactivo"));

            div.appendChild(label);
            div.appendChild(select);

            formRol.querySelector(".modal-body").appendChild(div);
        }

        document.querySelector("#estadoRol").value = rol.estado;

        limpiarValidaciones();
        modalRoleForm.show();
    }

    // ---------- GUARDAR ROL ----------
    formRol.addEventListener("submit", async e => {
        e.preventDefault();

        limpiarValidaciones();

        const formData = {
            nombre: formRol.nombre.value.trim(),
            descripcion: formRol.descripcion.value.trim(),
            ...(tipoAccion === "editar" ? {
                id: idRolAccion,
                estado: document.querySelector("#estadoRol")?.value
            } : {})
        };

        // Validación si tiene errores
        let hasError = false;

        if (!formData.nombre) {
            const input = formRol.querySelector("[name='nombre']");
            input.classList.add("is-invalid");
            input.parentElement.querySelector(".invalid-feedback").textContent = "El nombre es obligatorio.";
            hasError = true;
        }

        if (!formData.descripcion || formData.descripcion.length < 5) {
            const input = formRol.querySelector("[name='descripcion']");
            input.classList.add("is-invalid");
            input.parentElement.querySelector(".invalid-feedback").textContent = "La descripción debe tener al menos 5 caracteres.";
            hasError = true;
        }

        if (hasError) return;

        // Ajustar método según acción
        let method = tipoAccion === "editar" ? "PUT" : "POST";
        
        try {
            const response = await fetch("./api/index.php?roles", {
                method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            });
    
            const data = await response.json();
    
            // Procesar errores del backend
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, message]) => {
                    const input = formRol.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add("is-invalid");
                        const feedback = input.parentElement.querySelector(".invalid-feedback");
                        if (feedback) feedback.textContent = message;
                    }
                });
            }
    
            if (data.success) {
                modalRoleForm.hide();
                cargarRoles(inputBuscarRol.value.trim());
                mostrarMensaje(data.message, "Gestión de Roles", "success");
            }
            
        } catch (error) {
            console.error("Error al guardar el rol:", error);    
        }
    });

    // ---------- CONFIRMAR EDICIÓN DE ROL ----------
    function confirmarEditarRol(rol) {
        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Está seguro de editar el rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            modalConfirm.hide();
            abrirModalEditarRol(rol);
        };
    };

    // ---------- CONFIRMAR CAMBIO DE ESTADO ----------
    function confirmarCambioEstado(rol) {

        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Está seguro de cambiar el estado del rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            const nuevoEstado = rol.estado === "Activo" ? "Inactivo" : "Activo";
            
            try {
                const response = await fetch("./api/index.php?roles", {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: rol.id,
                        nombre: rol.nombre,
                        descripcion: rol.descripcion,
                        estado: nuevoEstado
                    })
                });
    
                const data = await response.json();
                mostrarMensaje(data.message, "Estado actualizado", data.success ? "success" : "danger");
    
                if (data.success) {
                    cargarRoles(inputBuscarRol.value.trim());
                    modalConfirm.hide();
                }
                
            } catch (error) {
                console.error("Error al cambiar el estado del rol:", error);    
            }
        };
    }

    // ---------- CONFIRMAR CAMBIO DE PERMISOS ----------
    function confirmarCambioPermisos(rol) {
        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Está seguro de cambiar los permisos del rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            modalConfirm.hide();
            abrirModalPermisos(rol.id);
        };
    };

    // ---------- CONFIRMAR ELIMINACIÓN ----------
    function confirmarEliminacionRol(rol) {
        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Desea eliminar el rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            
            try {
                const response = await fetch("./api/index.php?roles", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: rol.id })
                });
    
                const data = await response.json();
                mostrarMensaje(data.message, "Eliminar rol", data.success ? "success" : "danger");
    
                if (data.success) {
                    cargarRoles(inputBuscarRol.value.trim());
                    modalConfirm.hide();
                }
            } catch (error) {
                console.error("Error al eliminar el rol:", error);
            }
        };
    }

    // ---------- MODAL PERMISOS ----------
    async function abrirModalPermisos(idRol) {

        try {
            const response = await fetch(`./api/index.php?roles&id=${idRol}`);
            const result = await response.json();
    
            if (!result.success) {
                mostrarMensaje(result.message, "Error", "danger");
                return;
            }
    
            idRolAccion = idRol;
    
            document.querySelector("#nombreRolPerms").textContent = result.data.nombre;
    
            const listaDisponibles = document.querySelector("#listaDisponibles");
            const listaAsignados = document.querySelector("#listaAsignados");
    
            // Limpiar contenido previo
            listaDisponibles.innerHTML = "";
            listaAsignados.innerHTML = "";
    
            // Recorrer permisos y distribuirlos
            result.permisos.disponibles.forEach(perm => {
                const option = document.createElement("option");
                option.value = perm.id;
                option.textContent = perm.nombre;
                option.title = perm.descripcion; // tooltip con descripción
    
                if (result.permisos.asignados.includes(perm.id)) {
                    listaAsignados.appendChild(option);
                } else {
                    listaDisponibles.appendChild(option);
                }
            });
    
            modalRolePerms.show();
            
        } catch (error) {
            console.error("Error al cargar los permisos del rol:", error);
        }
    }

    // ---------- GUARDAR PERMISOS ----------
    document.querySelector("#btnSavePerms").addEventListener("click", async () => {
        // Tomo todas las opciones del select de asignados
        const asignados = document.querySelector("#listaAsignados");

        // Extraigo los IDs (value) de cada opción
        const permisos = [...asignados.options].map(opt => parseInt(opt.value));
        try {
            const response = await fetch("./api/index.php?roles", {
                method: "PATCH",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ idRol: idRolAccion, permisos })
            });
    
            const data = await response.json();
            mostrarMensaje(data.message, "Permisos", data.success ? "success" : "danger");
    
            if (data.success) modalRolePerms.hide();

        } catch (error) {
            console.error("Error al guardar los permisos del rol:", error);
        }
    });

    // ---------- Botones de mover permisos ----------
    document.getElementById("btnAsignar").addEventListener("click", () => {
        [...disponibles.selectedOptions].forEach(opt => {
            asignados.add(opt);
        });
    });

    document.getElementById("btnQuitar").addEventListener("click", () => {
        [...asignados.selectedOptions].forEach(opt => {
            disponibles.add(opt);
        });
    });

    // ---------- MODAL MENSAJE ----------
    function mostrarMensaje(texto, titulo = "Mensaje", tipo = "success") {
        document.querySelector("#modalMessageTitle").textContent = titulo;

        const header = document.querySelector("#modalMessageHeader");
        header.className = "";
        header.classList.add("modal-header", `bg-${tipo}`, "text-white");

        document.querySelector("#modalMessageBody").textContent = texto;

        modalMessage.show();
    }

    // Limpiar validaciones
    function limpiarValidaciones() {
        [...formRol.elements].forEach(el => {
            el.classList.remove("is-invalid");
            const feedback = el.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.textContent = ""; // resetear mensaje
        });
    }

});
