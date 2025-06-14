<?= $this->extend('layouts/page'); ?>

<?= $this->section('title'); ?> - Index<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.css']) ?>" />
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-6">

        <div class="col-12 col-md-4 ps-md-4 ps-lg-6">
            <div class="card h-100 p-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div>
                            <h5 class="mb-1">Tareas</h5>
                            <p class="mb-9">Estados</p>
                        </div>
                    </div>
                    <div id="leadsReportChart"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xxl-8 col-md-8">
            <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Progreso de actividades</h5>
                
            </div>
                <div class="card-body pt-1 pb-1">
                    <ul class="p-0 m-0">
                        <?php
                            $lastKey = array_key_last($activities);
                            foreach($activities as $key => $activity): ?>
                            <li class="d-flex <?= $key !== $lastKey ? "mb-5" : "" ?>">
                                <div
                                class="chart-progress me-1"
                                data-series="0"
                                data-a_id="<?= $activity->id ?>"
                                data-progress_variant="true"></div>
                                <div class="row w-100 align-items-center">
                                    <div class="col-9">
                                        <div class="me-2">
                                            <h6 class="mb-2"><?= $activity->name ?></h6>
                                            <p class="mb-0 small" id="small-activity-<?= $activity->id ?>"></p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Carga de trabajo</h5>
                
            </div>
                <div class="card-body pt-1 pb-1">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-5">
                            <div
                            class="chart-charge me-1"
                            data-series="0"
                            data-a_id=""
                            data-progress_variant="true"></div>
                            <div class="row w-100 align-items-center">
                                <div class="col-9">
                                    <div class="me-2">
                                        <h6 class="mb-2">Sin asignar</h6>
                                        <p class="mb-0 small" id="small-user-"></p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                            $lastKey = array_key_last($users);
                            foreach($users as $key => $user): ?>
                                <li class="d-flex <?= $key !== $lastKey && $key > 0 ? "mb-5" : "" ?>">
                                    <div
                                    class="chart-charge me-1"
                                    data-series="0"
                                    data-a_id="<?= $user->id ?>"
                                    data-progress_variant="true"></div>
                                    <div class="row w-100 align-items-center">
                                        <div class="col-9">
                                            <div class="me-2">
                                                <h6 class="mb-2"><?= $user->name ?></h6>
                                                <p class="mb-0 small" id="small-user-<?= $user->id ?>"></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-xxl-6 col-md-6">
            <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Progreso de tareas</h5>
                
            </div>
                <div class="card-body pt-1 pb-1">
                    <ul class="p-0 m-0">
                        <!-- <li class="d-flex mb-8">
                            <div
                            class="chart-progress-user me-1"
                            data-series="0"
                            data-a_id=""
                            data-progress_variant="true"></div>
                            <div class="row w-100 align-items-center">
                                <div class="col-9">
                                    <div class="me-2">
                                        <h6 class="mb-2">Sin asignar</h6>
                                        <p class="mb-0 small" id="small-user-progress-"></p>
                                    </div>
                                </div>
                            </div>
                        </li> -->
                        <?php
                            $lastKey = array_key_last($users);
                            foreach($users as $key => $user): ?>
                                <li class="d-flex <?= $key !== $lastKey && $key > 0 ? "mb-5" : "" ?>">
                                    <div
                                    class="chart-progress-user me-1"
                                    data-series="0"
                                    data-a_id="<?= $user->id ?>"
                                    data-progress_variant="true"></div>
                                    <div class="row w-100 align-items-center">
                                        <div class="col-9">
                                            <div class="me-2">
                                                <h6 class="mb-2"><?= $user->name ?></h6>
                                                <p class="mb-0 small" id="small-user-progress-<?= $user->id ?>"></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('javaScripts'); ?>
<script src="<?= base_url(['assets/vendor/libs/flatpickr/flatpickr.js']) ?>"></script>

<script src="<?= base_url(['assets/vendor/libs/apex-charts/apexcharts.js']) ?>"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script>
    const statesData = () => <?= json_encode($states) ?>;
</script>

    <!-- <script src="<?= base_url(['assets/js/app-academy-dashboard.js']) ?>"></script> -->
<script src="<?= base_url(['master/js/tasks/functions.js?v='.getCommit()]) ?>"></script>
<script src="<?= base_url(['master/js/tasks/index.js?v='.getCommit()]) ?>"></script>
<?= $this->endSection(); ?>
