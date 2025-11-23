document.addEventListener("DOMContentLoaded", () => {
    //Elementos
    const tbodyUsuarios = document.getElementById("tablaUsuarios").querySelector("tbody");
    const inputBuscarUsuario = document.getElementById("buscarUsuario");
    const formUsuario = document.getElementById("formUsuario");
    const btnNewUser = document.getElementById("btnNewUser");

    //Modales
    const modals = {
        userForm: new bootstrap.Modal(document.getElementById("modalUserForm")),
        confirm: new bootstrap.Modal(document.getElementById("modalConfirm")),
        message: new bootstrap.Modal(document.getElementById("modalMessage"))
    };

    // Variables para acciones
    let idUsuarioAccion = null;
    let tipoAccion = null;

    // Variables de paginacion y ordenamiento
    let currentSort = "id";
    let currentOrder = "asc";
    let currentPage = 1;

    // Muestra un mensaje en el modal con título y estilo
    function mostrarMensaje(mensaje, titulo = "Error", estilo = "danger") {
        const modalBody = document.getElementById("modalMessageBody");
        const modalTitle = document.getElementById("modalMessageTitle");
        const modalHeader = document.getElementById("modalMessageHeader");

        modalTitle.textContent = titulo;
        modalBody.innerHTML = mensaje;
        modalHeader.classList.remove("bg-danger", "bg-success", "bg-warning", "bg-info");
        modalHeader.classList.add(`bg-${estilo}`);

        modals.message.show();
    }

    // Actualiza los iconos de ordenamiento en la cabecera de la tabla
    function actualizarIndicadoresOrden(sort, order) {
        // Ocultar todos los iconos
        document.querySelectorAll("#tablaUsuarios thead th.sortable i").forEach(icon => {
            icon.className = "bi bi-caret-up-fill opacity-0"; // invisible, reserva espacio
        });
        // Limpiar atributos data-order
        document.querySelectorAll("#tablaUsuarios thead th.sortable").forEach(th => {
            th.removeAttribute("data-order");
        });

        // Resaltar la columna actual
        const th = document.querySelector(`#tablaUsuarios thead th.sortable[data-sort="${sort}"]`);
        if (th) {
            const icon = th.querySelector("i");
            if (icon) {
                icon.className = (order === "asc")
                    ? "bi bi-caret-up-fill text-warning"
                    : "bi bi-caret-down-fill text-warning";
            }
            th.dataset.order = order;
        }
    }

    // Carga la lista de usuarios desde la API
    async function cargarUsuarios(filtro = "", sort = currentSort, order = currentOrder, page = 1) {
        try {
            const url = `./api/index.php?users&search=${encodeURIComponent(filtro)}&sort=${sort}&order=${order}&page=${page}`;
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                renderUsuarios(result.data);
                renderPaginacion(result.pagination);
                // Sincronizar estado
                currentSort = sort;
                currentOrder = order;
                currentPage = result.pagination.page;
                // Actualizar íconos de orden
                actualizarIndicadoresOrden(currentSort, currentOrder);
            } else {
                mostrarMensaje("Error al cargar usuarios", "Usuarios", "warning");
            }
        } catch (err) {
            console.error("Error de conexión:", err);
        }
    }

    // Carga el select de roles dinamicamente
    async function cargarRoles(selectedId = null) {
        const selectRol = document.getElementById("rolUsuario");

        // Opcion inicial de carga
        selectRol.innerHTML = "";
        let option = document.createElement('option');
        option.value = "";
        option.disabled = true;
        option.selected = true;
        option.textContent = "Cargando Roles ...";
        selectRol.appendChild(option);

        try {
            const response = await fetch("./api/index.php?roles");
            const data = await response.json();

            // Resetear select
            selectRol.innerHTML = "";
            option = document.createElement('option');
            option.value = "";
            option.textContent = "Seleccione un rol";
            selectRol.appendChild(option);

            if (data.success && data.roles) {
                const roles = Array.isArray(data.roles) ? data.roles : [data.roles];

                roles.forEach(r => {
                    const opt = document.createElement("option");
                    opt.value = r.id;
                    opt.textContent = r.nombre;

                    if (selectedId && Number(selectedId) === Number(r.id)) {
                        opt.selected = true;
                    }
                    selectRol.appendChild(opt);
                });
            } else {
                option.textContent = "No se pudieron cargar los roles";
            }
        } catch (err) {
            console.error("Error cargando roles:", err);
        }
    }


    // Renderiza las filas de la tabla de usuarios
    function renderUsuarios(usuarios) {
        tbodyUsuarios.innerHTML = "";

        const templateRow = document.getElementById("userRowTemplate");
        const templateEmpty = document.getElementById("userRowNullTemplate");

        // Si no hay resultados
        if (!usuarios.length) {
            const emptyClone = templateEmpty.content.cloneNode(true);
            tbodyUsuarios.appendChild(emptyClone);
            return;
        }

        // Si hay usuarios
        usuarios.forEach(u => {
            const clone = templateRow.content.cloneNode(true);
            const row = clone.querySelector("tr");

            row.dataset.id = u.id;
            row.querySelector(".id").textContent = u.id;
            row.querySelector(".nombre").textContent = u.nombre;
            row.querySelector(".apellido").textContent = u.apellido;
            row.querySelector(".email").textContent = u.email;
            row.querySelector(".rol").textContent = u.rol;

            const estadoBadge = row.querySelector(".estado .badge");
            estadoBadge.textContent = u.estado;
            estadoBadge.classList.toggle("bg-success", u.estado === "Activo");
            estadoBadge.classList.toggle("bg-secondary", u.estado !== "Activo");


            tbodyUsuarios.appendChild(clone);
        });
    }

    // Renderiza los controles de paginacion
    function renderPaginacion(pagination) {
        const container = document.getElementById("paginationContainer");
        container.innerHTML = "";

        const page = pagination.page;
        const pages = pagination.pages;

        if (pages <= 1) return;

        // Funcion auxiliar para crear botones
        const crearBoton = (texto, iconClass, pagDestino, disabled = false, active = false) => {
            const btn = document.createElement("button");
            btn.className = `btn btn-sm ${active ? 'btn-dark' : 'btn-outline-dark'} mx-1 border-0`;
            btn.disabled = disabled;
            btn.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : texto;

            if (!disabled && !active) {
                btn.addEventListener("click", () => {
                    cargarUsuarios(inputBuscarUsuario.value.trim(), currentSort, currentOrder, pagDestino);
                });
            }
            return btn;
        };

        // Boton Anterior
        container.appendChild(crearBoton("", "bi bi-chevron-left", page - 1, page <= 1));

        // Botones Numericos
        for (let i = 1; i <= pages; i++) {
            container.appendChild(crearBoton(i, null, i, false, i === page));
        }

        // Boton Siguiente
        container.appendChild(crearBoton("", "bi bi-chevron-right", page + 1, page >= pages));
    }

    // Muestra y configura el modal de usuario (Crear/Editar)
    function mostrarUserForm(modo) {
        if (!modo) return;

        const icon = document.getElementById('modalTitleIcon');
        const title = document.getElementById('modalTitle');
        const textNode = document.createTextNode('');

        // Limpiar contenido previo
        title.textContent = '';
        icon.className = '';
        title.appendChild(icon); // Agregamos el elemento <i> eliminado.

        if (modo === 'editar') {
            icon.classList.add('bi', 'bi-pencil-square');
            textNode.textContent = ' Editar usuario';
            title.appendChild(textNode);

            // Ocultar campo password al editar
            formUsuario.password.closest(".col-md-6").classList.add("d-none");
        }

        if (modo === 'crear') {
            icon.classList.add('bi', 'bi-person-plus-fill');
            textNode.textContent = ' Nuevo usuario';
            title.appendChild(textNode);

            // Mostrar campo password al crear
            formUsuario.password.closest(".col-md-6").classList.remove("d-none");
        }

        modals.userForm.show();
    }

    // Muestra el modal de confirmacion generico
    function abrirConfirmacion(id, accion, mensaje) {
        idUsuarioAccion = id;
        tipoAccion = accion;
        modals.confirm._element.querySelector("#modalConfirmMensaje").textContent = mensaje;
        modals.confirm.show();
    }

    // Listener de busqueda con retraso (debounce)
    let debounceTimer;
    inputBuscarUsuario.addEventListener("input", () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const filtro = inputBuscarUsuario.value.trim();
            // Reset variables paginación
            currentSort = "id";
            currentOrder = "asc";
            cargarUsuarios(filtro, currentSort, currentOrder, 1);
        }, 500);
    });

    // Boton para abrir el formulario de creacion de usuario
    btnNewUser.addEventListener("click", () => {
        tipoAccion = 'crear';
        mostrarUserForm(tipoAccion);
    });

    // Manejo del envio del formulario (Crear/Editar)
    formUsuario.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = {
            nombre: formUsuario.nombre.value.trim(),
            apellido: formUsuario.apellido.value.trim(),
            email: formUsuario.email.value.trim(),
            rol: formUsuario.rol.value,
            estado: formUsuario.estado.value,
            password: formUsuario.password.value.trim()
        };

        // Limpiar errores visuales previos
        formUsuario.querySelectorAll("input, select").forEach(el => {
            el.classList.remove("is-invalid");
        });

        // Validaciones basicas
        let valid = true;

        if (!formData.nombre || !formData.apellido || !formData.email || !formData.rol || !formData.estado) {
            formUsuario.querySelectorAll("input, select").forEach(el => {
                if (!el.value) el.classList.add("is-invalid");
            });
            valid = false;
        }

        // Solo validar contraseña si estamos creando
        if (tipoAccion === 'crear' && !formData.password) {
            formUsuario.password.classList.add("is-invalid");
            valid = false;
        }

        if (!valid) return;

        // Configuracion de la peticion
        const method = tipoAccion === 'editar' ? "PUT" : "POST";
        const url = "./api/index.php?users";
        if (tipoAccion === 'editar') {
            formData.id = idUsuarioAccion;
            delete formData.password; // No enviamos password en edicion general
        }

        try {
            const response = await fetch(url, {
                method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            mostrarMensaje(data.message, tipoAccion === 'editar' ? "Acción - Editar usuario" : "Nuevo usuario", data.success ? "success" : "warning");

            if (data.success) {
                modals.userForm.hide();
                formUsuario.reset();
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                cargarUsuarios(inputBuscarUsuario.value.trim(), currentSort, currentOrder, currentPage);
            }
        } catch (err) {
            mostrarMensaje("Error al guardar el usuario.", "danger");
        } finally {
            idUsuarioAccion = null;
            tipoAccion = null;
        }
    });


    // Manejo de confirmacion de acciones (Estado, Password, Eliminar)
    modals.confirm._element.querySelector("#modalConfirmBtnAceptar").addEventListener("click", async () => {
        if (!idUsuarioAccion || !tipoAccion) return;

        let body = { id: idUsuarioAccion, accion: tipoAccion };
        let method = "PATCH";

        // Caso especial: Carga datos para edicion antes de mostrar formulario
        if (tipoAccion === "editar") {
            try {
                const response = await fetch(`./api/index.php?users&id=${idUsuarioAccion}`);
                const data = await response.json();

                if (data.success && data.usuario) {
                    const u = data.usuario;

                    formUsuario.nombre.value = u.nombre;
                    formUsuario.apellido.value = u.apellido;
                    formUsuario.email.value = u.email;
                    formUsuario.rol.value = u.idRol;
                    formUsuario.estado.value = u.estado;

                    await cargarRoles(u.idRol); // Cargar roles y marcar el del usuario

                    mostrarUserForm("editar");
                } else {
                    mostrarMensaje("No se pudo cargar el usuario.", "Acción - Editar usuario", "warning");
                }
            } catch (err) {
                mostrarMensaje("Error al consultar usuario.", "Acción - Editar usuario", "danger");
            } finally {
                modals.confirm.hide();
            }
            return;
        }


        if (tipoAccion === "estado") {
            const fila = document.querySelector(`tr[data-id="${idUsuarioAccion}"]`);
            const estadoActual = fila.querySelector("td:nth-child(6)").textContent.trim();
            body.estado = estadoActual === "Activo" ? "Inactivo" : "Activo";
        }

        if (tipoAccion === "eliminar") {
            body = { id: idUsuarioAccion };
            method = "DELETE";
        }

        try {
            const response = await fetch("./api/index.php?users", {
                method: method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            let titulo = "";
            let estilo = data.success ? "success" : "danger";

            switch (tipoAccion) {
                case "estado":
                    titulo = "Acción - Cambiar estado";
                    break;
                case "reset-password":
                    titulo = "Acción - Restablecer contraseña";
                    break;
                case "eliminar":
                    titulo = "Acción - Eliminar usuario";
                    break;
                default:
                    titulo = "Acción realizada";
            }

            mostrarMensaje(
                data.tempPassword
                    ? `${data.message}<br><strong>Nueva contraseña:</strong> ${data.tempPassword}`
                    : data.message,
                titulo,
                estilo
            );

            modals.confirm.hide();
            cargarUsuarios(inputBuscarUsuario.value.trim(), currentSort, currentOrder, 1);
        } catch {
            mostrarMensaje("Error de conexión con el servidor.");
        } finally {
            idUsuarioAccion = null;
            tipoAccion = null;
        }
    });

    // Delegacion de eventos para botones de la tabla
    tbodyUsuarios.addEventListener("click", e => {
        const fila = e.target.closest("tr");
        const id = fila?.dataset.id;
        if (!id) return;

        // Editar datos
        if (e.target.closest(".btn-editar")) {
            abrirConfirmacion(id, "editar", "¿Desea editar los datos del usuario?");
        }

        // Cambiar estado
        if (e.target.closest(".btn-estado")) {
            abrirConfirmacion(id, "estado", "¿Desea cambiar el estado del usuario?");
        }

        // Restablecer contraseña
        if (e.target.closest(".btn-password")) {
            abrirConfirmacion(id, "reset-password", "¿Desea restablecer la contraseña del usuario?");
        }

        // Eliminar usuario
        if (e.target.closest(".btn-eliminar")) {
            abrirConfirmacion(id, "eliminar", "¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.");
        }
    });

    // Ordenamiento al hacer clic en encabezados de tabla (<th>)
    document.querySelectorAll("#tablaUsuarios thead th.sortable").forEach(th => {
        th.addEventListener("click", () => {
            const sort = th.dataset.sort;
            const current = th.dataset.order || "asc";
            const newOrder = current === "asc" ? "desc" : "asc";
            th.dataset.order = newOrder;

            // Actualizar variables globales
            currentSort = sort;
            currentOrder = newOrder;

            // Recargar usuarios ordenados
            cargarUsuarios(inputBuscarUsuario.value.trim(), currentSort, currentOrder, 1);
        });
    });


    // Enfoca automáticamente el campo de entrada cuando el modal se muestra,
    // ya que el atributo HTML 'autofocus' no funciona dentro de modales Bootstrap.
    modals.userForm._element.addEventListener('show.bs.modal', async () => {

        formUsuario.nombre.focus();
        if (tipoAccion === 'crear') {
            await cargarRoles(); // Cargar roles antes de mostrar el modal
            formUsuario.reset();
        }

        // Quita los is-invalid del Formulario
        formUsuario.querySelectorAll("input, select").forEach(el => {
            el.classList.remove("is-invalid");
        });
    });

    // Resetear modal al cerrar
    modals.userForm._element.addEventListener("hidden.bs.modal", () => {
        formUsuario.reset();
        idUsuarioAccion = null;
        tipoAccion = null;
    });

    // Carga inicial de usuarios
    cargarUsuarios("", currentSort, currentOrder, 1);

});
