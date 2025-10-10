<?= $this->extend('layouts/page'); ?>

<?= $this->section('styles'); ?>
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/css/pages/page-profile.css']) ?>" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css']) ?>" />
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/typeahead-js/typeahead.css']) ?>" />
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/dropzone/dropzone.css']) ?>" />
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/select2/select2.css']) ?>" />
    <link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />

<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card mb-6">
                    <div class="user-profile-header-banner">
                        <?php $image = mt_rand(1, 20).".png" ?>
                        <img src="
                            <?= base_url(["assets/img/pages/profile-banner.png"]) ?>
                        " alt="Banner image" class="rounded-top">
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-5">
                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                            <img src="<?= $data && $data->photo ? base_url(["upload/images/", $data->photo]) : base_url(["assets/img/avatars", $image]) ?>" alt="user image" class="d-block h-auto ms-0 ms-sm-5 rounded-4 user-profile-img">
                        </div>
                        <div class="flex-grow-1 mt-4 mt-sm-12">
                            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-6">
                                <div class="user-profile-info">
                                    <h4 class="mb-2"><?= $data->name ?></h4>
                                    <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                        <li class="list-inline-item">
                                            <i class="ri-palette-line me-2 ri-24px"></i><span class="fw-medium"><?= $data->role_name ?></span>
                                        </li>
                                        <li class="list-inline-item">
                                            <i class="ri-calendar-schedule-line me-2 ri-24px"></i><span class="fw-medium"><?= formatDate(date('Y-m-d'), strtotime($data->created_at)) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 col-sm-12 col-lg-8">
                <div class="card mb-6 h-100">
                    <h5 class="card-header my-0 pb-0">Actualizar datos de perfil</h5>
                    <!-- Account -->
                    <div class="card-body pt-0">
                        <form id="form-perfile" action="<?= base_url(['dashboard/perfile']) ?>" method="PUT" onsubmit="onSubmit(event, 'form-perfile')">
                        <div class="row mt-1 g-5">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input
                                        class="form-control required"
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="<?= $data->name ?>"
                                        autofocus />
                                    <label for="name">* Nombre</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input
                                    class="form-control required"
                                    type="text"
                                    id="email"
                                    name="email"
                                    value="<?= $data->email ?>"
                                    placeholder="" />
                                    <label for="email">* E-mail</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline required">
                                    <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    value="<?= $data->username ?>" />
                                    <label for="username">* Usuario</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="rol" class="select2 form-select required" <?= $data->id != 1 ? 'disabled' : '' ?>>
                                    <option value="">Seleccionar</option>
                                    <?php foreach($roles as $role): ?>
                                        <option value="<?= $role->id ?>" <?= $role->id == session('user')->role_id ? "selected" : "" ?>><?= "{$role->name}" ?></option>
                                    <?php endforeach ?>
                                    </select>
                                    <label for="rol">* Rol</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="btn btn-primary me-3">Guardar</button>
                        </div>
                        </form>
                    </div>
                    <!-- /Account -->
                </div>
            </div>
            <!-- Basic  -->
            <div class="col-md-12 col-sm-12 col-lg-4">
                <div class="card mb-6 h-100">
                    <h5 class="card-header">Actualizar foto de perfil</h5>
                    <div class="card-body">
                        <form action="<?= base_url(['dashboard/update_photo']) ?>" method="PUT" class="dropzone needsclick" id="dropzone-basic">
                            <div class="dz-message needsclick">
                                Arrastra la foto aquí o haz clic para subirlo
                            </div>
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Basic  -->

            <div class="col-12 mt-5">
                
            <div class="card">
                <h5 class="card-header mb-1">Eliminar cuenta</h5>
                <div class="card-body">
                    <div class="mb-6 col-12 mb-0">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading mb-1">¿Estás seguro de que quieres eliminar tu cuenta?</h6>
                        <p class="mb-0">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate.</p>
                    </div>
                    </div>
                    <form id="formAccountDeactivation" action="<?= base_url(['dashboard/perfile', $data->id]) ?>" method="DELETE" onsubmit="onSubmit(event, 'formAccountDeactivation')">
                        <div class="form-check mb-6">
                            <input
                            class="form-check-input"
                            type="checkbox"
                            name="accountActivation"
                            onchange="changeAccountDesactivation()"
                            id="accountActivation" />
                            <label class="form-check-label" for="accountActivation"
                            >Confirmo la eliminación de mi cuenta</label
                            >
                        </div>
                        <button type="submit" class="btn btn-danger deactivate-account" disabled>
                            Eliminar cuenta
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>

<script src="<?= base_url(['assets/vendor/libs/dropzone/dropzone.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/select2/select2.js']) ?>"></script>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>
    
<script src="<?= base_url(["master/js/functions/functions.js?v=".getCommit()]) ?>"></script>
<script src="<?= base_url(["master/js/functions/fetchHelper.js?v=".getCommit()]) ?>"></script>
<script src="<?= base_url(["master/js/user/perfile.js?v=".getCommit()]) ?>"></script>
<?= $this->endSection(); ?>

