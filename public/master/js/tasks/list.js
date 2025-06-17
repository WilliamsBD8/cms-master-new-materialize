const loadData = async () => {
    const task_div = $('#tasks');
    const tasks_list = tasksData.reduce((acc, task) => {
        let dates_activity = []
        task.task_activity_date_start ? dates_activity.push(task.task_activity_date_start) : null;
        task.task_activity_date_end ? dates_activity.push(task.task_activity_date_end) : null;

        let color_font = task.task_state_color_font.split(" ");
        color_font = `text-${color_font.join(" text-")}`;

        const list = `
            <li
                data-id="${task.id}"
                class="list-group-item drag-item d-flex justify-content-between align-items-center">
                    <div class="todo-move cursor-move ">
                        ${task.task_state_id != 5 ? `<i class="ri-more-2-fill icon-move"></i>` : ""}
                    </div>
                    <div class="list-left">
                        
                    </div>
                    <div class="list-content  w-100 px-5">
                        <div class="list-title-area w-100 d-flex justify-content-between">
                            <div class="list-title">
                                <b class="indigo-text">Tarea #${task.id}: </b>${task.title} - <span class="grey-text"> (${task.username ? task.username : "Sin Asignar"}) </span>
                                <br><small><b>${task.task_activity_name}:</b> ${task.task_date_start} / ${task.task_date_end}</small>
                            </div>
                            <div class="display-flex align-items-center justify-content-between div-span">
                                <span class="badge ${task.task_state_color_background} ${color_font} ">${task.task_state_name}</span>
    
                                <span class="badge cyan lighten-5 text-cyan ">${task.date_task ? task.date_task : "Sin definir"}</span>
                                <span class="badge deep-orange lighten-5 text-deep-orange ">${task.sprint ? task.sprint : "Sin sprint"}</span>
                            </div>
                        </div>
                        <div class="list-desc">
                            ${task.description ? task.description : ""}
                            
                    <ul class="list-group list-group-flush">
                              <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                                <div class="d-flex flex-wrap align-items-center">
                                  <ul class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                    ${
                                        task.comments.length > 0 ? `
                                            <li onclick="comments(${task.id})" class="avatar pull-up d-inline-flex position-relative" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" data-bs-original-title="Comentarios">
                                                <span class="avatar-initial rounded-circle amber"><i class="ri-question-answer-line"></i></span>
                                                <span class="position-absolute top-80 start-0 translate-middle badge badge-center rounded-pill amber lighten-5 text-amber">${task.comments.length}</span>
                                            </li>
                                        ` : ""
                                    }
                                  
                                    
                                    ${task.files.reduce((acc, file) =>  {
                                        const data = getIcon(file.file.split(".")[1]);
                                        
                                        const li = `
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar pull-up" data-bs-original-title="${file.name}">
                                                <span class="avatar-initial rounded-circle ${data.color} lighten-4"><a class="text-${data.color}" href="${base_url([`uploads/task_${task.id}`,file.file])}" target="_blank"><i class="${data.icon}"></i></a></span>
                                            </li>
                                        `
                                        acc.push(li)
                                        return acc
                                    }, []).join("")}
                                  </ul>
                                </div>
                              </li>
                            </ul>
                        </div>
                    </div>
                    ${ task.task_state_id != 5 ? `
                            <div class="list-right">
                                <div><a class="text-amber" href="javascript:void(0)"  data-bs-toggle="offcanvas" data-bs-target="#canvas-form-comment" aria-controls="offcanvasEnd" onclick="comment_form(${task.id})"><i class="ri-chat-new-line"></i></a></div>
                                <div><a class="text-light-blue" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#canvas-form-edit" aria-controls="offcanvasEnd" onclick="edit_form(${task.id})"><i class="ri-edit-2-line"></i></a></div>
                                <div><a class="delete-task text-red" href="javascript:void(0)" onclick="cancelTask(${task.id})"><i class="ri-delete-bin-5-line"></i></a></div>
                            </div>
                        ` : ""}
            </li>
        `
        acc.push(list)
        return acc
    }, []);

    task_div.html(tasks_list.join(""));

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    Sortable.create(task_div[0], {
        animation: 150,
        handle: "i.icon-move",
        onStart: function (evt, ui) {
            startIndex = evt.oldIndex;
            startId = evt.item.getAttribute("data-id");
        },
        onEnd: async function (event, ui) {
                const endIndex = event.newIndex;
                
                const visibleIds = $("ul#tasks li").map(function () {
                    return $(this).attr("data-id");
                }).get();
                
                const inicio = Math.min(startIndex, endIndex);
                const fin = Math.max(startIndex, endIndex);
                const arr = Array.from({ length: fin - inicio + 1 }, (_, i) => i + inicio);
                const newOrden = visibleIds.filter((t, i) => arr.includes(i));
                
                const newTasks = newOrden.reduce((acc, id) => {

                    $(`li[data-id='${id}'] .list-left`).html(`<i class="ri-refresh-line girando text-blue"></i>`)

                    const task = tasksData.find(t => t.id == id);
                    acc.push(task);
                    return acc;
                }, []);

                const orden = newTasks
                    .map(t => t.orden)
                    .sort((a, b) => b - a);
                    

                const newTasksOrden = newTasks.map((task, i) => ({ ...task, orden: orden[i] }));

                await Promise.all(newTasksOrden.map(async (t) => {
                    const data = {
                        task_id: t.id,
                        edit_task_state: t.task_state_id,
                        edit_task_sprint: t.task_sprint_id,
                        edit_task_activity: t.task_activity_id,
                        edit_task_user: t.task_user_id,
                        edit_task_title: t.title,
                        edit_task_description: t.description,
                        edit_task_orden: t.orden,
                        edit_task_date: t.date_task
                    };

                    const url = base_url(['dashboard', 'task']);
                    const info = await fetchHelper.put(url, data);

                    $(`li[data-id='${t.id}'] .list-left`).html(`<i class="ri-list-check-3 text-green"></i>`);
                    await delay(3000);
                    $(`li[data-id='${t.id}'] .list-left i`).hide().html("");
                }));
                await getTasks();
                loadData();
        }
    })
}

const getIcon = (extencion) => {
    console.log(extencion)
    color = "";
    icon = "";
    switch(extencion){
        case "pdf":
            icon = "ri-file-pdf-2-line";
            color = "red";
            break;
        case "doc":
            icon = "ri-file-word-2-line";
            color = "blue";
            break;
        case "xlsx":
            icon = "ri-file-excel-2-line";
            color = "green";
            break;
        case "ppt":
            icon = "ri-file-ppt-2-line";
            color = "orange";
            break;
        case "txt":
            icon = "ri-file-text-line";
            color = "blue-grey";
            break;
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
        case 'tiff':
        case 'ico':
        case 'webp':
            icon = "ri-image-line";
            color = "amber";
            break;
        default:
            icon = "ri-file-2-line";
            color = "grey";
            break;
    }
    return {icon, color}
}

async function cancelTask(id){
    const task = tasksData.find(t => t.id == id)
    Swal.fire({
        title: `Cancelar Tarea #${task.id}`,
        text: `Una vez cancelado no se podra revertir`,
        icon: "warning",
        showCancelButton: true,
        customClass: {
            confirmButton: 'btn rounded-pill btn-primary waves-effect waves-light',
            cancelButton: 'btn rounded-pill btn-danger',
        },
        buttonsStyling: false,
        confirmButtonText: "Si",
        cancelButtonText: "No"  
    }).then(async result => {
        if(result.isConfirmed){
            const data = {
                task_id:            task.id,
                edit_task_state:    5,
                edit_task_sprint:   task.task_sprint_id,
                edit_task_activity: task.task_activity_id,
                edit_task_user:     task.task_user_id,
                edit_task_company:  task.task_company_id,
                edit_task_title:    task.title,
                edit_task_description:  task.description,
                edit_task_orden:        task.orden,
                edit_task_date:         task.date_task
            };

            const url = base_url(['dashboard/task']);
            const info = await fetchHelper.put(url, data);
            await getTasks();
            await loadData();
        }
    })
}

function comment_form(id){
    $('#form-comment-task #task_id').val(id);
}

function comments(id){

    const task = tasksData.find(t => t.id == id);
    const comments = task.comments;

    $("#canvas-comments .comments ul").html(`
        ${

            comments.reduce((acc, comment) => {

                const li = `
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-success"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-3">
                                <small class="text-muted">${comment.created_at}</small>
                            </div>
                            <p class="mb-2">${comment.comment}</p>

                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-1_5">
                              <div class="d-flex flex-wrap align-items-center">
                                <div class="avatar avatar-sm me-2">
                                  <img src="${comment.photo ? base_url(['assets/upload/images', comment.photo]) : base_url(['assets/img/avatars/1.png'])}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div>
                                  <p class="mb-0 small fw-medium">${comment.user_name}</p>
                                </div>
                              </div>
                            </div>
                        </div>
                    </li>
                `
                acc.push(li)
                return acc;
            }, []).join("")

        }
    `);

    const offcanvasElement = document.getElementById('canvas-comments');
    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
    offcanvas.show();
}

async function sendTaskComment(e) {
    e.preventDefault();
    const {isValid, data} = validData("form-comment-task");
    if(!isValid){
        return alert('Campos obligatorios', 'Por favor llenar los campos requeridos.', 'warning', 5000)
    }
    const url = base_url(['dashboard/task/comment']);
    await fetchHelper.post(url, data);
    await getTasks();
    await loadData();
    $('#canvas-form-comment .btn-close').click();
    return alert('Comentario creado', 'El comentario se añadio existosamente.', 'success', 5000);
}

const offcanvasElementComment = document.getElementById('canvas-form-comment');

if(offcanvasElementComment){
  offcanvasElementComment.addEventListener('shown.bs.offcanvas', function () {
    console.log('El offcanvas se ha abierto');
    
    initQuill("form-comment-task");
  
  });

}