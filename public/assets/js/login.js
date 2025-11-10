document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formLogIn");
    const emailInput = document.getElementById("loginEmail");
    const passInput = document.getElementById("loginPassword");
    const queryInputGroup = document.querySelectorAll(".input-group");
    const feedbackDiv = document.querySelector(".invalid-feedback");

    // Expresión regular para validar el formato del correo electrónico
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Contenedor de alertas dinámicas (errores del servidor)
    const alertPlaceholder = document.createElement("div");
    alertPlaceholder.classList.add("mt-3");
    form.parentNode.appendChild(alertPlaceholder);

    // Validación en tiempo real del formato del correo electrónico
    emailInput.addEventListener("input", () => {
        const emailValue = emailInput.value.trim();

        if (!emailRegex.test(emailValue)) {
            queryInputGroup[0].classList.add("is-invalid");
            emailInput.classList.add("is-invalid");
            feedbackDiv.textContent = "Ingrese un correo electrónico válido.";
        } else {
            queryInputGroup[0].classList.remove("is-invalid");
            emailInput.classList.remove("is-invalid");
            feedbackDiv.textContent = "";
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Limpia mensajes y alertas previas
        alertPlaceholder.innerHTML = "";
        feedbackDiv.textContent = "";

        const email = emailInput.value.trim();
        const password = passInput.value.trim();
        let valid = true;

        // Validación de correo electrónico
        if (!email) {
            queryInputGroup[0].classList.add("is-invalid");
            emailInput.classList.add("is-invalid");
            feedbackDiv.textContent = "El campo de correo electrónico es obligatorio.";
            valid = false;
        } else if (!emailRegex.test(email)) {
            queryInputGroup[0].classList.add("is-invalid");
            emailInput.classList.add("is-invalid");
            feedbackDiv.textContent = "Ingrese un correo electrónico válido.";
            valid = false;
        } else {
            queryInputGroup[0].classList.remove("is-invalid");
            emailInput.classList.remove("is-invalid");
        }

        // Validación de contraseña
        if (!password) {
            queryInputGroup[1].classList.add("is-invalid");
            passInput.classList.add("is-invalid");
            feedbackDiv.textContent = "Debe ingresar una contraseña.";
            valid = false;
        } else {
            queryInputGroup[1].classList.remove("is-invalid");
            passInput.classList.remove("is-invalid");
        }

        // Si la validación falla, no se envía la solicitud
        if (!valid) return;

        try {
            // Envío de datos mediante AJAX (fetch)
            const response = await fetch("./api/index.php?action=login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            // Verificación de respuesta del servidor
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalLogin"));
                modal.hide();
                console.log("Redirigiendo a:", data.redirect);
                window.location.href = data.redirect;
            }
            else {
                queryInputGroup[0].classList.add("is-invalid");
                emailInput.classList.add("is-invalid");
                feedbackDiv.textContent = data.message || "Error en las credenciales.";
            }
        } catch (error) {
            // Error general de comunicación con el servidor
            alertPlaceholder.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Error de conexión con el servidor.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    });
});
