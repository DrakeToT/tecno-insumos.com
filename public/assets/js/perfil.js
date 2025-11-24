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

// URL API BASE
const API_PERFIL = "./api/index.php?perfil";

// Función para cargar datos
async function cargarDatosUsuario() {
    try {
        const response = await fetch(API_PERFIL);
        const data = await response.json();
        if (data.success) {
            const u = data.user;
            // Actualizamos valores solo si los elementos existen
            const inputNombre = document.getElementById("nombrePerfil");
            if (inputNombre) inputNombre.value = u.nombre;

            const inputApellido = document.getElementById("apellidoPerfil");
            if (inputApellido) inputApellido.value = u.apellido;

            const inputEmail = document.getElementById("emailPerfil");
            if (inputEmail) inputEmail.value = u.email;
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

    // === Lógica de cambio de foto de perfil ===
    if (fotoPerfil && inputFoto) {
        // Abrir selector de archivo al hacer click
        [fotoPerfil, overlayEditar].forEach(el =>
            el?.addEventListener("click", () => inputFoto.click())
        );
        // Enviar automáticamente al seleccionar archivo
        inputFoto.addEventListener("change", async () => {
            const formData = new FormData(formFoto);
            if (formData.get("fotoPerfil").size === 0) return;

            try {
                const response = await fetch(API_PERFIL, { method: "POST", body: formData });
                const data = await response.json();

                if (data.success) {
                    const nuevaUrl = URL.createObjectURL(formData.get("fotoPerfil"));
                    fotoPerfil.src = nuevaUrl;  // actualiza sin recargar
                    if (fotoNavBody) fotoNavBody.src = nuevaUrl;
                    if (fotoNavHeader) fotoNavHeader.src = nuevaUrl;
                    mostrarMensajeModal("Foto actualizada correctamente.", "Éxito", "success");
                } else {
                    mostrarMensajeModal(data.message, "Error Foto", "warning");
                }
            } catch {
                mostrarMensajeModal("Error al subir la imagen.");
            }
        });
    }

    // Cargar datos del usuario
    try {
        const response = await fetch(API_PERFIL);
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
    const btnEditar = document.getElementById("btnEditar");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCancelar = document.getElementById("btnCancelar");
    const formDatos = document.getElementById("formDatos");
    const camposEditables = ["nombrePerfil", "apellidoPerfil", "emailPerfil"];

    if (btnEditar) {
        btnEditar.addEventListener("click", () => {
            camposEditables.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.removeAttribute("readonly");
                    el.classList.remove("bg-light"); // Visual feedback
                }
            });
            btnEditar.classList.add("d-none");
            btnGuardar.classList.remove("d-none");
            btnCancelar.classList.remove("d-none");
        });

        btnCancelar.addEventListener("click", async () => {
            await cargarDatosUsuario(); // Restaurar datos originales
            camposEditables.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.setAttribute("readonly", true);
                    el.classList.add("bg-light");
                }
            });
            btnEditar.classList.remove("d-none");
            btnGuardar.classList.add("d-none");
            btnCancelar.classList.add("d-none");
        });

        formDatos.addEventListener("submit", async (e) => {
            e.preventDefault();
            const nombre = document.getElementById("nombrePerfil").value.trim();
            const apellido = document.getElementById("apellidoPerfil").value.trim();
            const email = document.getElementById("emailPerfil").value.trim();

            try {
                const response = await fetch(API_PERFIL, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ nombre, apellido, email })
                });
                const data = await response.json();

                mostrarMensajeModal(data.message, "Datos Personales", data.success ? "success" : "danger");

                if (data.success) {
                    btnCancelar.click(); // Re-bloquear campos
                }
            } catch {
                mostrarMensajeModal("Error al guardar los cambios.");
            }
        });
    }

    // === Cambio de contraseña con invalid-feedback ===
    const formPassword = document.getElementById("formPassword");
    const modalPasswordEl = document.getElementById("modalPassword");

    if (formPassword) {
        formPassword.addEventListener("submit", async (e) => {
            e.preventDefault();

            const actual = document.getElementById("actualPassword");
            const nueva = document.getElementById("newPassword");
            const confirm = document.getElementById("confirmPassword");
            const fbActual = document.getElementById("feedbackActual");
            const fbNueva = document.getElementById("feedbackNueva");
            const fbConfirm = document.getElementById("feedbackConfirmar");

            // Reset visual
            [actual, nueva, confirm].forEach(i => i.classList.remove("is-invalid"));
            [fbActual, fbNueva, fbConfirm].forEach(fb => fb.textContent = "");

            let valid = true;

            if (!actual.value.trim()) {
                actual.classList.add("is-invalid");
                fbActual.textContent = "Requerido.";
                valid = false;
            }
            if (nueva.value.length < 8) {
                nueva.classList.add("is-invalid");
                fbNueva.textContent = "Mínimo 8 caracteres.";
                valid = false;
            }
            if (confirm.value !== nueva.value) {
                confirm.classList.add("is-invalid");
                fbConfirm.textContent = "Las contraseñas deben coincidir.";
                valid = false;
            }

            if (!valid) return;

            try {
                const response = await fetch(API_PERFIL, {
                    method: "PATCH",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        actualPassword: actual.value.trim(),
                        newPassword: nueva.value.trim()
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    // Si falla, asumimos que es la pass actual
                    actual.classList.add("is-invalid");
                    fbActual.textContent = data.message;
                } else {
                    formPassword.reset();
                    const modal = bootstrap.Modal.getInstance(modalPasswordEl);
                    modal.hide();
                    mostrarMensajeModal("Contraseña actualizada.", "Cambio de Contraseña", "success");
                }
            } catch {
                mostrarMensajeModal("Error de conexión.");
            }
        });

        modalPasswordEl.addEventListener("hidden.bs.modal", () => {
            formPassword.reset();
            formPassword.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
            formPassword.querySelectorAll(".invalid-feedback").forEach(el => el.textContent = "");
        });
    }

});