
<div class="container-offcanvas">
    <div class="offcanvas offcanvas-end form" tabindex="-1" id="canvas-form" aria-labelledby="canvas-form-label">
        <div class="offcanvas-header">
            <h5 id="canvas-form-label" class="offcanvas-title">Añadir tarea</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form action="" onsubmit="sendTask(event)" id="form-add-task" class="row">

                <div class="col-12">
                    <div class="form-floating col-md-12 mb-2">
                        <input type="text" class="form-control required" id="add-task-title" placeholder="">
                        <label for="add-task-title">Titulo *</label>
                        <span class="form-floating-focused"></span>
                    </div>
                </div>

                <div class="col-lg-7 col-md-12 col-sm-12">
                    <div class="row">
            
                        <div class="col-12 mb-2 container-editor">
                            <div class="full-editor" id="add-task-description">
                                
                            </div>
                        </div>
            
                        <!-- <div class="form-floating col-md-12 form-floating-outline mb-2">
                            <textarea class="form-control h-px-100" id="add-task-description" placeholder=""></textarea>
                            <label for="add-task-description">Descripción</label>
                        </div> -->
            
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg"
                                id="add-task-sprint">
                                    <option value="">Sin asignar</option>
                                    <?php foreach ($taskSprints as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="add-task-sprint">Sprints</label>
                        </div>
            
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg required"
                                id="add-task-activity">
                                    <option value="" disabled>Sin Asignar</option>
                                    <?php foreach ($activities as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="add-task-activity">Actividad *</label>
                        </div>
            
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg"
                                id="add-task-user">
                                    <option value="">Sin Asignar</option>
                                    <?php foreach ($users as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="add-task-user">Usuario</label>
                        </div>
            
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-5">
                            <input type="text" class="form-control flatpickr-input required" placeholder="YYYY-MM-DD" id="add-task-date">
                            <label for="add-task-date">Fecha *</label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12 col-sm-12 mb-5">
                    <div class="dropzone needsclick" id="add-task-files" >
                        <div class="dz-message needsclick">
                            Cargar archivos
                        </div>
                        <div class="fallback">
                            <input name="file" type="file"/>
                        </div>
                    </div>
                </div>
                                        
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Crear</button>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                            Cancelar
                        </button>
                    </div>
                </div>
    
            </form> 
        </div>
    </div>
    
    <div class="offcanvas offcanvas-end form" tabindex="-1" id="canvas-form-edit" aria-labelledby="canvas-form-edit-label">
        <div class="offcanvas-header">
            <h5 id="canvas-form-edit-label" class="offcanvas-title">Editar tarea</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form action="" class="row" onsubmit="sendTaskEdit(event)" id="form-edit-task">
    
                <input type="hidden" id="task_id">
    
                <div class="form-floating mb-2">
                    <input type="text" class="form-control required" id="edit-task-title" placeholder="">
                    <label for="edit-task-title">Titulo *</label>
                    <span class="form-floating-focused"></span>
                </div>

                <div class="col-lg-7 col-md-12 col-sm-12">
                    <div class="row">
            
                        <div class="col-12 mb-2 container-editor">
                            <div class="full-editor" id="edit-task-description">
                                
                            </div>
                        </div>
    
                        <!-- <div class="form-floating form-floating-outline mb-2">
                            <textarea class="form-control h-px-100" id="edit-task-description" placeholder=""></textarea>
                            <label for="edit-task-description">Descripción</label>
                        </div> -->
    
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg"
                                id="edit-task-sprint">
                                    <option value="">Sin asignar</option>
                                    <?php foreach ($taskSprints as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="edit-task-sprint">Sprints</label>
                        </div>
    
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg required"
                                id="edit-task-activity">
                                    <option value="" disabled>Sin Asignar</option>
                                    <?php foreach ($activities as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="edit-task-activity">Actividad *</label>
                        </div>
    
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-2">
                            <select
                                class="form-select form-select-lg"
                                id="edit-task-user">
                                    <option value="">Sin Asignar</option>
                                    <?php foreach ($users as $key => $data):?>
                                        <option value="<?= $data->id ?>"><?= $data->name ?></option>
                                    <?php endforeach ?>
                            </select>
                            <label for="edit-task-user">Usuario</label>
                        </div>
    
                        <div class="form-floating col-md-6 col-sm-12 form-floating-outline mb-5">
                            <input type="text" class="form-control flatpickr-input required" placeholder="YYYY-MM-DD" id="edit-task-date">
                            <label for="edit-task-date">Fecha *</label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12 col-sm-12 mb-5">
                    <div class="dropzone needsclick" id="edit-task-files" >
                        <div class="dz-message needsclick">
                            Cargar archivos
                        </div>
                        <div class="fallback">
                            <input name="file" type="file"/>
                        </div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Editar</button>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="canvas-form-comment" aria-labelledby="canvas-form-comment-label">
        <div class="offcanvas-header">
            <h5 id="canvas-form-comment-label" class="offcanvas-title">Comentar tarea</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form action="" class="row" onsubmit="sendTaskComment(event)" id="form-comment-task">

                <input type="hidden" id="task_id">

                <div class="col-12 mb-2 container-editor">
                    <div class="full-editor required" id="task-comment" data-placeholder="Comentario">
                        
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Comentar</button>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="canvas-comments" aria-labelledby="canvas-comments-label">
        <div class="offcanvas-header">
            <h5 id="canvas-comments-label" class="offcanvas-title">Comentarios</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <div class="comments">
                <ul class="timeline mb-0 pb-5">
                        
                </ul>        
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>