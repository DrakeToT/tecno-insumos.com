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
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="id_categoria" required>
                                    <option value="">
                                        Cargando...
                                    </option>
                                </select>
                                <label class="form-label">Categoría </label>
                                <div class="invalid-feedback">Seleccione una categoría.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="marca" placeholder="Ingrese la marca del Equipo" required>
                                <label class="form-label">Marca </label>
                                <div class="invalid-feedback">La marca es obligatoria.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="modelo" placeholder="Ingrese el modelo del Equipo" required>
                                <label class="form-label">Modelo </label>
                                <div class="invalid-feedback">El modelo es obligatorio.</div>
                            </div>
                        </div>

                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="numero_serie" placeholder="Ingrese el número de serie del Equipo" required>
                                <label class="form-label">Número de Serie </label>
                                <div class="invalid-feedback">Error en el número de serie.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select border-dark-subtle focus-ring focus-ring-dark" name="estado">
                                    <option value="" selected disabled>Seleccione un estado</option>
                                    <option value="Disponible" title="Estado para equipos nuevos y en condiciones de uso">Disponible</option>
                                    <option value="Asignado" title="Estado para equipos entregados y vinculados a un usuario/área/empleado">Asignado</option>
                                    <option value="En reparacion" title="Estado para equipos fuera de servicio y en proceso de reparación">En Reparación</option>
                                    <option value="Baja" title="Estado para equipos dados de baja y retirados del inventario">Baja</option>
                                </select>
                                <label class="form-label">Estado</label>
                            </div>
                        </div>
                    </div>

                    <div class="row m-0 d-none p-3 bg-light border border-dark-subtle rounded" id="bloqueAsignacion">
                        <p class="fw-bold mb-1 p-0">¿A quién se asigna? </p>

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
                        <div class="form-floating">
                            <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="ubicacion_detalle" placeholder="Ej: Estante 3, Depósito Central">
                            <label class="form-label">Ubicación Física</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-floating">
                            <textarea class="form-control border-dark-subtle focus-ring focus-ring-dark" name="observaciones" placeholder="Detalles del equipo (ej: especificaciones, estado físico, etc)"></textarea>
                            <label class="form-label">Detalles del Equipo (Opcional)</label>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Datos de Adquisición (Opcional)</h6>
                    <div class="row mb-3 g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="fecha_adquisicion" placeholder="Ingrese la fecha de compra">
                                <label class="form-label">Fecha Compra</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="proveedor" placeholder="Ingrese el nombre del proveedor">
                                <label class="form-label">Proveedor</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control border-dark-subtle focus-ring focus-ring-dark" name="valor_compra" placeholder="Ingrese el costo del equipo">
                                <label class="form-label">Costo</label>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3 p-3 bg-light border rounded d-none" id="divMotivoCambio">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-pencil-square"></i> ¿Por qué realizas este cambio? 
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