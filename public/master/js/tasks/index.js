
let labelColor, headingColor, borderColor, currentTheme;

const states = statesData();


async function loadStates (){
    console.log(tasksData);
    console.log(states);
    const info = {
        labels: [],
        series: [],
        colors: []
    };

    // console.log(colors);

    states.map(state => {
        const task = tasksData.filter(t => state.id == t.task_state_id).length;
        console.log(state.color_background.includes('light') ? state.color_font :state.color_background);
        const color_state = state.color_background.includes('light') ? state.color_font : state.color_background
        const color = colors.find(c => c.color == color_state)?.hex;
        info.labels.push(state.name);
        info.series.push(task);
        info.colors.push(color);
    })

    console.log(info);

      const leadsReportChartEl = document.querySelector('#leadsReportChart'),
    leadsReportChartConfig = {
      chart: {
        height: 157,
        width: 130,
        parentHeightOffset: 0,
        type: 'donut',
        opacity: 1
      },
      labels: info.labels,
      series: info.series,
      colors: info.colors,
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: false,
        formatter: function (val, opt) {
          return parseInt(val);
        }
      },
      legend: {
        show: false
      },
    //   tooltip: {
    //     theme: currentTheme
    //   },
      grid: {
        padding: {
          top: 0
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '75%',
            labels: {
              show: true,
              value: {
                fontSize: '1.125rem',
                fontFamily: 'Inter',
                color: headingColor,
                fontWeight: 500,
                offsetY: -15,
                formatter: function (val) {
                  return parseInt(val);
                }
              },
              name: {
                offsetY: 20,
                fontFamily: 'Inter'
              },
              total: {
                show: true,
                fontSize: '.9375rem',
                label: 'Total',
                color: labelColor,
                formatter: function (w) {
                  return tasksData.length;
                }
              }
            }
          }
        }
      }
    };
  if (typeof leadsReportChartEl !== undefined && leadsReportChartEl !== null) {
    const leadsReportChart = new ApexCharts(leadsReportChartEl, leadsReportChartConfig);
    leadsReportChart.render();
  }
}

function radialBarChart(color, value, show) {
    const radialBarChartOpt = {
      chart: {
        height: show == 'true' ? 58 : 55,
        width: show == 'true' ? 58 : 45,
        type: 'radialBar'
      },
      plotOptions: {
        radialBar: {
          hollow: {
            size: show == 'true' ? '45%' : '25%'
          },
          dataLabels: {
            show: show == 'true' ? true : false,
            value: {
              offsetY: -10,
              fontSize: '15px',
              fontWeight: 500,
              fontFamily: 'Inter',
              color: headingColor
            }
          },
          track: {
            background: config.colors_label.secondary
          }
        }
      },
      stroke: {
        lineCap: 'round'
      },
      colors: [color],
      grid: {
        padding: {
          top: show == 'true' ? -12 : -15,
          bottom: show == 'true' ? -17 : -15,
          left: show == 'true' ? -17 : -5,
          right: -15
        }
      },
      series: [value],
      labels: show == 'true' ? [''] : ['Progress']
    };
    return radialBarChartOpt;
  }

async function loadActivities(){
    const chartProgressList = document.querySelectorAll('.chart-progress');
    if (chartProgressList) {
        chartProgressList.forEach(function (chartProgressEl) {
            const color = '#2196f3', series = chartProgressEl.dataset.series, activity_id = chartProgressEl.dataset.a_id;
            const total_activities = tasksData.filter(t => t.task_activity_id == activity_id).length;
            const activities = tasksData.filter(
                t => t.task_activity_id == activity_id && (t.task_state_id == 4 || t.task_state_id == 5)
            ).length;
            const percentage = total_activities > 0
                ? Math.round((activities / total_activities) * 100)
                : 0;
            $(`#small-activity-${activity_id}`).html(`Total tareas realizadas: ${activities} / ${total_activities}`)
            const progress_variant = chartProgressEl.dataset.progress_variant;
            const optionsBundle = radialBarChart(color, percentage, progress_variant);
            const chart = new ApexCharts(chartProgressEl, optionsBundle);
            chart.render();
        });
     }
}

async function loadChargeUsers(){
    const chartProgressList = document.querySelectorAll('.chart-charge');
    if (chartProgressList) {
        chartProgressList.forEach(function (chartProgressEl) {
            const color = '#1b5e20', series = chartProgressEl.dataset.series, user_id = chartProgressEl.dataset.a_id;

            const total_tasks = tasksData.length;
            const tasks = tasksData.filter(
                t => t.task_user_id == (user_id == "" ? null : user_id) && (t.task_state_id != 5)
            ).length;
            const percentage = total_tasks > 0
                ? Math.round((tasks / total_tasks) * 100)
                : 0;
            $(`#small-user-${user_id}`).html(`Total tareas: ${tasks} / ${total_tasks}`)
            const progress_variant = chartProgressEl.dataset.progress_variant;
            const optionsBundle = radialBarChart(color, percentage, progress_variant);
            const chart = new ApexCharts(chartProgressEl, optionsBundle);
            chart.render();
        });
     }
}

async function loadProgressUsers(){
    const chartProgressList = document.querySelectorAll('.chart-progress-user');
    if (chartProgressList) {
        chartProgressList.forEach(function (chartProgressEl) {
            const color = '#009688', series = chartProgressEl.dataset.series, user_id = chartProgressEl.dataset.a_id;

            const total_tasks = tasksData.filter(t => t.task_user_id == (user_id == "" ? null : user_id)).length;
            const tasks = tasksData.filter(
                t => t.task_user_id == (user_id == "" ? null : user_id) && (t.task_state_id == 4 || t.task_state_id == 5)
            ).length;
            const percentage = total_tasks > 0
                ? Math.round((tasks / total_tasks) * 100)
                : 0;
            $(`#small-user-progress-${user_id}`).html(`Total tareas terminadas: ${tasks} / ${total_tasks}`)
            const progress_variant = chartProgressEl.dataset.progress_variant;
            const optionsBundle = radialBarChart(color, percentage, progress_variant);
            const chart = new ApexCharts(chartProgressEl, optionsBundle);
            chart.render();
        });
     }
}

async function loadData(){
    console.log(tasksData);
    loadStates();
    loadActivities();
    loadChargeUsers();
    loadProgressUsers();
}