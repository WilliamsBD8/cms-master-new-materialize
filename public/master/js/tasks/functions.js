const tasksData = []
const colors = [];

async function getTasks(headers = {}, time = 0){
    const url = base_url(['dashboard/task/data']);
    const {tasks} = await fetchHelper.get(url, headers, time);
    tasksData.length = 0;
    tasksData.push(...tasks);
}

const sendTask = async (e) => {
    e.preventDefault();
    const {isValid, data} = validData("form-add-task");
    if(!isValid){
        return alert('Campos obligatorios', 'Por favor llenar los campos requeridos.', 'warning', 5000)
    }
    data.files = dropzoneFiles['add-task-files'];
    const url = base_url(['dashboard/task']);
    await fetchHelper.post(url, data, {}, 500);
    await getTasks();
    loadData();
    $('#canvas-form .btn-close').click();
    return alert('Tarea creada', 'La tarea se añadio existosamente.', 'success', 5000);
}

const sendTaskEdit = async (e) => {
    e.preventDefault();
    const {isValid, data} = validData("form-edit-task");
    if(!isValid){
        return alert('Campos obligatorios', 'Por favor llenar los campos requeridos.', 'warning', 5000)
    }
    data.files = dropzoneFiles['edit-task-files'];
    const url = base_url(['dashboard/task']);
    await fetchHelper.put(url, data, {}, 500);
    await getTasks();
    loadData();
    $('#canvas-form-edit .btn-close').click();
    return alert('Tarea editada', 'La tarea se edito existosamente.', 'success', 5000)
}

const edit_form = async (id) => {
    const task = tasksData.find(t => t.id == id)
    $('#edit-task-title').val(task.title);
    $('#edit-task-description').attr('data-description', task.description);
    $('#edit-task-sprint').val(task.task_sprint_id).trigger('change');
    $('#edit-task-activity').val(task.task_activity_id).trigger('change');
    $('#edit-task-user').val(task.task_user_id).trigger('change');
    $('#edit-task-date').val(task.date_task);
    $('#task_id').val(task.id);
    dropzoneFiles['edit-task-files'] = task.files;
}

const sendFilter = async (e) => {
    e.preventDefault();
}

async function getColors(){

    const json_color = base_url(['assets/json/colors.json'])
    const colores = await fetchHelper.get(json_color);

    $.each(colores, function(nombreColor, valor) {
        if (typeof valor === "string") {
            colors.push({ color: nombreColor, hex: valor });
        } else {
            $.each(valor, function(tono, hex) {
                const nombreCompleto = tono === 'base' ? nombreColor : `${nombreColor} ${tono}`;
                colors.push({ color: nombreCompleto, hex: hex });
            });
        }
    });
    return colors;
}

window.addEventListener('load', async function () {
    await getTasks({}, 500);
    await getColors();
    loadData();
    
})