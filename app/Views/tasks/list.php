<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Lista<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />

<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/typeahead-js/typeahead.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/typography.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/katex.css']) ?>" />
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/quill/editor.css']) ?>" />

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
                        Lista de tareas
                    </h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <button class="btn btn-primary waves-effect waves-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#canvas-form" aria-controls="offcanvasEnd">
                                Añadir Tarea
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xxl-12">
            <div class="row mb-6">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-12 mb-md-0 mb-6">
                                    <ul class="list-group list-group-flush" id="tasks">
                                        <!-- <li
                                            class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                                            <span>Buy products.</span>
                                            <img
                                            class="rounded-circle"
                                            src="../../assets/img/avatars/1.png"
                                            alt="avatar"
                                            height="32"
                                            width="32" />
                                        </li>
                                        <li
                                            class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                                            <span>Reply to emails.</span>
                                            <img
                                            class="rounded-circle"
                                            src="../../assets/img/avatars/2.png"
                                            alt="avatar"
                                            height="32"
                                            width="32" />
                                        </li>
                                        <li
                                            class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                                            <span>Write blog post.</span>
                                            <img
                                            class="rounded-circle"
                                            src="../../assets/img/avatars/3.png"
                                            alt="avatar"
                                            height="32"
                                            width="32" />
                                        </li>
                                        <li
                                            class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                                            <span>Update packages.</span>
                                            <img
                                            class="rounded-circle"
                                            src="../../assets/img/avatars/4.png"
                                            alt="avatar"
                                            height="32"
                                            width="32" />
                                        </li>
                                        <li
                                            class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">
                                            <span>New blog layout.</span>
                                            <img
                                            class="rounded-circle"
                                            src="../../assets/img/avatars/5.png"
                                            alt="avatar"
                                            height="32"
                                            width="32" />
                                        </li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-md-12">
                    </div>
                </div>
            </div> -->
        </div>


    </div>
</div>

<?= view("layouts/forms") ?>


<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/quill/katex.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/quill/quill.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script>
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="<?= base_url(['master/js/tasks/functions.js?v='.getCommit()]) ?>"></script>
<script src="<?= base_url(['master/js/tasks/list.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
