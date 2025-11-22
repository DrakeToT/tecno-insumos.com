<template id="roleRowTemplate">
    <tr>
        <td class="id"></td>
        <td class="nombre"></td>
        <td class="descripcion"></td>
        <td class="estado">
             <span class="badge"></span>
        </td>
        <td class="text-center text-nowrap acciones">
            <button class="btn btn-outline-primary btn-sm btn-editar" title="Editar rol">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-warning ms-1 btn-estado" title="Cambiar estado">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-sm btn-outline-success ms-1 btn-permisos" title="Cambiar permisos">
                <i class="bi bi-shield-lock"></i>
            </button>
            <button class="btn btn-outline-danger btn-sm btn-eliminar" title="Eliminar rol">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </td>
    </tr>
</template>

<template id="roleRowNullTemplate">
    <tr>
        <td colspan="5" class="text-center text-muted py-3">
            <i class="bi bi-info-circle"></i> No se encontraron roles.
        </td>
    </tr>
</template>