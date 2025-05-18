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

function alert(message, type = 'red', duration = 300) {
  M.toast({ html: `'<span class="${type}-text">${message}</span>'`, classes: `rounded ${type} lighten-5`, outDuration: duration });
}

function base_url(array = []) {
  var url = localStorage.getItem('url');
  if (array.length == 0) return `${url}`;
  else return `${url}${array.join('/')}`;
}