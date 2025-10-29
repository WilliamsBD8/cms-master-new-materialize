
let url_base;

function load_datatable(url, columns, buttons = [], url_page){
    url_base = url_page;
    let buttons_default = default_buttons();
    buttons = [...buttons_default, ...buttons];

    table_datatable[0] = $(`#table_datatable`).DataTable({
        ajax: {
            url: base_url([url]),
            data: function(d) {
                // d.date_init     = $('#date_init').val();
                $('#form-filter').serializeArray().forEach(field => {
                    d[field.name] = field.value;
                });
            },
            dataSrc: 'data',
            error: function (xhr, error, thrown) {
                console.error("Error en la petición AJAX:", error, thrown);
                console.log("Respuesta del servidor:", xhr.responseText);
    
                // Ejemplo: mostrar alerta con SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la carga',
                    text: 'No se pudieron obtener los datos del servidor',
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect'
                    },
                });
            }
        },
        columns,
        dom: '<"card-header flex-column flex-md-row border-bottom"<"head-label text-center"><"dt-action-buttons text-end pt-0 pt-md-0"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        responsive: false,
        scrollX: true,
        scrollY: false,
        ordering: false,
        processing: true,
        serverSide: true,
        drawCallback: async function(setting){
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl);
            });            

            Swal.close();
            setTimeout(() => {
                this.api().columns.adjust();
            }, 300);

        },
        initComplete: async () => {
        },


        buttons
    });
}

function load_datatable_total(columns, data, buttons = []){
    table_datatable[0] = $(`#table_datatable`).DataTable({
        data,
        columns,
        dom: '<"card-header flex-column flex-md-row border-bottom"<"head-label text-center"><"dt-action-buttons text-end pt-0 pt-md-0"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        responsive: false,
        scrollX: true,
        scrollY: false,
        ordering: false,
        processing: true,
        serverSide: false,
        drawCallback: async function(setting){
        },
        initComplete: async () => {
            // await indicadores();
        },
        buttons
    });
}

const exportConfig = {
    format: {
      body: function (inner, coldex, rowdex) {
        if (inner.length <= 0) return inner;
        var el = $.parseHTML(inner);
        var result = '';
        $.each(el, function (index, item) {
          if (item.classList !== undefined && item.classList.contains('user-name')) {
            result += item.lastChild.firstChild.textContent;
          } else if (item.innerText === undefined) {
            result += item.textContent;
          } else {
            result += item.innerText;
          }
        });
        return result;
      }
    }
  };

function default_buttons(){
    const buttons = [
        {
            extend: 'excel',
            text: '<i class="ri-file-excel-line me-1"></i><span class="d-none d-sm-inline-block">Excel</span>',
            className: `btn rounded-pill btn-label-success waves-effect mx-2 mt-2`,
            filename: `Reporte_${info_page.title.replace(/\s+/g, "_").toLowerCase()}`,
            title: `Reporte de ${info_page.title}`,
            action: async function (e, dt, button, config) {
        
                // 🔹 Traer columnas visibles
                const visibleColumns = dt.columns(':visible').indexes().toArray();
        
                const selected = await sweetAlertExport(visibleColumns, dt)
        
                // Si no selecciona nada o cancela
                if (!selected || selected.length === 0) {
                    return;
                }

                config.exportOptions = {
                    ...exportConfig,
                    columns: selected
                };

                const getData = {
                    length:         -1,
                }

                const url = base_url(['dashboard/brand_portfolio/data'], getData);
                const {data: dataExport} = await fetchHelper.get(url);

                // 🔹 Recargar datos temporalmente
                dt.clear();
                dt.rows.add(dataExport);
                dt.draw();
        
                // 🔹 Ejecutar exportación normal de Excel
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
            }
        },
        {
            extend: 'csv',
            text: '<i class="ri-file-text-line me-1"></i><span class="d-none d-sm-inline-block">CSV</span>',
            className: 'btn rounded-pill btn-label-info waves-effect mx-2 mt-2',
            filename: `Reporte_${info_page.title.replace(/\s+/g, "_").toLowerCase()}`,
            title: `Reporte de ${info_page.title}`,
            action: async function (e, dt, button, config) {
                // 🔹 Traer columnas visibles
                const visibleColumns = dt.columns(':visible').indexes().toArray();
            
                const selected = await sweetAlertExport(visibleColumns, dt)
        
                // Si no selecciona nada o cancela
                if (!selected || selected.length === 0) {
                    return;
                }

                config.exportOptions = {
                    ...exportConfig,
                    columns: selected
                };

                const getData = {
                    length:         -1,
                }

                const url = base_url(['dashboard/brand_portfolio/data'], getData);
                const {data: dataExport} = await fetchHelper.get(url);

                // 🔹 Recargar datos temporalmente
                dt.clear();
                dt.rows.add(dataExport);
                dt.draw();
            
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
            }
        },
        {
            extend: 'pdf',
            text: '<i class="ri-file-pdf-2-line me-1"></i><span class="d-none d-sm-inline-block">PDF</span>',
            className: 'btn rounded-pill btn-label-danger waves-effect mx-2 mt-2',
            filename: `Reporte_${info_page.title.replace(/\s+/g, "_").toLowerCase()}`,
            title: `Reporte de ${info_page.title}`,
            action: async function (e, dt, button, config) {
                // 🔹 Traer columnas visibles
                const visibleColumns = dt.columns(':visible').indexes().toArray();
            
                const selected = await sweetAlertExport(visibleColumns, dt)
        
                // Si no selecciona nada o cancela
                if (!selected || selected.length === 0) {
                    return;
                }

                config.exportOptions = {
                    ...exportConfig,
                    columns: selected
                };

                const getData = {
                    length:         -1,
                }

                const url = base_url(['dashboard/brand_portfolio/data'], getData);
                const {data: dataExport} = await fetchHelper.get(url);

                // 🔹 Recargar datos temporalmente
                dt.clear();
                dt.rows.add(dataExport);
                dt.draw();
            
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
            }
        }
    ].filter(Boolean);

    return buttons;
}

function reloadTable(){
    table_datatable[0].ajax.reload();
}

async function sendFilter(e){
    e.preventDefault();
    Swal.fire({
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: {},
        // timer: time,
        willOpen: function () {
            Swal.showLoading();
        }
    });
    $('#canvasFilter .btn-close').click();
    await reloadTable();

}

function getDataDT(){
    return table_datatable[0].rows().data().toArray();
}

async function sweetAlertExport(visibleColumns, dt){
    // 🔹 Armar HTML con checkboxes
    let html = '<div style="text-align:left">';
    visibleColumns.forEach(i => {
        const colTitle = dt.column(i).header().textContent.trim();
        // Última columna (acciones) la puedes excluir si quieres
        if (i !== visibleColumns[visibleColumns.length - 1] && colTitle != "") {
            html += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="col_${i}" value="${i}" checked>
                    <label class="form-check-label" for="col_${i}">${colTitle}</label>
                </div>`;
        }
    });
    html += '</div>';

    // 🔹 Mostrar SweetAlert con checkboxes
    const { value: selected } = await Swal.fire({
        title: 'Selecciona las columnas a exportar',
        html: html,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Exportar',
        preConfirm: () => {
            return [...document.querySelectorAll('input[type=checkbox]:checked')]
                .map(cb => parseInt(cb.value));
        }
    });

    return selected;
}