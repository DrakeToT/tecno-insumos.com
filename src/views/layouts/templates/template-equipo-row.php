<template id="equipoRowTemplate">
    <tr>
        <td class="align-middle fw-bold codigo"></td>
        <td class="align-middle categoria"></td>
        <td class="align-middle marca-modelo"></td>
        <td class="align-middle serie text-muted small"></td>
        <td class="align-middle estado text-center"><span class="badge"></span></td>
        <td class="align-middle ubicacion small"></td>
        <td class="align-middle text-nowrap text-center">
            <button class="btn btn-sm btn-outline-primary btn-editar" title="Mostrar / Editar">
                <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger btn-eliminar" title="Eliminar">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </td>
    </tr>
</template>

<template id="equipoRowNullTemplate">
    <tr>
        <td colspan="100%" class="text-center text-muted py-4">
            <i class="bi bi-inbox h4 d-block"></i>
            No se encontraron equipos.
        </td>
    </tr>
</template>