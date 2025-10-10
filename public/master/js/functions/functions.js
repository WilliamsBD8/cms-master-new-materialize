$(() => {
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
})

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

function validData(id_form){
  const form = $(`#${id_form}`);
  const inputs = form.find('input, select, textarea');
  const data = {};
  let isValid = true;
  inputs.each(function () {
      const input = $(this);
      let value = input.val() ? input.val() : "";
      value = /^\d{1,3}(\.\d{3})*,\d{2}$/.test(value)  ? format_number(value) : value;
      const isRequired = input.hasClass('required');
      const isSelect2 = input.hasClass('select2-hidden-accessible');
      const id = input.attr('id');

      if(id != undefined){
    
        if (isRequired && !value){
            isValid = false;
            if (isSelect2) {
              input.next('.select2-container').find('.select2-selection').addClass('invalid');
              value = input.val().trim();
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
            $(this).addClass('invalid');
        } else {
            $(this).removeClass('invalid');
        }

        data[id.replace(/\-/g, "_")] = htmlContent == '<p><br></p>' ? '' : htmlContent;
    }
  });

  return {isValid: isValid, data: data};
}

function base_url(array = []) {
  var url = localStorage.getItem('url');
  if (array.length == 0) return `${url}`;
  else return `${url}${array.join('/')}`;
}

const separador_miles = (numero) => {
  const formatter = new Intl.NumberFormat('es-CO', {
      style: 'decimal',
      minimumFractionDigits: 2,
  });
  return formatter.format(numero);
};

function isLast15DaysOfYear() {
  const today = new Date();
  const lastDay = new Date(today.getFullYear(), 8, 30); // 31 diciembre
  const diffTime = lastDay - today; // diferencia en milisegundos
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // convertir a días
  return diffDays < 15; // true si faltan menos de 15 días
}

const valueFormat = (valor) => {
  const numero = Number(valor);
  if (isNaN(numero)) return valor;

  return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const format_number = (numero) => {
  return parseFloat(numero.replace(/[a-zA-Z]/g, '').replace(/\./g, '').replace(',', '.'));
}

function updateFormattedValue(input) {
  let value = input.value;

  // Remover letras, puntos de miles y convertir coma decimal a punto
  value = value.replace(/[a-zA-Z]/g, '').replace(/\./g, '').replace(',', '.');

  // Convertir el valor en número flotante
  let numericValue = parseFloat(value);

  if (!isNaN(numericValue)) {
      // Formatear el valor como número con separadores de miles
      const formattedValue = separador_miles(numericValue);

      // Posición del cursor antes de actualizar el valor
      const cursorPosition = input.selectionStart;

      // Actualizar el valor del input
      input.value = formattedValue;

      // Restaurar la posición del cursor
      setTimeout(() => {
          input.setSelectionRange(cursorPosition, cursorPosition);
      }, 0);
  }
}


function formatPrice(price){
    const formatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    })
    return formatter.format(price)
}

function formatearMiles(numero) {
  return new Intl.NumberFormat('es-CO').format(numero);
}

function onlyNumericKeypress(event) {
  const input = event.target;
  // Permite dígitos y un solo punto
  input.value = input.value
    .replace(/[^0-9.]/g, '')      // elimina todo excepto números y punto
    .replace(/(\..*)\./g, '$1');  // evita más de un punto decimal
}


async function getColors(){
  
  const colors = []
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

const previewTemplate = () => `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
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
</div>`;