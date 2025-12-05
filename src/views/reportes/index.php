<?php
require_once __DIR__ . '/../../../src/config/base-url.php';
require_once __DIR__ . '/../../../src/helpers/session.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL);
    exit;
}

$titlePage = "Reportes y Consultas - Tecno Insumos";
$page = 'reportes';
$extra_js = ["/assets/js/reportes.js"];

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/navbar.php';
?>

<div class="container-fluid mt-4">
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-search"></i> Consultar Asignaciones</h5>
        </div>
        <div class="card-body">
            <form id="formReporte" class="row g-3 align-items-end">
                
                <div class="col-md-3">
                    <label for="selectTipo" class="form-label fw-bold">Tipo de Entidad</label>
                    <select class="form-select" id="selectTipo" required>
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="usuario">Usuario del Sistema</option>
                        <option value="empleado">Empleado (Nómina)</option>
                        <option value="area">Área / Sector</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label for="selectEntidad" class="form-label fw-bold">Seleccionar Responsable</label>
                    <select class="form-select" id="selectEntidad" disabled required>
                        <option value="">Primero seleccione un tipo...</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" id="btnConsultar">
                        <i class="bi bi-search"></i> Generar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm d-none" id="cardResultados">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary"><i class="bi bi-clipboard-data"></i> Resultado del Reporte</h5>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
        
        <div class="card-body">
            <div class="alert alert-light border d-flex justify-content-between align-items-center" role="alert">
                <div>
                    <h4 class="alert-heading mb-1" id="lblNombre">Nombre Responsable</h4>
                    <p class="mb-0 text-muted">
                        <span id="lblTipo" class="badge bg-secondary me-2">Tipo</span>
                        <span id="lblExtra">Dato Extra</span>
                    </p>
                </div>
                <div class="text-end">
                    <h2 class="display-6 fw-bold mb-0" id="lblTotal">0</h2>
                    <small class="text-muted">Equipos Asignados</small>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-hover align-middle border">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Marca / Modelo</th>
                            <th>Serie</th>
                            <th>Fecha Asignación</th> <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyResultados">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-dark">
                <h5 class="modal-title"><i class="bi bi-clock-history"></i> Historial del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold mb-3 text-primary" id="modalEquipoTitulo"></h6>
                <div class="list-group list-group-flush" id="listaHistorial">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/modals/modal-message.php'; ?>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>