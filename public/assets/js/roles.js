document.addEventListener("DOMContentLoaded", () => {

    const tablaRoles = document.querySelector("#tablaRoles tbody");
    const buscarRolEl = document.querySelector("#buscarRol");

    const modalRoleForm = new bootstrap.Modal("#modalRoleForm");
    const modalRolePerms = new bootstrap.Modal("#modalRolePerms");
    const modalConfirm = new bootstrap.Modal("#modalConfirm");
    const modalMessage = new bootstrap.Modal("#modalMessage");

    const formRol = document.querySelector("#formRol");
    const modalTitle = document.querySelector("#modalTitle");
    const modalTitleIcon = document.querySelector("#modalTitleIcon");

    const templateRow = document.querySelector("#roleRowTemplate");
    const templateNull = document.querySelector("#roleRowNullTemplate");

    let tipoAccion = null;
    let idRolAccion = null;

    let currentSort = "id";
    let currentOrder = "ASC";

    cargarRoles();

    // Buscar roles al escribir
    buscarRolEl.addEventListener("input", () => {
        cargarRoles(buscarRolEl.value.trim());
    });

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

            cargarRoles(buscarRolEl.value.trim());
        });
    });

    function cargarRoles(search = "") {
        fetch(`./api/index.php?action=roles&search=${encodeURIComponent(search)}&sort=${currentSort}&order=${currentOrder}`)
            .then(r => r.json())
            .then(data => {
                tablaRoles.innerHTML = "";

                if (!data.success || data.roles.length === 0) {
                    tablaRoles.appendChild(templateNull.content.cloneNode(true));
                    return;
                }

                data.roles.forEach(rol => {
                    const row = templateRow.content.cloneNode(true);

                    row.querySelector(".id").textContent = rol.id;
                    row.querySelector(".nombre").textContent = rol.nombre;
                    row.querySelector(".descripcion").textContent = rol.descripcion ?? "";

                    // Estado
                    const badge = row.querySelector(".estado span");
                    badge.textContent = rol.estado;
                    badge.classList.add(rol.estado === "Activo" ? "bg-success" : "bg-danger");

                    // Botón editar rol
                    row.querySelector(".btn-editar").addEventListener("click", () => {
                        abrirModalEditarRol(rol);
                    });

                    // Botón cambiar estado
                    row.querySelector(".btn-estado").addEventListener("click", () => {
                        confirmarCambioEstado(rol);
                    });

                    // Botón eliminar rol
                    row.querySelector(".btn-eliminar").addEventListener("click", () => {
                        confirmarEliminacionRol(rol);
                    });

                    // Doble clic para editar permisos
                    row.querySelector("tr").addEventListener("dblclick", () => {
                        abrirModalPermisos(rol.id);
                    });

                    tablaRoles.appendChild(row);
                });
            });
    }

    // ---------- MODAL CREAR ROL ----------
    document.querySelector("#btnNewRol").addEventListener("click", () => {
        tipoAccion = "nuevo";
        idRolAccion = null;

        modalTitle.textContent = "Nuevo Rol";
        modalTitleIcon.className = "bi bi-plus-circle";

        formRol.reset();
        modalRoleForm.show();
    });

    // ---------- MODAL EDITAR ROL ----------
    function abrirModalEditarRol(rol) {
        tipoAccion = "editar";
        idRolAccion = rol.id;

        modalTitle.textContent = "Editar Rol";
        modalTitleIcon.className = "bi bi-pencil-square";

        formRol.nombre.value = rol.nombre;
        formRol.descripcion.value = rol.descripcion;

        // Agregamos el select de estado dinámicamente solo si no existe
        if (!document.querySelector("#estadoRol")) {
            const div = document.createElement("div");
            div.classList.add("mb-3");
            div.innerHTML = `
                <label for="estadoRol" class="form-label">Estado</label>
                <select id="estadoRol" class="form-select">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            `;
            formRol.querySelector(".modal-body").appendChild(div);
        }

        document.querySelector("#estadoRol").value = rol.estado;

        modalRoleForm.show();
    }

    // ---------- GUARDAR ROL ----------
    formRol.addEventListener("submit", async e => {
        e.preventDefault();

        const data = {
            nombre: formRol.nombre.value.trim(),
            descripcion: formRol.descripcion.value.trim()
        };

        let method = "POST";

        if (tipoAccion === "editar") {
            method = "PUT";
            data.id = idRolAccion;
            data.estado = document.querySelector("#estadoRol").value;
        }

        const response = await fetch("./api/index.php?action=roles", {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        mostrarMensaje(result.message, "Gestión de Roles", result.success ? "success" : "danger");

        if (result.success) {
            modalRoleForm.hide();
            cargarRoles(buscarRolEl.value.trim());
        }
    });

    // ---------- CONFIRMAR CAMBIO DE ESTADO ----------
    function confirmarCambioEstado(rol) {
        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Está seguro de cambiar el estado del rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            const nuevoEstado = rol.estado === "Activo" ? "Inactivo" : "Activo";

            const response = await fetch("./api/index.php?action=roles", {
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
                cargarRoles(buscarRolEl.value.trim());
                modalConfirm.hide();
            }
        };
    }

    // ---------- CONFIRMAR ELIMINACIÓN ----------
    function confirmarEliminacionRol(rol) {
        document.querySelector("#modalConfirmMensaje").textContent =
            `¿Desea eliminar el rol "${rol.nombre}"?`;

        modalConfirm.show();

        document.querySelector("#modalConfirmBtnAceptar").onclick = async () => {
            const response = await fetch("./api/index.php?action=roles", {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: rol.id })
            });

            const data = await response.json();
            mostrarMensaje(data.message, "Eliminar rol", data.success ? "success" : "danger");

            if (data.success) {
                cargarRoles(buscarRolEl.value.trim());
                modalConfirm.hide();
            }
        };
    }

    // ---------- MODAL PERMISOS ----------
    async function abrirModalPermisos(idRol) {

        const response = await fetch(`./api/index.php?action=roles&id=${idRol}`);
        const data = await response.json();

        if (!data.success) {
            mostrarMensaje(data.message, "Error", "danger");
            return;
        }

        idRolAccion = idRol;

        document.querySelector("#nombreRolPerms").textContent = data.rol.nombre;

        const contenedor = document.querySelector("#contenedorPermisos");
        contenedor.innerHTML = "";

        data.permisosDisponibles.forEach(perm => {

            const isChecked = data.permisosAsignados.includes(perm.id);

            const div = document.createElement("div");
            div.classList.add("col-md-4");

            div.innerHTML = `
                <div class="form-check border rounded p-2 bg-light">
                    <input class="form-check-input permisoCheck"
                           type="checkbox"
                           value="${perm.id}"
                           id="perm_${perm.id}"
                           ${isChecked ? "checked" : ""}>
                    <label class="form-check-label" for="perm_${perm.id}">
                        ${perm.nombre}
                    </label>
                </div>
            `;

            contenedor.appendChild(div);
        });

        modalRolePerms.show();
    }

    // ---------- GUARDAR PERMISOS ----------
    document.querySelector("#btnSavePerms").addEventListener("click", async () => {

        const checks = [...document.querySelectorAll(".permisoCheck")];

        const permisos = checks
            .filter(chk => chk.checked)
            .map(chk => parseInt(chk.value));

        const response = await fetch("./api/index.php?action=roles", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ idRol: idRolAccion, permisos })
        });

        const data = await response.json();
        mostrarMensaje(data.message, "Permisos", data.success ? "success" : "danger");

        if (data.success) modalRolePerms.hide();
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

});
