window.addEventListener("load",()=>{

  if (history.state){
		mensaje(history.state.mode,history.state.mensaje);
		history.replaceState(null,'','');
	}

  document.querySelectorAll('.menu-option').forEach(element=>{
    element.addEventListener("click",(e)=>{
      document.querySelectorAll('.screen').forEach(scrEEn=>{
        let chat = document.querySelectorAll(".screen")[1];
        if (scrEEn.classList.contains(e.target.id)) scrEEn.classList.add('active');  
        else scrEEn.classList.remove('active');

        if (e.target.id != "chats") chat.setAttribute("style","opacity:0");
        else chat.removeAttribute("style"); 
      });
    });
  });

  document.querySelector('.solicitud').addEventListener('click',(e)=>{
    console.log(e.target.previousElementSibling.textContent);
  });

  document.querySelector('.user_search').addEventListener('input',(e)=>{
    let forme = new FormData();
    forme.append("busqueda",e.target.value.trim());
    fetch('./mvc/controllers/loged_controller.php',{method:"POST",body:forme})
      .then(res=>res.json())
      .then(res=>{
        document.querySelectorAll('.search')[1].innerHTML = "";
        if (res.usuarios != ""){
          for (let i=0; i < res.usuarios.length;i++){
            let div = [document.createElement('div'),document.createElement('div')];
            div[0].classList.add('users');
            let span = document.createElement('span');
            div[0].appendChild(span);
            let h2 = document.createElement('h2');
            h2.textContent = res.usuarios[i]['login_user'];
            div[1].appendChild(h2);
            div[0].appendChild(div[1]);

            div[0].addEventListener('click',(e)=>{
              document.querySelector('.other-profile').children[1].textContent = res.usuarios[i]['login_user'];
              document.querySelector('.buscar').classList.remove('active');
              document.querySelector('.other-profile').classList.add('active');
            });

            document.querySelectorAll('.search')[1].appendChild(div[0]);
          }
        }
      });
  });

  document.querySelectorAll(".form").forEach(forme=>{
    forme.addEventListener("submit",(e)=>{
      e.preventDefault();
      let forme_one = new FormData(e.target);
      forme_one.append(e.target.classList.contains('password-form') ? "update-password" : e.target.classList.contains('remove-account-form') ? "remove-account" : e.target.classList.contains("ask-form") ? "ask-form" : "" , "");

      let msg = e.target.classList.contains('password-form') ? (
        forme_one.get('old-password') == "" ? "Escribir la contraseña antigua es obligatoria" :
        forme_one.get('new-password') == "" ? "Si quieres una nueva contraseña debes escribirla" :
        forme_one.get('new-password-confirm') == "" ? "Debes confirmar tu contraseña para continuar" :
        forme_one.get('new-password') != forme_one.get('new-password-confirm') ? "Las contraseñas son diferentes" : ""
      ) : e.target.classList.contains('remove-account-form') ? (
        forme_one.get('password') == "" || forme_one.get('confirm-password') == "" ? "Los campos no pueden quedar en blanco" :
        forme_one.get('password') != forme_one.get('confirm-password') ? "Las contraseñas no son iguales" : ""
      ) : e.target.classList.contains('ask-form') ? (
        forme_one.get('ask1') == '0' || forme_one.get('ask2') == '0' || forme_one.get('ask3') == '0' ?
					'Debes elegir 3 preguntas obligatoriamente' :
				forme_one.get('response1') == '' || forme_one.get('response2') == '' || forme_one.get('response3') == '' ? 
					'Debes responder a las preguntas obligatoriamente' : ''
      ) : "" ;

      if (msg) return mensaje("no",msg);

      if (e.target.classList.contains("ask-form")){
				for (let i=1;i<=3;i++){
					forme_one.append(`pregunta${i}`,document.querySelector(`.ask${i}`).value);
					forme_one.append(`respuesta${i}`,forme_one.get(`response${i}`));
				}
			}

      fetch('./mvc/controllers/loged_controller.php',{method:'post',body:forme_one})
        .then(res=>res.json())
        .then(res=>{
          if (res.mode == "updated" || res.mode == "removed"){
            history.replaceState({'mode':(res.mode == "updated" ? 'edit' : "delete"),'mensaje':res.mensaje},'','index.php');
            location.reload();
          } else mensaje(res.mode,res.mensaje);
        });
    });
  });
});