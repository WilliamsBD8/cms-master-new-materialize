const quillInstances = {};
const dropzoneFiles = {
  'add-task-files': [],
  'edit-task-files': []
};

function proceso_fetch(url, data, method = 'POST') {
  return fetch(url, {
      method: method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
  }).then(response => {
      if (!response.ok) throw Error(response.status);
      return response.json();
  }).catch(error => {
    console.error(error);
      // alert('<span class="red-text">Error en la consulta</span>', 'red lighten-5');
  });
}

function proceso_fetch_get(url) {
  return fetch(url).then(response => {
      if (!response.ok) throw Error(response.status);
      return response.json();
  }).catch(error => {
    console.error(error);
    // alert('Error en la consulta', 'red');
  });
}

function alert(title = 'Alert', msg = 'Alert', icon = 'success', time=0, maxOpened = 5){
  var shortCutFunction = icon,
      prePositionClass = 'toast-top-right';

  prePositionClass =
      typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
  toastr.options = {
      maxOpened,
      autoDismiss: true,
      closeButton: true,
      newestOnTop: true,
      progressBar: false,
      preventDuplicates: true,
      timeOut: time,             // Duración en milisegundos (0 significa que no se cierra automáticamente)
      extendedTimeOut: time,
      onclick: null,
      tapToDismiss: true,
  };
  var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
  if (typeof $toast === 'undefined') {
    return;
  }
}

function base_url(array = []) {
  var url = localStorage.getItem('url');
  if (array.length == 0) return `${url}`;
  else return `${url}${array.join('/')}`;
}

function validData(id_form){
    const form = $(`#${id_form}`);
    const inputs = form.find('input, select, textarea');
    const data = {};
    let isValid = true;
    inputs.each(function () {
        const input = $(this);
        const value = input.val() ? input.val().trim() : "";
        const isRequired = input.hasClass('required');
        const isSelect2 = input.hasClass('select2-hidden-accessible');
        const id = input.attr('id');

        if(id != undefined){
      
          if (isRequired && !value){
              isValid = false;
              if (isSelect2) {
                  input.next('.select2-container').find('.select2-selection').addClass('invalid');
              } else {
                  input.addClass('invalid');
              }
          } else {
              if (isSelect2) {
                  input.next('.select2-container').find('.select2-selection').removeClass('invalid');
              } else {
                  input.removeClass('invalid');
              }
          }
          data[input.attr('id').replace(/\-/g, "_")] = value;

        }

    });

    $(`#${id_form} .full-editor`).each(function () {
      const id = $(this).attr('id');
      const quill = quillInstances[id];
      if (quill) {
          const htmlContent = quill.root.innerHTML.trim();
          const plainText = quill.getText().trim();
          const isRequired = $(this).hasClass('required');

          if (isRequired && plainText === '') {
              isValid = false;
              quill.root.classList.add('invalid');
          } else {
              quill.root.classList.remove('invalid');
          }

          data[id.replace(/\-/g, "_")] = htmlContent;
      }
  });

    return {isValid: isValid, data: data};
}

function truncateText(text, maxLength) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    } else {
        return text;
    }
}

function getFirstLetters(phrase) {
    const words = phrase.split(' '); // Dividir la frase en palabras
    let result = ''; // Inicializar la cadena resultante

    // Verificar si hay al menos dos palabras
    if (words.length > 0) {
        result += words[0].charAt(0).toUpperCase(); // Tomar la primera letra de la primera palabra y ponerla en mayúsculas
    }
    
    if (words.length > 1) {
        result += words[1].charAt(0).toUpperCase(); // Tomar la primera letra de la segunda palabra y ponerla en mayúsculas
    }

    return result;
}

function initQuill(form = "form-add-task", reference = 'full-editor'){
  const fullToolbar = [
    [
        {
            font: []
        },
        {
            size: []
        }
    ],
    ['bold', 'italic', 'underline', 'strike'],
    [
        {
            color: []
        },
        {
            background: []
        }
    ],
    [
        {
            script: 'super'
        },
        {
            script: 'sub'
        }
    ],
    [
        {
            header: '1'
        },
        {
            header: '2'
        },
        'blockquote',
        'code-block'
    ],
    [
        {
            list: 'ordered'
        },
        {
            list: 'bullet'
        },
        {
            indent: '-1'
        },
        {
            indent: '+1'
        }
    ],
    [{ direction: 'rtl' }],
    ['link', 'formula'],
    ['clean']
  ];
  const elements = document.querySelectorAll(`#${form} .${reference}`);

  elements.forEach((el) => {
    const id = el.getAttribute('id');
    if (!id) return;

    const description = el.getAttribute('data-description');

    $(`#${form} .container-editor`).html(`
      <div class="full-editor" id="${id}">${description ?? ""}</div>  
    `);

    const editor = new Quill(`#${id}`, {
      bounds: `#${id}`,
      placeholder: 'Descripción...',
      modules: {
        formula: true,
        toolbar: fullToolbar
      },
      theme: 'snow'
    });

    quillInstances[id] = editor;
    
  });
}

const previewTemplate = `
<div class="dz-preview dz-file-preview">
  <div class="dz-details">
    <div class="dz-thumbnail">
      <img data-dz-thumbnail>
      <div class="dz-success-mark"></div>
      <div class="dz-error-mark"></div>
      <div class="dz-error-message"><span data-dz-errormessage></span></div>
      <div class="progress">
        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
      </div>
    </div>
    <div class="dz-filename" data-dz-name></div>
    <div class="dz-size" data-dz-size></div>
  </div>
</div>
`;


function initDropzone(reference = 'dropzone'){

  const elements = document.querySelectorAll(`.${reference}`);
  elements.forEach((element) => {
    const id = element.getAttribute('id');
    if (!id) return;
    
    const existingDZ = Dropzone.instances.find(dz => dz.element === element);
    if (existingDZ) existingDZ.destroy();
    $('.dz-preview.dz-file-preview.dz-complete').remove();

    new Dropzone(`#${id}`, {
      url: "/", // no se usa, porque no subiremos al servidor
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 5,
      addRemoveLinks: true,
        init: function () {
          const dz = this;
          this.on("addedfile", function (file) {
              if (file.id) return;
              const reader = new FileReader();
              reader.onload = function (event) {
                  dropzoneFiles[id].push({
                      name: file.name,
                      base64: event.target.result
                  });
              };
              reader.readAsDataURL(file);
          });

          this.on("removedfile", function (file) {
            if(file.id)
              dropzoneFiles[id].map(f => {
                if(f.id == file.id) f.onDelete = true;
              })
            else
              dropzoneFiles[id] = dropzoneFiles[id].filter(f => f.name !== file.name);

            console.log(dropzoneFiles[id])
          });
          dropzoneFiles[id].map(file => {
            if(file.onDelete) return;
            const mockFile = {
              name: file.name,
              size: file.size,
              accepted: true,
              id: file.id,
              status: 'success',
            };
            dz.emit("addedfile", mockFile);
            dz.emit("complete", mockFile);
          })
        }
    });
  });
}

const offcanvasElement = document.getElementById('canvas-form');

if(offcanvasElement){
  offcanvasElement.addEventListener('shown.bs.offcanvas', function () {
    console.log('El offcanvas se ha abierto');
    const $selects = $(offcanvasElement).find('select');
  
    $(offcanvasElement).find('input, select, textarea').each(function () {
      const $field = $(this);
      
      // Para selects, vaciar selección y disparar change
      if ($field.is('select')) {
        $field.val("").trigger('change');
      }
      // Para inputs o textarea, vaciar y disparar change si es necesario
      else if ($field.is('input, textarea')) {
        $field.val('');
        $field.trigger('change');
      }
    });
  
    $selects.each(function () {
      const $select = $(this);
  
      // Solo inicializa si no está ya inicializado
      if (!$select.hasClass("select2-hidden-accessible")) {
        const isRequired = $select.hasClass('required');
        $select.select2({
          dropdownParent: $(offcanvasElement),
          placeholder: $select.attr('placeholder') || "Selecciona una opción",
          allowClear: !isRequired,
          width: '100%'
        });
      }
    });
  
    const flatpickrDate = document.querySelector('#add-task-date')
  
    if (flatpickrDate) {
      flatpickrDate.flatpickr({
        monthSelectorType: 'static'
      });
    }

    delay(500);

    initQuill();

    initDropzone();
    

  
  });
  
  offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
    const $selects = $(offcanvasElement).find('select.select2-hidden-accessible');
    $selects.each(function () {
      $(this).select2('destroy');
    });
    
  });
}

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));


const offcanvasElementEdit = document.getElementById('canvas-form-edit');

if(offcanvasElementEdit){
  offcanvasElementEdit.addEventListener('shown.bs.offcanvas', function () {
    console.log('El offcanvas se ha abierto');
    const $selects = $(offcanvasElementEdit).find('select');
  
    $selects.each(function () {
      const $select = $(this);
  
      // Solo inicializa si no está ya inicializado
      if (!$select.hasClass("select2-hidden-accessible")) {
        const isRequired = $select.hasClass('required');
        $select.select2({
          dropdownParent: $(offcanvasElementEdit),
          placeholder: $select.attr('placeholder') || "Selecciona una opción",
          allowClear: !isRequired,
          width: '100%'
        });
      }
    });
  
    const flatpickrDate = document.querySelector('#edit-task-date')
  
    if (flatpickrDate) {
      flatpickrDate.flatpickr({
        monthSelectorType: 'static'
      });
    }
    initQuill("form-edit-task");

    initDropzone();
    
  
  });
  
  offcanvasElementEdit.addEventListener('hidden.bs.offcanvas', function () {
    
  
    $(offcanvasElementEdit).find('input, select, textarea').each(function () {
      const $field = $(this);
      
      // Para selects, vaciar selección y disparar change
      if ($field.is('select')) {
        $field.val("").trigger('change');
      }
      // Para inputs o textarea, vaciar y disparar change si es necesario
      else if ($field.is('input, textarea')) {
        $field.val('');
        $field.trigger('change');
      }
    });
  
    const $selects = $(offcanvasElementEdit).find('select.select2-hidden-accessible');
    $selects.each(function () {
      $(this).select2('destroy');
    });
    
    dropzoneFiles['edit-task-files'] = [];
    console.log(dropzoneFiles['edit-task-files'])
    
  });

}
