// Fución para mostrar Errores en un modal
function mostrarMensajeModal(mensaje, titulo = "Error", estilo = "danger") {
    const modalBody = document.getElementById("modalMessageBody");
    const modalTitle = document.getElementById("modalMessageTitle");
    const modalHeader = document.getElementById("modalMessageHeader");
    modalTitle.textContent = titulo;
    modalBody.textContent = mensaje;
    modalHeader.classList.remove("bg-danger", "bg-success", "bg-warning", "bg-info");
    modalHeader.classList.add(`bg-${estilo}`);

    const modal = new bootstrap.Modal(document.getElementById("modalMessage"));
    modal.show();
}

// Función auxiliar para recargar datos
async function cargarDatosUsuario() {
    try {
        const response = await fetch("./api/index.php?action=perfil");
        const data = await response.json();
        if (data.success) {
            const u = data.user;
            document.getElementById("nombrePerfil").value = u.nombre;
            document.getElementById("apellidoPerfil").value = u.apellido;
            document.getElementById("emailPerfil").value = u.email;
        }
    } catch (err) {
        console.error("Error recargando perfil:", err);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    const fotoPerfil = document.getElementById("fotoPerfil");
    const fotoNavBody = document.getElementById("fotoNavBody");
    const fotoNavHeader = document.getElementById("fotoNavHeader");
    const overlayEditar = document.querySelector(".perfil-overlay-editar");
    const inputFoto = document.getElementById("inputFoto");
    const formFoto = document.getElementById("formFoto");

    // Abrir selector de archivo al hacer click
    [fotoPerfil, overlayEditar].forEach(el =>
        el.addEventListener("click", () => inputFoto.click())
    );

    // Enviar automáticamente al seleccionar archivo
    inputFoto.addEventListener("change", async () => {
        const formData = new FormData(formFoto);
        const archivo = formData.get("fotoPerfil");
        if (archivo.size === 0) return;

        try {
            const response = await fetch("./api/index.php?action=perfil", {
                method: "POST",
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                fotoPerfil.src = URL.createObjectURL(archivo); // actualiza sin recargar
                fotoNavBody.src = fotoPerfil.src;
                fotoNavHeader.src = fotoPerfil.src;
            }
        } catch {
            mostrarMensajeModal("Error al subir la imagen.");
        }
    });

    // Cargar datos del usuario
    try {
        const response = await fetch("./api/index.php?action=perfil");
        const data = await response.json();
        if (data.success) {
            const u = data.user;
            document.getElementById("nombrePerfil").value = u.nombre;
            document.getElementById("apellidoPerfil").value = u.apellido;
            document.getElementById("emailPerfil").value = u.email;
            fotoPerfil.src = u.fotoPerfil
                ? `./assets/uploads/${u.fotoPerfil}`
                : `./assets/img/perfil.webp`;
        }
    } catch (err) {
        console.error("Error cargando perfil:", err.message);
    }

    // === Lógica de edición de datos personales ===
    const formDatos = document.getElementById("formDatos");
    const btnEditar = document.getElementById("btnEditar");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCancelar = document.getElementById("btnCancelar");

    const camposEditables = ["nombrePerfil", "apellidoPerfil", "emailPerfil"];

    // Habilitar edición
    btnEditar.addEventListener("click", () => {
        camposEditables.forEach(id => {
            document.getElementById(id).removeAttribute("readonly");
        });
        btnEditar.classList.add("d-none");
        btnGuardar.classList.remove("d-none");
        btnCancelar.classList.remove("d-none");
    });

    // Cancelar edición (restaurar valores)
    btnCancelar.addEventListener("click", async () => {
        await cargarDatosUsuario();
        camposEditables.forEach(id => {
            document.getElementById(id).setAttribute("readonly", true);
        });
        btnEditar.classList.remove("d-none");
        btnGuardar.classList.add("d-none");
        btnCancelar.classList.add("d-none");
    });

    // Guardar cambios
    formDatos.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nombre = document.getElementById("nombrePerfil").value.trim();
        const apellido = document.getElementById("apellidoPerfil").value.trim();
        const email = document.getElementById("emailPerfil").value.trim();

        try {
            const response = await fetch("./api/index.php?action=perfil", {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ nombre, apellido, email })
            });
            const data = await response.json();
            mostrarMensajeModal(data.message, "Datos Personales - Edición", data.success ? "success" : "danger");
            if (data.success) {
                btnCancelar.click(); // Restablece los botones
            }
        } catch {
            mostrarMensajeModal("Error al guardar los cambios.");
        }
    });

    // === Cambio de contraseña con invalid-feedback ===
    const formPassword = document.getElementById("formPassword");

    formPassword.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Obtener inputs y feedbacks
        const actualPassword = document.getElementById("actualPassword");
        const newPassword = document.getElementById("newPassword");
        const confirmPassword = document.getElementById("confirmPassword");

        const feedbackActual = document.getElementById("feedbackActual");
        const feedbackNueva = document.getElementById("feedbackNueva");
        const feedbackConfirmar = document.getElementById("feedbackConfirmar");

        // Resetear estados previos
        [actualPassword, newPassword, confirmPassword].forEach(input => {
            input.classList.remove("is-invalid");
        });
        [feedbackActual, feedbackNueva, feedbackConfirmar].forEach(fb => fb.textContent = "");

        // Validaciones del lado del cliente
        let valid = true;

        if (!actualPassword.value.trim()) {
            actualPassword.classList.add("is-invalid");
            feedbackActual.textContent = "Debe ingresar su contraseña actual.";
            valid = false;
        }

        if (!newPassword.value.trim()) {
            newPassword.classList.add("is-invalid");
            feedbackNueva.textContent = "Debe ingresar una nueva contraseña.";
            valid = false;
        } else if (newPassword.value.length < 8) {
            newPassword.classList.add("is-invalid");
            feedbackNueva.textContent = "La nueva contraseña debe tener al menos 8 caracteres.";
            valid = false;
        }

        if (confirmPassword.value.trim() !== newPassword.value.trim()) {
            confirmPassword.classList.add("is-invalid");
            feedbackConfirmar.textContent = "Las contraseñas no coinciden.";
            valid = false;
        }

        if (!valid) return;

        // Enviar datos al servidor
        try {
            const response = await fetch("./api/index.php?action=perfil", {
                method: "PATCH",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    actualPassword: actualPassword.value.trim(),
                    newPassword: newPassword.value.trim()
                })
            });

            const data = await response.json();

            if (!data.success) {
                // Mostrar errores específicos del servidor
                actualPassword.classList.add("is-invalid");
                feedbackActual.textContent = data.message || "Error al actualizar la contraseña.";
                return;
            }

            // Éxito
            formPassword.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById("modalPassword"));
            modal.hide();
            mostrarMensajeModal("Contraseña actualizada correctamente.", "Cambio de contraseña - Exitoso", "success");

        } catch (error) {
            mostrarMensajeModal("Error de conexión con el servidor.", "Cambio de contraseña - Error", "danger");
        }
    });

    // === Resetear el modal de contraseña al cerrarse ===
    const modalPasswordEl = document.getElementById("modalPassword");

    modalPasswordEl.addEventListener("hidden.bs.modal", () => {
        // Limpiar los valores
        formPassword.reset();

        // Quitar estados de validación
        formPassword.querySelectorAll(".is-invalid, .is-valid").forEach(input => {
            input.classList.remove("is-invalid", "is-valid");
        });

        // Limpiar mensajes de feedback
        formPassword.querySelectorAll(".invalid-feedback").forEach(fb => {
            fb.textContent = "";
        });
    });

});