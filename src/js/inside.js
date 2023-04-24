"use strict";

let body = document.querySelector('.body');
let mainSquare = document.querySelector('.type');
let menuButton = document.querySelector('.menu-button');
let neighborProfile = document.querySelector('.other-profile')
let solicitudeButton = document.querySelector('.solicitud');
let rejectButton = document.querySelector('.reject_friend');
let chatField = document.querySelector('.messages');
let sendMessageButton = document.querySelector('.send');

fetch('./mvc/controllers/loged_controller.php?userInfo=true',{method:'get'})
  .then(res=>res.json()).then(res=>{
    document.querySelector('.emailPHP').textContent = res.login_email;
    document.querySelectorAll(".nombrePHP").forEach(element=>{
      element.textContent = res.login_user;
    });
  });

window.addEventListener("load",async ()=>{
  if (history.state){
		mensaje(history.state.mode,history.state.mensaje);
		history.replaceState(null,'','');
	}

  document.querySelectorAll('.back').forEach(button=>{
    button.addEventListener('submit',e=>{
      e.preventDefault();
  
      fetch('./mvc/controllers/loged_controller.php?back=true',{method:"GET"})
        .then(res=>res.json()).then(res=>{
          if (res.mode == "ok") location.reload();
        })
    });
  });

  menuButton.addEventListener('click',(e)=>{
    let showButton = menuButton.children[0];
    let backButton = menuButton.children[1];
    [showButton.style.display,backButton.style.display] = [backButton.style.display,showButton.style.display];
    if (!menuButton.classList.contains('active')){
      mainSquare.classList.add('active')
    } else {
      mainSquare.classList.remove('active');
    }
    menuButton.classList.toggle('active');
  });

  document.querySelector('.back-profile').addEventListener('click',()=>{
    mainSquare.classList.remove('active');
  });

  document.querySelectorAll('.menu-option').forEach(menuOption=>{
    menuOption.addEventListener("click",e=>{
      let screens = document.querySelectorAll('.screen');
      let chat = document.querySelectorAll(".screen")[1];
      screens.forEach(element=>element.classList.remove('active'));
      screens.forEach(scrEEn=>{
        if (scrEEn.classList.contains(e.target.id)) scrEEn.classList.add('active');  
        mainSquare.classList.remove('active');
      });
      let showButton = menuButton.children[0];
      let backButton = menuButton.children[1];
      [showButton.style.display,backButton.style.display] = [backButton.style.display,showButton.style.display];
      menuButton.classList.remove('active');
    });
  });

  solicitudeButton.addEventListener('click',(e)=>{
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({"request":(e.target.textContent == "Enviar solicitud" ? "sendSolicitude" : "acceptSolicitude"),"body":e.target.previousElementSibling.textContent})});
    solicitudeButton.classList.add('disabled');
    neighborProfile.classList.remove('active');
  });

  document.querySelector('.user_search').addEventListener('input',(e)=>{
    fetch(`./mvc/controllers/chat_controller.php`,
      {method:"POST",
      body: JSON.stringify({
        'request': 'searchUsers',
        'body': e.target.value.trim()
      })})
      .then(res=>res.json()).then(res=>{
        document.querySelectorAll('.search')[1].innerHTML = "";
        if (res.usuarios != ""){
          for (let i=0; i < res.usuarios.length;i++){
            if (document.querySelectorAll('.nombrePHP')[0].textContent != res.usuarios[i]['login_user']){
              let div = [document.createElement('div'),document.createElement('div')];
              div[0].classList.add('users');
              let span = document.createElement('span');
              div[0].appendChild(span);
              let h2 = document.createElement('h2');
              h2.textContent = res.usuarios[i]['login_user'];
              div[1].appendChild(h2);
              div[0].appendChild(div[1]);

              div[0].addEventListener('click',()=>{
                neighborProfile.children[1].textContent = res.usuarios[i]['login_user'];
                document.querySelector('.user_find').classList.remove('active');
                neighborProfile.classList.add('active');
                solicitudeButton.textContent = "Enviar solicitud";
                solicitudeButton.classList.remove('disabled');
              });

              document.querySelectorAll('.search')[1].appendChild(div[0]);
            }
          }
        }
      });
  });

  document.querySelector('.local_search').addEventListener('input',(e)=>{
    let chatfields = document.querySelectorAll('.chat');
    chatfields.forEach(element=>{
      if (element.lastChild.firstChild.textContent.toLowerCase().startsWith(e.target.value)){
        element.style.display = "flex";
      } else element.style.display = "none";
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
        forme_one.get('new-password').length < 6 ? "La contraseña debe tener como mínimo 6 caracteres" :
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