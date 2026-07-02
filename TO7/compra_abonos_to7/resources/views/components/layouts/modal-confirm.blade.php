
        <!-- Modal -->
        <div class="modal fade" 
        id="confirmModal" 
        tabindex="-1" 
        aria-labelledby="confirmModalLabel" 
        aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x"></i>
                        </button>

                        <div>
                            <i class="bi bi-exclamation-circle"></i>

                            <h1 class="modal-title fs-5" id="confirmModalLabel">
                                <!-- ¿Quiere eliminar este tipo de abono? -->
                            </h1>
                        </div>
                    </div>

                    <div class="modal-body">
                        <!-- Una vez eliminado, no podrá revertirse ¿Desea continuar? -->
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnConfirmar">Confirmar</button>
                    </div>
                
                </div>
            </div>
        
        </div>
