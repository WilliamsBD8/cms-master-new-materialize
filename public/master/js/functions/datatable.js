
const info_page = infoPage();
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

            const indicadores = setting.json.indicadores;
            if(indicadores){
                const info_indicadores = indicadoresMovimientos(indicadores);
                $('.indicadores').html(info_indicadores)
            }

            if ($('input[name="date_init"]').length && $('input[name="date_end"]').length) {
                const dates = [];
            
                if ($('#date_init').val() !== "")
                    dates.push(`Desde ${$('#date_init').val()}`);
            
                if ($('#date_end').val() !== "")
                    dates.push(`Hasta ${$('#date_end').val()}`);
            
                if ($("#title-page").length) {
                    $("#title-page").html(`${info.title}<br><small>${dates.join(" / ")}</small>`);
                }
            }
            

            Swal.close();
            setTimeout(() => {
                this.api().columns.adjust();
            }, 300);

        },
        initComplete: async () => {
            if("form_filter" in info_page){
                if(info_page.form_filter.length != 0){
                    const select2 = $('.form-select');
    
                    if (select2.length) {
                        select2.each(function () {
                            var $this = $(this);
                            const placeholder = $this.attr('placeholder') || 'Seleccione una opción';
                            const allowNew = $this.hasClass('allow-new');
                            select2Focus($this);
                            $this.wrap('<div class="position-relative"></div>').select2({
                                placeholder,
                                dropdownParent: $this.parent(),
                                tags: allowNew
                            });
                        });
                    }
    
                    $('.date-input').flatpickr({
                        locale:             "es",
                        monthSelectorType:  'dropdown',
                    });
                }
            }
            
            await indicadores();
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

function default_buttons(){
    const buttons = [
        {
            extend: 'excel',
            text: '<i class="ri-file-excel-line me-1"></i><span class="d-none d-sm-inline-block">Excel</span>',
            className: `btn rounded-pill btn-label-success waves-effect mx-2 mt-2 ${user.role_id == 3 ? 'd-none' : ''}`,
            filename: `Reporte_${info_page.title.replace(/\s+/g, "_").toLowerCase()}`,
            title: `Reporte de ${info_page.title}`,
            action: async function (e, dt, button, config) {
        
                // 🔹 Traer columnas visibles
                const visibleColumns = dt.columns(':visible').indexes().toArray();
        
                // 🔹 Armar HTML con checkboxes
                let html = '<div style="text-align:left">';
                visibleColumns.forEach(i => {
                    const colTitle = dt.column(i).header().textContent.trim();
                    // Última columna (acciones) la puedes excluir si quieres
                    if (i !== visibleColumns[visibleColumns.length - 1]) {
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
        
                // Si no selecciona nada o cancela
                if (!selected || selected.length === 0) {
                    return;
                }
        
                // 🔹 Pasar columnas seleccionadas al botón Excel
                config.exportOptions = {
                    columns: selected,
                    format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                                let text = "";
                                if (item.classList !== undefined && item.classList.contains('user-name')) {
                                    text = item.lastChild.firstChild.textContent;
                                } else if (item.innerText === undefined) {
                                    text = item.textContent;
                                } else {
                                    text = item.innerText;
                                }

                                if (/\$/.test(text)) {
                                    text = format_number(text.replace(/\$/g, '').trim());
                                }

                                result += text;
                            });
                            return result;
                        }
                    }
                };
        
                // 🔹 Ejecutar exportación normal de Excel
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
            }
        },
        ("form_filter" in info_page) && info_page.form_filter.length ? {
            text: `<i class="ri-filter-3-line ri-16px me-sm-2"></i> <span class="d-none d-sm-inline-block">Filtro</span>`,
            className: 'btn btn-label-warning waves-effect waves-light mx-2 mt-2',
            action: () => {

                const offCanvasElement = document.querySelector('#canvasFilter');
                let offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                offCanvasEl.show();
            }
        }:null
    ].filter(Boolean);

    return buttons;
}

function indicadoresMovimientos(indicadores){

    console.log(info_page.id);
    let info_indicadores;

    switch (info_page.id) {
        case 2:
            const total = indicadores.reduce((acc, type) => {
                acc += type.states.reduce((acc, state) => acc += state.movements.length, 0)
                return acc;
            }, 0)
            const total_pendientes = indicadores.reduce((acc, type) => {
                acc += type.states.reduce((acc, state) => acc += (state.id == 1 ?  (state.movements.length) : 0), 0)
                return acc;
            }, 0);

            const date_prox = indicadores.reduce((acc, type) => {
                const state_pendiente = type.states.find(s => s.id == 1);
                if(state_pendiente){
                    acc = state_pendiente.movements_pend;
                }
                return acc;

            }, []);

            info_indicadores = `
                <div class="col-sm-12 col-lg-4 mb-1">
                    <div class="card card-border-shadow-green h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-green">
                                    <i class="ri-functions"></i>
                                </span>
                                </div>
                                <h4 class="mb-0">Total Actividades</h4>
                            </div>
                            <div class="row g-6">
                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                    <p class="mb-0 text-center" >
                                        <span class="me-1 fw-medium">Total: </span>
                                        <small class="text-muted">${total}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-4 mb-1">
                    <div class="card card-border-shadow-orange h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-orange">
                                    <i class="ri-information-2-line"></i>
                                </span>
                                </div>
                                <h4 class="mb-0">Total Pendientes</h4>
                            </div>
                            <div class="row g-6">
                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                    <p class="mb-0 text-center" >
                                        <span class="me-1 fw-medium">Total: </span>
                                        <small class="text-muted">${total_pendientes}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-4 mb-1">
                    <div class="card card-border-shadow-pink h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-pink">
                                    <i class="ri-calendar-schedule-line"></i>
                                </span>
                                </div>
                                <h4 class="mb-0">${date_prox.length != 0 ? `Próxima${date_prox.length == 1 ? "" : "s"}` : `Sin actividades pendientes`} a vencer</h4>
                            </div>
                            <div class="row g-6">
                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                    <p class="mb-0 text-center" >
                                        ${
                                            date_prox.length != 0 ? `
                                                ${date_prox.length == 1 ? `
                                                    <span class="me-1 fw-medium">${date_prox[0].title}</span>
                                                    <span class="text-muted">${date_prox[0].date}</span>
                                                ` : `
                                                    <span class="me-1 fw-medium">${date_prox.length} actividades: </span>
                                                    <span class="text-muted">${date_prox[0].date}</span>
                                                `}
                                            ` : ""
                                        }
                                        
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `
            break;
        case 3:

            const total_jornales = indicadores.reduce((acc, type) => {
                acc += type.states.reduce((acc, state) => {
                    const total = state.movements.reduce((acc_j, m) => {
                        const total_jornal = m.details.reduce(
                            (acc_d, d) => acc_d + (d.resource_id == 1 ? parseInt(d.quantity) : 0),
                            0
                        );
                        console.log([total_jornal, acc_j]);
                        return acc_j + total_jornal; // ✅ retornar acumulador
                    }, 0);
            
                    return acc + total; // ✅ retornar acumulador
                }, 0);
            
                return acc;
            }, 0);

            const total_jornales_pendientes = indicadores.reduce((acc, type) => {
                acc += type.states.reduce((acc, state) => {
                    let total = 0;
                    if(state.id == 2){
                        total = state.movements.reduce((acc_j, m) => {
                            const total_jornal = m.details.reduce(
                                (acc_d, d) => acc_d + (d.resource_id == 1 ? parseInt(d.quantity) : 0),
                                0
                            );
                            console.log([total_jornal, acc_j]);
                            return acc_j + total_jornal; // ✅ retornar acumulador
                        }, 0);
                    }
            
                    return acc + total; // ✅ retornar acumulador
                }, 0);
            
                return acc;
            }, 0);

            info_indicadores = `
                <div class="col-sm-12 col-lg-6 mb-1">
                    <div class="card card-border-shadow-green h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-green">
                                    <i class="ri-functions"></i>
                                </span>
                                </div>
                                <h4 class="mb-0">Total Jornales</h4>
                            </div>
                            <div class="row g-6">
                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                    <p class="mb-0 text-center" >
                                        <span class="me-1 fw-medium">Total: </span>
                                        <small class="text-muted">${total_jornales}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6 mb-1">
                    <div class="card card-border-shadow-orange h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-orange">
                                    <i class="ri-information-2-line"></i>
                                </span>
                                </div>
                                <h4 class="mb-0">Jornales Pendientes</h4>
                            </div>
                            <div class="row g-6">
                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                    <p class="mb-0 text-center" >
                                        <span class="me-1 fw-medium">Total: </span>
                                        <small class="text-muted">${total_jornales_pendientes}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `
            
            break;
    
        default:
            info_indicadores = indicadores.reduce((acc, type) => {
                let info = "";
                switch (info_page.id) {
                    case 2:
                        info = type.states.reduce((acc, state) => {
                            let info = `
                                <div class="col-sm-12 col-lg-${(12 / type.states.length)} mb-1">
                                    <div class="card card-border-shadow-${state.color_background.split(" ")[0]} h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="avatar me-4">
                                                <span class="avatar-initial rounded-3 bg-label-${state.color_background.split(" ")[0]}">
                                                    <i class="${state.icon}"></i>
                                                </span>
                                                </div>
                                                <h4 class="mb-0">${state.name}</h4>
                                            </div>
                                            <div class="row g-6">
                                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                                    <p class="mb-0 text-center" >
                                                        <span class="me-1 fw-medium">Total: </span>
                                                        <small class="text-muted">${state.movements.length}</small>
                                                        ${state.movements_pend ? `
                                                            <br>
                                                            <span class="me-1 fw-medium">${state.name}${state.movements_pend.length >= 2 ? `s (${state.movements_pend.length})`: ""}: </span>
                                                            <small class="text-muted">${state.movements_pend.length == 1 ? `
                                                                ${state.movements_pend[0].title} / ${state.movements_pend[0].date}
                                                                `: `${state.movements_pend[0].date}`}</small>
                                                        ` : ``}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            acc.push(info)
                            return acc;
                        }, []).join('');
                        acc.push(info)
                        
                        break;
                    
                    case 3:
                        info = type.states.reduce((acc, state) => {
                            let info = `
                                <div class="col-sm-12 col-lg-${(12 / type.states.length)}">
                                    <div class="card card-border-shadow-${state.color_background.split(" ")[0]} h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="avatar me-4">
                                                <span class="avatar-initial rounded-3 bg-label-${state.color_background.split(" ")[0]}">
                                                    <i class="${state.icon}"></i>
                                                </span>
                                                </div>
                                                <h4 class="mb-0">${state.name}</h4>
                                            </div>
                                            <div class="row g-6">
                                                <div class="col-sm-12 col-lg-12 d-flex justify-content-center align-items-center">
                                                    <p class="mb-0 text-center" >
                                                        <span class="me-1 fw-medium">Total: </span>
                                                        <small class="text-muted">${state.movements.reduce((acc, m) => {
                                                            const total_detail = m.details.reduce((acc, md) => {
                                                                console.log(m)
                                                                const total = md.resource_id == 1 ? parseInt(md.quantity) : 0;
                                                                acc += total;
                                                                return acc;
                                                            }, 0)
                                                            acc += total_detail;
                                                            return acc;
                                                        }, 0)}</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            acc.push(info)
                            return acc;
                        }, []).join('');
                        acc.push(info)
        
                        break;
                
                    default:
                        const total = type.states.reduce((acc, state) => {
                            const total = state.movements.reduce((acc, m) => acc + (m.state_id == 3 ? parseFloat(m.value) : 0), 0);
                            acc += total;
                            return acc;
                        }, 0);
                
                        info = `
                            <div class="col-sm-12 col-lg-${(12 / indicadores.length)}">
                                <div class="card card-border-shadow-${type.id == 1 ? "primary" : (type.id == 2 ? "info" : "warning")} h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <div class="avatar me-4">
                                            <span class="avatar-initial rounded-3 bg-label-${type.id == 1 ? "primary" : (type.id == 2 ? "info" : "warning")}">
                                                <i class="${
                                                    type.id == 1 ? `ri-draft-line` : 
                                                    (type.id == 2 ? `ri-timer-line` : 
                                                        (type.id == 3 ? `ri-currency-line` : "" )
                                                    )
                                                    
                                                }"></i>
                                            </span>
                                            </div>
                                            <h4 class="mb-0">${formatPrice(total)} | ${type.name}</h4>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center">
                                            ${
                                                info_page.id != 3 ? 
                                                    type.states.reduce((acc, state) => {
                                                        let total_state = state.movements.length
                                                        const info_state = `
                                                            <button type="button" class="btn ${state.color_background} ${state.color_font} me-2 waves-effect waves-light">
                                                                ${state.name}s
                                                                <span class="badge bg-white ${state.color_font} ms-1">${total_state}</span>
                                                            </button>
                                                        `;
                                                        acc.push(info_state);
                                                        return acc;
                                                    }, []).join('') :
                                                    `<button type="button" class="btn light-blue lighten-5 text-blue me-2 waves-effect waves-light">
                                                        Jornales
                                                        <span class="badge bg-white text-blue ms-1">${
                                                            type.states.reduce((acc, state) => {
                                                                const total_m = state.movements.reduce((acc, m) => {
                                                                    const total_d = m.details.reduce((acc, md) => acc += (md.resource_id == 1 ? parseInt(md.quantity) : 0), 0)
                                                                    acc += total_d;
                                                                    return acc;
                                                                    }, 0)
                                                                acc += total_m;
                                                                return acc;
                                                            }, 0)
                                                        }</span>
                                                    </button>`
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `
                        acc.push(info)
                        break;
                }
                
                return acc;
            }, []).join('')
            break;
    }


    return info_indicadores;
}

function reloadTable(){
    table_datatable[0].ajax.reload();
    indicadores();
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

async function indicadores(){
    const url = base_url([url_base, 'indicadores']);
    const filters = {};
    const {data, states, type} = await fetchHelper.post(url, filters, {}, 500);
    switch (type) {
        case 'projects':
            let total = 0;
            let total_sales = 0;
            let total_harvest = 0;
            let total_utilities = 0;
            let quantities = 0;
            let quantity_sales = 0;
            let quantity_harvest = 0;
            data.map(p => {
                p.movements.map(m => {
                    if(m.type_movement_id == 1 && m.state_id == 7){
                        total += parseFloat(m.value);
                        quantities += parseFloat(m.details[0].quantity);
                    }
                    if(m.type_movement_id == 2 && m.state_id != 11){
                        total_sales += parseFloat(m.value);
                        quantity_sales += parseFloat(m.details[0].quantity);
                    }
                    if(m.type_movement_id == 4 && m.state_id != 16){
                        total_harvest += parseFloat(m.value);
                        quantity_harvest += parseFloat(m.details[0].quantity);
                    }
                    if(m.type_movement_id == 5 && m.state_id != 19){
                        total_utilities += parseFloat(m.value);
                    }
                })
            });

            var description = `
                <div class="d-flex justify-content-around align-items-center card-subtitle mt-1">
                    <div class="me-2"><b>Total Proyectos:</b> ${formatPrice(total)}</div>
                    <div class="me-2"><b>Total Ventas:</b> ${formatPrice(total_sales)}</div>
                    <div class="me-2"><b>Total Unidades:</b> ${valueFormat(quantities)}</div>
                    <div class="me-2"><b>Unidades Vendidas:</b> ${valueFormat(quantity_sales)}</div>
                </div>
                <hr>
                <div class="d-flex justify-content-around align-items-center card-subtitle mt-1">
                    <div class="me-2"><b>Total Cosechas:</b> ${formatPrice(total_harvest)}</div>
                    <div class="me-2"><b>Total Cosechas Kg:</b> ${valueFormat(quantity_harvest)}</div>
                    <div class="me-2"><b>Total Utilidades:</b> ${formatPrice(total_utilities)}</div>
                </div>
            `;
            $('#description-indicadores').html(description);
            var indicadores = `
                <div class="d-flex align-items-center gap-3">
                <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                    <i class="ri-user-star-line ri-24px"></i>
                    </div>
                </div>
                <div class="card-info">
                    <h5 class="mb-0">8,458</h5>
                    <p class="mb-0">New Customers</p>
                </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                <div class="avatar">
                    <div class="avatar-initial bg-label-warning rounded">
                    <i class="ri-pie-chart-2-line ri-24px"></i>
                    </div>
                </div>
                <div class="card-info">
                    <h5 class="mb-0">$28.5k</h5>
                    <p class="mb-0">Total Profit</p>
                </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                <div class="avatar">
                    <div class="avatar-initial bg-label-info rounded">
                    <i class="ri-arrow-left-right-line ri-24px"></i>
                    </div>
                </div>
                <div class="card-info">
                    <h5 class="mb-0">2,450k</h5>
                    <p class="mb-0">New Transactions</p>
                </div>
                </div>
            `
            
            break;
        case 'Venta':

            const paymentMethods = data.reduce((acc, m) => {
                let exists = acc.some(pm => pm.name === m.payment_method.name);
                if (!exists) {
                    acc.push(m.payment_method);
                }
                return acc;
            }, []);

            var description = `
                <div class="d-flex justify-content-around align-items-center card-subtitle mt-1">
                    ${
                        paymentMethods.reduce((acc, pm) => {
                            const total = data.filter(m => m.payment_method_id == pm.id).length;
                            acc.push(`
                                <div class="me-2"><b>Total a ${pm.name.toLowerCase()}:</b> ${valueFormat(total)}</div>
                            `)
                            return acc;
                        }, []).join('')
                    }
                    ${
                        paymentMethods.reduce((acc, pm) => {
                            if(pm.id == 2){
                                const state = states.find(s => s.id == 9);
                                const name = state.option_state ? state.option_state : state.name;
                                acc.push(`
                                    <div class="me-2"><b>${pm.name} ${name.toLowerCase()}:</b> ${valueFormat(
                                        data.filter(m => (m.payment_method_id == pm.id && m.state_id == state.id)).length
                                    )}</div>
                                `)
                            }
                            return acc;
                        }, []).join('')
                    }
                </div>
            `;
            $('#description-indicadores').html(description);
            var indicadores = `
                ${
                    states.reduce((acc, state) => {
                        const color = state.background.split(' ')[0];
                        const value = data.reduce((acc, movement) => {
                            switch (state.id) {
                                case '10':
                                    acc += movement.state_id == state.id ? parseFloat(movement.total_x_payable) : 0;
                                    acc += movement.state_id == 9 ? parseFloat(movement.value) - parseFloat(movement.total_x_payable) : 0;
                                    break;
                            
                                default:
                                    acc += movement.state_id == state.id ? parseFloat(movement.total_x_payable) : 0;
                                    break;
                            }
                            return acc;
                        }, 0);

                        const indi = `
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-${color} rounded">
                                        <i class="${state.icon} ri-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">${formatPrice(value)}</h5>
                                    <p class="mb-0">${state.name}: ${data.filter(m => m.state_id == state.id).length}</p>
                                </div>
                            </div>
                        `
                        acc.push(indi)
                        return acc;
                    }, []).join('')
                }
            `
            $('#indicadores').html(indicadores);

            break;
        case 'Pago / Abono':
            const types = data.reduce((acc, m) => {
                let type_reference = m.type_movement_resolution_reference;
                if (!acc.includes(type_reference)) {
                    acc.push(type_reference);
                }
                return acc;
            }, []);

            console.log(data);

            var description = `
                <div class="d-flex justify-content-around align-items-center card-subtitle mt-1">
                    ${
                        types.reduce((acc, tm) => {
                            const total = data.filter(m => m.type_movement_resolution_reference == tm).length;
                            acc.push(`
                                <div class="me-2"><b>Total a ${tm.toLowerCase()}:</b> ${valueFormat(total)}</div>
                            `)
                            return acc;
                        }, []).join('')
                    }
                </div>
            `;
            $('#description-indicadores').html(description);

            var indicadores = `
                ${
                    states.reduce((acc, state) => {
                        const color = state.background.split(' ')[0];
                        const value = data.reduce((acc, movement) => {
                            acc += movement.state_id == state.id ? parseFloat(movement.value) : 0;
                            return acc;
                        }, 0);

                        const indi = `
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-${color} rounded">
                                        <i class="${state.icon} ri-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">${formatPrice(value)}</h5>
                                    <p class="mb-0">${state.name}: ${data.filter(m => m.state_id == state.id).length}</p>
                                </div>
                            </div>
                        `
                        acc.push(indi)
                        return acc;
                    }, []).join('')
                }
            `
            $('#indicadores').html(indicadores);

            break;

        case 'Cosecha':
            var indicadores = `
                ${
                    states.reduce((acc, state) => {
                        const color = state.background.split(' ')[0];
                        const value = data.reduce((acc, movement) => {
                            acc += movement.state_id == state.id ? parseFloat(movement.value) : 0;
                            return acc;
                        }, 0);

                        const indi = `
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-${color} rounded">
                                        <i class="${state.icon} ri-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">${formatPrice(value)}</h5>
                                    <p class="mb-0">${state.name}: ${data.filter(m => m.state_id == state.id).length}</p>
                                    <p class="mb-0">Kgs: ${valueFormat(data.reduce((acc, m) => acc += (m.state_id == state.id) ? m.detail.quantity: 0 , 0))}</p>
                                </div>
                            </div>
                        `
                        acc.push(indi)
                        return acc;
                    }, []).join('')
                }
            `
            $('#indicadores').html(indicadores);
            break;
    
        default:
            var indicadores = `
                ${
                    states.reduce((acc, state) => {
                        const color = state.background.split(' ')[0];
                        const value = data.reduce((acc, movement) => {
                            acc += movement.state_id == state.id ? parseFloat(movement.value) : 0;
                            return acc;
                        }, 0);

                        const indi = `
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-${color} rounded">
                                        <i class="${state.icon} ri-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">${formatPrice(value)}</h5>
                                    <p class="mb-0">${state.name}: ${data.filter(m => m.state_id == state.id).length}</p>
                                </div>
                            </div>
                        `
                        acc.push(indi)
                        return acc;
                    }, []).join('')
                }
            `
            $('#indicadores').html(indicadores);
            break;
    }
    console.log([data, states, type])
}