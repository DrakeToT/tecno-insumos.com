<!-- =================================================================== -->
<!-- MODAL FORMULARIO EQUIPO -->
<!-- =================================================================== -->
<div class="modal fade" id="modalEquipoForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-plus-circle" id="modalTitleIcon"></i> <span>Nuevo Equipo</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEquipo" novalidate>

                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Código Inventario *</label>
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="codigo_inventario" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría *</label>
                            <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="id_categoria" required>
                                <option value="">Seleccione una categoría</option>
                            </select>
                            <div class="invalid-feedback">Seleccione una categoría.</div>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Marca *</label>
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="marca" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="modelo">
                        </div>
                        
                        <div class="col-12 mt-3 d-none p-3 bg-light border rounded" id="bloqueAsignacion">
                            <label class="form-label fw-bold">¿A quién se asigna?</label>

                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="asignado_tipo" id="radioUsuario" value="usuario">
                                    <label class="form-check-label" for="radioUsuario">Usuario Sistema</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="asignado_tipo" id="radioEmpleado" value="empleado">
                                    <label class="form-check-label" for="radioEmpleado">Empleado (Nómina)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="asignado_tipo" id="radioArea" value="area">
                                    <label class="form-check-label" for="radioArea">Área / Sector</label>
                                </div>
                            </div>

                            <div class="mt-2">
                                <select class="form-select d-none selector-asignacion" id="selAsignarUsuario">
                                    <option value="">Seleccione Usuario...</option>
                                </select>

                                <select class="form-select d-none selector-asignacion" id="selAsignarEmpleado">
                                    <option value="">Seleccione Empleado...</option>
                                </select>

                                <select class="form-select d-none selector-asignacion" id="selAsignarArea">
                                    <option value="">Seleccione Área...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número de Serie</label>
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="numero_serie">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="estado">
                                <option value="Disponible">Disponible</option>
                                <option value="Asignado">Asignado</option>
                                <option value="En reparacion">En Reparación</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ubicación Física</label>
                        <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="ubicacion_detalle" placeholder="Ej: Estante 3, Depósito Central">
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Datos de Adquisición (Opcional)</h6>
                    <div class="row mb-3 g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Compra</label>
                            <input type="date" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="fecha_adquisicion">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Proveedor</label>
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="proveedor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="valor_compra">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones del Equipo</label>
                        <textarea class="form-control border-dark-subtle focus-ring focus-ring-dark" name="observaciones" rows="2" placeholder="Detalles permanentes del equipo (ej: rayón en tapa)"></textarea>
                    </div>

                    <div class="mb-3 p-3 bg-light border rounded d-none" id="divMotivoCambio">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-pencil-square"></i> ¿Por qué realizas este cambio? *
                        </label>
                        <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="motivo_cambio" id="inputMotivoCambio" placeholder="Ej: Corregí error de tipeo en el serial / Actualicé el precio">
                        <div class="form-text">Este comentario quedará guardado en el historial.</div>
                        <div class="invalid-feedback">Debes indicar el motivo de la edición.</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <button type="reset" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>