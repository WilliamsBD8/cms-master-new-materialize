async function onSubmit(event) {
  event.preventDefault();
  
  $('#card-error').hide();
  const url = base_url(['validation']);
  
  const data = {
    email_username: $('#email-username').val(),
    password: $('#password').val(),
    captcha: $('#captcha').val()
  }

  if(data.email_username == '' || data.password == '' || data.captcha == ''){
    $('#card-error h5').html('Campos obligatorios.');
    $('#card-error p').html('Por favor revisa que los campos no esten vacios.');
    return $('#card-error').show();
  }

  $('#btn-send').attr('disabled', true);
  $('#btn-send').html('Enviando... <i class="ri-restart-line ri-spin"></i>');

  await proceso_fetch(url, data).then(respond => {
    if(respond.status == 403){
      $('#card-error h5').html(respond.title);
      $('#card-error p').html(respond.message);
      $('#card-error').show();
      $('#btn-send').attr('disabled', false);
      $('#btn-send').html('Iniciar sesión');
    }else{
      $('#card-success h5').html(respond.title);
      $('#card-success p').html(respond.message);
      $('#card-success').show();
      $('#btn-send').html('Iniciar sesión');
      setTimeout(() => {
        window.location.href = respond.url;
      }, 3000)
    }
  })

}