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
                        </div>
                    </div>
                    ${ task.task_state_id != 5 ? `
                            <div class="list-right">
                                <div><a class="text-light-blue" href="javascript:void(0)"  data-bs-toggle="offcanvas" data-bs-target="#canvas-form-edit" aria-controls="offcanvasEnd" onclick="edit_form(${task.id})"><i class="ri-edit-2-line"></i></a></div>
                                <div><a class="delete-task text-red" href="javascript:void(0)" onclick="cancelTask(${task.id})"><i class="ri-delete-bin-5-line"></i></a></div>
                            </div>
                        ` : ""}
            </li>
        `
        acc.push(list)
        return acc
    }, []);

    task_div.html(tasks_list.join(""));
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