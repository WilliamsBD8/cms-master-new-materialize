$(() => {
    const dropzoneBasic = document.querySelector('#dropzone-basic');
    if (dropzoneBasic) {
        const myDropzone = new Dropzone(dropzoneBasic, {
            previewTemplate: previewTemplate(),
            parallelUploads: 1,
            maxFilesize: 5,
            addRemoveLinks: true,
            maxFiles: 1
        });
    }

    const select2 = $('.form-select');
    
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            const placeholder = $this.attr('placeholder') || 'Seleccione una opción';
            select2Focus($this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder,
                dropdownParent: $this.parent()
            });
        });
    }
});

async function onSubmit(e, id_form){
    e.preventDefault();
    const form = $(`#${id_form}`);
    const url = form.attr('action');
    const {isValid, data} = validData(id_form);
    if(!isValid){
        alert('Campos obligatorios', 'Por favor llenar los campos requeridos.', 'warning', 5000);
        return false;
    }
    $('input').removeClass('invalid');

    const method = form.attr('method').toLowerCase();
    let res;
    switch (method) {
        case 'put':
            res = await fetchHelper.put(url, data, {}, 500)
            break;
        case 'delete':
            res = await fetchHelper.delete(url, data, {}, 500)
            break;
    
        default:
            break;
    }

    switch (res.status) {
        case 'error':
            console.log(res.errors);
            Object.entries(res.errors).map(([campo, mensaje]) => {
                $(`#${campo}`).addClass('invalid');
                alert(res.title, mensaje, 'error');
            });
            break;
    
        default:
            Swal.fire({
                title: 'Datos actualizados con éxito.',
                text: res.message,
                icon: "success",
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect'
                },
            })
            break;
    }
}

function changeAccountDesactivation() {
    const checkbox = document.getElementById('accountActivation');
    if (checkbox.checked) {
      $('.deactivate-account').attr('disabled', false);
    } else {
      $('.deactivate-account').attr('disabled', true);
    }
  }