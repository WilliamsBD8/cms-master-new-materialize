const states = statesGet();

async function loadData(){

    const boards = states.reduce((acc, state) => {
        acc.push({...state,id:`state-${state.id}`, title:state.name, item:tasksData.filter(t => t.task_state_id == state.id).map(t => ({...t, id: `id-task-${t.id}`}))})
        return acc
    }, [])

    $('.kanban-wrapper').html("")

    const kanban = new jKanban({
        element: '.kanban-wrapper',
        gutter: '12px',
        widthBoard: '250px',
        dragItems: true,
        boards: boards,
        dragBoards: true,
        addItemButton: false,
        click: function (el) {
            const id = $(el).attr("data-eid").split("id-task-").pop();
            edit_form(id);
            const offCanvasElement = document.querySelector('#canvas-form-edit');
            let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
            offCanvasEl.show();
        },
        dropEl: async function (el, target, source, sibling) {

                const reinit = [target, source]
                const itemId = el.dataset.eid.split("id-task-").pop(); // ID del item que fue movido
                const sourceBoard = source.parentElement.dataset.id; // ID de la columna origen
                const targetBoard = target.parentElement.dataset.id; // ID de la columna destino

                const task = tasksData.find(t => t.id == itemId)

                const [origen, valorOrigen] = sourceBoard.split('-');
                const [destino, valorDestino] = targetBoard.split('-');
        
                console.log("🔄 Item movido:", itemId);
                console.log("🔙 De:", sourceBoard);
                console.log("➡️ A:", targetBoard);

                let actualizar = true;

                console.log(origen);

                if(origen == "state" || destino == "state"){ // Reglas para actualizar datos
                    if(valorOrigen == 1 || valorDestino == 1){
                        const sourceList = source.parentElement.querySelector('.kanban-drag');
                        sourceList.appendChild(el);
                        actualizar = false;
                    }
                }

                if((valorOrigen != valorDestino) && actualizar){
                    const data_edit = {
                        task_id:            task.id,
                        edit_task_state:    origen == "state" ? valorDestino : task.task_state_id,
                        edit_task_sprint:   origen == "sprint" ? valorDestino : task.task_sprint_id,
                        edit_task_activity: origen == "activity" ? (valorDestino == 0 ? "" : valorDestino) : task.task_activity_id,
                        edit_task_user:     origen == "user" ? (valorDestino == 0 ? "" : valorDestino) : task.task_user_id,
                        edit_task_title:    task.title,
                        edit_task_description:  task.description,
                        edit_task_orden:        task.orden,
                        edit_task_date:         task.date_task
                    };
        
                    const url = base_url(['dashboard/task']);
                    await fetchHelper.put(url, data_edit, {}, 500);

                //     newTask.name=  newTask.username;
                //     newTask.activity=  newTask.task_activity_name;
                //     newTask.startDate = newTask.date_task != null ? newTask.date_task : "Sin Definir";
                //     newTask.status=  newTask.task_state_id;
                //     // templateKanban(newTask, newTask.id);
                
                    await getTasks();
                    loadData();
                    alert(`Tarea actualizada`, "La tarea fue actualizada con exito", "success", 5000);
                }


                // reinit.map(r => {
                //     const targetItems = Array.from(r.querySelectorAll(".kanban-item"));
        
                //     // 🧠 Ordenar por `orden` de mayor a menor según tus datos en `tasks`
                //     targetItems.sort((a, b) => {
                //         const taskA = tasks.find(t => t.id == a.dataset.eid);
                //         const taskB = tasks.find(t => t.id == b.dataset.eid);
                //         return (taskB?.orden || 0) - (taskA?.orden || 0);
                //     });
        
                //     // 🛠️ Reinsertar los elementos en el DOM en el nuevo orden
                //     targetItems.forEach(item => r.appendChild(item));
                // })
        
            }
            
    });

    boards.map(b => {
        console.log(b);
        let color_font = b.color_font.split(" ");
        color_font = `text-${color_font.join(" text-")}`;
        $(`.kanban-board[data-id='${b.id}']`).find(".kanban-board-header").addClass(`${b.color_background} ${color_font} w-100 mb-2 p-2 text-center border-radius`);
        $(`.kanban-board[data-id='${b.id}']`).find(".kanban-title-board").addClass(`w-100 text-center`);
        b.item.map(i => {
            const id = i.id.split("id-task-").pop();
            templateKanban(i, id)
        })
    })

}

function templateKanban(kanban_item, kanban_id){
    const div_kanban = $(`.kanban-item[data-eid="${kanban_item.id}"]`);

    $(div_kanban).html(
        `
            <p class="text-primary text-center title mt-0 mb-1">Tarea #${kanban_id}</p>
            ${
                kanban_item.title ? `<p class="mt-1 mb-0">${truncateText(kanban_item.title, 28)}</p>` : ""
            }

            <div class="kanban-footer mt-3">

                ${
                    kanban_item.task_activity_name != null && kanban_item.task_activity_name != "" ? `
                        <div class="kanban-due-date center mb-1 light-blue lighten-4 text-center">
                            <span class="badge text-blue">${kanban_item.task_activity_name}</span>
                        </div>` : ``
                }
                ${
                    kanban_item.date_task != null && kanban_item.date_task != "" ? `
                        <div class="kanban-due-date center mb-1 light-blue lighten-5 text-center">
                            <span class="badge text-blue">${kanban_item.date_task}</span>
                        </div>`
                        : ``
                }
                <div class="kanban-footer-left left display-flex pt-1">
                
                
                
                
                </div>
                <div class="kanban-footer-right right display-flex align-item-center w-100" style="justify-content: space-between;width: 100%;">
                    ${
                        kanban_item.sprint != null && kanban_item.sprint != "" ? `
                            <div class="kanban-due-date center mb-1 blue-grey lighten-4 text-center">
                                <span class="badge text-blue-grey">${kanban_item.sprint}</span>
                            </div>` : ``
                    }
                    ${
                        kanban_item.task_state_id != 1 ? `
                            <div class="kanban-users ml-2 ">
                                <div class="flex-shrink-0 avatar">
                                    <span class="avatar-initial lighten-5 rounded-circle ${kanban_item.username ? "green text-green" : "grey text-grey"}">${kanban_item.username ? getFirstLetters(kanban_item.username) : "S/A"}</span>
                                </div>
                            </div>
                        ` : ""
                    }
                </div>
            </div>
        `
        );
}