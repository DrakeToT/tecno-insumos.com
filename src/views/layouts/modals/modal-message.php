<?php
if (!isset($modalMessageTitle)) {
    $modalMessageTitle = "Error!";
}
?>

<div class="modal fade" id="modalMessage" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white" id="modalMessageHeader">
                <h5 class="modal-title text-capitalize" id="modalMessageTitle"><?php echo $modalMessageTitle ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" >
                <p class="fw-normal fst-normal" id="modalMessageBody"></p>
            </div>
            <div class="modal-footer d-none">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>