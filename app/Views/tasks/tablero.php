<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Tablero<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />

    <!-- Vendors CSS -->
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/typeahead-js/typeahead.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/jkanban/jkanban.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/select2/select2.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/typography.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/katex.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/editor.css']) ?>" />

<!-- Page CSS -->

<link rel="stylesheet" href="<?= base_url(['assets/vendor/css/pages/app-kanban.css']) ?>" />

<style>
    .girando {
        display: inline-block; /* necesario para que la transformación funcione bien */
        animation: giro 1s linear infinite;
    }

    @keyframes giro {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">

        <div class="col-md-12 col-xxl-12 mb-1">
            <div class="card">
                <div class="card-body py-2 px-5 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Tablero de tareas
                    </h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <button class="btn btn-primary waves-effect waves-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvas-form" aria-controls="offcanvasEnd">
                                Añadir Tarea
                            </button>
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="canvas-form" aria-labelledby="canvas-form-label">
                                <div class="offcanvas-header">
                                    <h5 id="canvas-form-label" class="offcanvas-title">Añadir tarea</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body mx-0 flex-grow-0">
                                    <form action="" onsubmit="sendTask(event)" id="form-add-task">

                                        <div class="form-floating mb-2">
                                            <input type="text" class="form-control required" id="add-task-title" placeholder="">
                                            <label for="add-task-title">Titulo *</label>
                                            <span class="form-floating-focused"></span>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-2">
                                            <textarea class="form-control h-px-100" id="add-task-description" placeholder=""></textarea>
                                            <label for="add-task-description">Descripción</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-2">
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

                                        <div class="form-floating form-floating-outline mb-2">
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

                                        <div class="form-floating form-floating-outline mb-2">
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

                                        <div class="form-floating form-floating-outline mb-5">
                                            <input type="text" class="form-control flatpickr-input required" placeholder="YYYY-MM-DD" id="add-task-date">
                                            <label for="add-task-date">Fecha *</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary mb-2 d-grid w-100 waves-effect waves-light">Crear</button>
                                        <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                                            Cancelar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-kanban">

                <!-- Kanban Wrapper -->
                <div class="kanban-wrapper"></div>

                <!-- Edit Task/Task & Activities -->
                <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
                  <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body pt-2">
                    <div class="nav-align-top">
                      <ul class="nav nav-tabs mb-2">
                        <li class="nav-item">
                          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
                            <i class="ri-edit-box-line me-2"></i>
                            <span class="align-middle">Edit</span>
                          </button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                            <i class="ri-pie-chart-line me-2"></i>
                            <span class="align-middle">Activity</span>
                          </button>
                        </li>
                      </ul>
                    </div>
                    <div class="tab-content px-0 pb-0">
                      <!-- Update item/tasks -->
                      <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                        <form>
                          <div class="form-floating form-floating-outline mb-5">
                            <input type="text" id="title" class="form-control" placeholder="Enter Title" />
                            <label for="title">Title</label>
                          </div>
                          <div class="form-floating form-floating-outline mb-5">
                            <input type="text" id="due-date" class="form-control" placeholder="Enter Due Date" />
                            <label for="due-date">Due Date</label>
                          </div>
                          <div class="form-floating form-floating-outline mb-5">
                            <select class="select2 select2-label form-select" id="label">
                              <option data-color="bg-label-success" value="UX">UX</option>
                              <option data-color="bg-label-warning" value="Images">Images</option>
                              <option data-color="bg-label-secondary" value="App">App</option>
                              <option data-color="bg-label-danger" value="Code Review">Code Review</option>
                              <option data-color="bg-label-info" value="Info">Info</option>
                              <option data-color="bg-label-primary" value="Charts & Maps">Charts & Maps</option>
                            </select>
                            <label for="label"> label</label>
                          </div>
                          <div class="mb-5">
                            <label class="form-label">Assigned</label>
                            <div class="assigned d-flex flex-wrap"></div>
                          </div>
                          <div class="mb-5">
                            <label class="form-label" for="attachments">Attachments</label>
                            <div>
                              <input type="file" class="form-control" id="attachments" />
                            </div>
                          </div>
                          <div class="mb-5">
                            <label class="form-label">Comment</label>
                            <div class="comment-editor"></div>
                            <div class="d-flex justify-content-end">
                              <div class="comment-toolbar">
                                <span class="ql-formats me-0">
                                  <button class="ql-bold"></button>
                                  <button class="ql-italic"></button>
                                  <button class="ql-underline"></button>
                                  <button class="ql-link"></button>
                                  <button class="ql-image"></button>
                                </span>
                              </div>
                            </div>
                          </div>
                          <div>
                            <div class="d-flex flex-wrap">
                              <button type="button" class="btn btn-primary me-4" data-bs-dismiss="offcanvas">
                                Update
                              </button>
                              <button type="button" class="btn btn-outline-danger" data-bs-dismiss="offcanvas">
                                Delete
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                      <!-- Activities -->
                      <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Jordan</span> Left the board.</p>
                            <small class="text-muted">Today 11:00 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0">
                              <span>Dianna</span> mentioned <span class="text-primary">@bruce</span> in a comment.
                            </p>
                            <small class="text-muted">Today 10:20 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/2.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Martian</span> added moved Charts & Maps task to the done board.</p>
                            <small class="text-muted">Today 10:00 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Barry</span> Commented on App review task.</p>
                            <small class="text-muted">Today 8:32 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Bruce</span> was assigned task of code review.</p>
                            <small class="text-muted">Today 8:30 PM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0">
                              <span>Clark</span> assigned task UX Research to
                              <span class="text-primary">@martian</span>
                            </p>
                            <small class="text-muted">Today 8:00 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0">
                              <span>Ray</span> Added moved <span>Forms & Tables</span> task from in progress to done.
                            </p>
                            <small class="text-muted">Today 7:45 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Barry</span> Complete all the tasks assigned to him.</p>
                            <small class="text-muted">Today 7:17 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Jordan</span> added task to update new images.</p>
                            <small class="text-muted">Today 7:00 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <img src="../../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0">
                              <span>Dianna</span> moved task <span>FAQ UX</span> from in progress to done board.
                            </p>
                            <small class="text-muted">Today 7:00 AM</small>
                          </div>
                        </div>
                        <div class="media mb-4 d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Clark</span> added new board with name <span>Done</span>.</p>
                            <small class="text-muted">Yesterday 3:00 PM</small>
                          </div>
                        </div>
                        <div class="media d-flex align-items-center">
                          <div class="avatar me-3 flex-shrink-0">
                            <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
                          </div>
                          <div class="media-body ms-1">
                            <p class="mb-0"><span>Bruce</span> added new task in progress board.</p>
                            <small class="text-muted">Yesterday 12:00 PM</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


    </div>
</div>




<?= view("layouts/forms") ?>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

    <script src="<?= base_url(['assets/vendor/libs/moment/moment.js']) ?>"></script>
    <script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
    <script src="<?= base_url(['assets/vendor/libs/select2/select2.js']) ?>"></script>
    <script src="<?= base_url(['assets/vendor/libs/jkanban/jkanban.js']) ?>"></script>
    <script src="<?= base_url(['assets/vendor/libs/quill/katex.js']) ?>"></script>
    <script src="<?= base_url(['assets/vendor/libs/quill/quill.js']) ?>"></script>
    <!-- <script src="<?= base_url(['assets/js/app-kanban.js']) ?>"></script> -->

<script>
    const statesGet = () => <?= json_encode($states) ?>;
</script>
<script src="<?= base_url(['master/js/tasks/functions.js?v='.getCommit()]) ?>"></script>
<script src="<?= base_url(['master/js/tasks/tablero.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
