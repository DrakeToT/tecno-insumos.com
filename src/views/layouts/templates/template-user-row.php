<template id="userRowTemplate">
    <tr data-id="">
        <td class="id"></td>
        <td class="nombre"></td>
        <td class="apellido"></td>
        <td class="email"></td>
        <td class="rol text-nowrap"></td>
        <td class="estado">
             <span class="badge"></span>
        </td>
        <td class="text-center text-nowrap acciones">
            <button class="btn btn-sm btn-outline-primary btn-editar" title="Editar usuario">
                <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning ms-1 btn-estado" title="Cambiar estado">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-sm btn-outline-dark ms-1 btn-password" title="Restablecer contraseña">
                <i class="bi bi-key-fill"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger ms-1 btn-eliminar" title="Eliminar usuario">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </td>
    </tr>
</template>

<template id="userRowNullTemplate">
    <tr>
        <td colspan="7" class="text-center text-muted py-3">
            <i class="bi bi-info-circle"></i> No se encontraron usuarios.
        </td>
    </tr>
</template>