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
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="codigo_inventario" id="codigo_inventario" placeholder="Código de Inventario" title="Se asigna según la categoría seleccionada." readonly>
                                <label class="form-label" for="codigo_inventario">Código Inventario</label>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="id_categoria" required>
                                    <option value="">
                                        Cargando...
                                    </option>
                                </select>
                                <label class="form-label">Categoría</label>
                            </div>
                            <div class="invalid-feedback">Seleccione una categoría.</div>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="marca" placeholder="Ingrese la marca del Equipo" required>
                                <label class="form-label">Marca</label>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="modelo">
                                <label class="form-label">Modelo</label>
                            </div>
                        </div>

                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="numero_serie">
                                <label class="form-label">Número de Serie</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="estado">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Asignado">Asignado</option>
                                    <option value="En reparacion">En Reparación</option>
                                    <option value="Baja">Baja</option>
                                </select>
                                <label class="form-label">Estado</label>
                            </div>
                        </div>
                    </div>

                    <div class="row m-0 d-none p-3 bg-light border border-dark-subtle rounded" id="bloqueAsignacion">
                        <p class="fw-bold mb-1 p-0">¿A quién se asigna?</p>

                        <div class="g-0 mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input border-dark-subtle focus-ring focus-ring-dark" type="radio" name="asignado_tipo" id="radioUsuario" value="usuario">
                                <label class="form-check-label" for="radioUsuario">Usuario Sistema</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input border-dark-subtle focus-ring focus-ring-dark" type="radio" name="asignado_tipo" id="radioEmpleado" value="empleado">
                                <label class="form-check-label" for="radioEmpleado">Empleado (Nómina)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input border-dark-subtle focus-ring focus-ring-dark" type="radio" name="asignado_tipo" id="radioArea" value="area">
                                <label class="form-check-label" for="radioArea">Área / Sector</label>
                            </div>
                        </div>

                        <div class="g-0">
                            <select class="form-select d-none selector-asignacion border-dark-subtle focus-ring focus-ring-dark" id="selAsignarUsuario">
                                <option value="">Seleccione Usuario...</option>
                            </select>

                            <select class="form-select d-none selector-asignacion border-dark-subtle focus-ring focus-ring-dark" id="selAsignarEmpleado">
                                <option value="">Seleccione Empleado...</option>
                            </select>

                            <select class="form-select d-none selector-asignacion border-dark-subtle focus-ring focus-ring-dark" id="selAsignarArea">
                                <option value="">Seleccione Área...</option>
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