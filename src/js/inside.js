"use strict";

let body = document.querySelector('.body');
let chatField = document.querySelector('.messages');

function chatConstructor(nombre, data){
  if (data != "not"){
    let chat = document.createElement('div');
    chat.classList.add('chat');
      
    let image = document.createElement('span');

    let name = document.createElement('h2');
    name.textContent = nombre;
    name.id = nombre;
    let chatInfo = document.createElement('h3');

    if (data == "Accept" || data == "Friend" || data.length == 0){
      chatInfo.classList.add('bold');
      chatInfo.textContent = data == "Accept" ? "Te ha enviado una solicitud de amistad" : data == "Friend" ? "¡Salúdalo!" : "¡Escribe el primer mensaje!";
    } else if (data['not-any']) {
      chatInfo.textContent = "¡Salúdalo!";
    } else chatInfo.textContent = data.emisor != nombre ? 'Tu: ' + data.content : data.content;

    let info = document.createElement('div');
    info.appendChild(name);
    info.appendChild(chatInfo);

    chat.appendChild(image);
    chat.appendChild(info);

    document.querySelector('.search').appendChild(chat);
    
    chat.addEventListener('click',()=>{
      if (data == 'Accept'){
        document.querySelector('.other-profile').children[1].textContent = nombre;
        document.querySelector('.buscar').classList.remove('active');
        document.querySelector('.other-profile').classList.add('active');
        document.querySelector('.solicitud').textContent = "Aceptar solicitud";
        document.querySelector('.solicitud').classList.remove('disabled');
        return
      }
      chats.getCurrentChat(nombre);

      body.classList.add('active');
      document.getElementById(nombre).nextElementSibling.classList.remove('bold');

      document.querySelector('.back-chat').addEventListener('click',()=>{
        body.classList.remove('active')
      });

      document.querySelector('.otherUser').textContent = nombre;
      document.querySelector('.user').textContent = nombre;

      document.querySelector('.send').previousElementSibling.addEventListener('keyup',(e)=>{
        if (e.code == 'Enter' && document.querySelector('.send').previousElementSibling.value != ''){
          sendMessage(e.target, nombre);
        }
      });

      document.querySelector('.user').addEventListener('click',()=>{
        document.querySelector('.type').style.top = "100%";
      });

      document.querySelector('.send').addEventListener('click',(e)=>{
        if (document.querySelector('.send').previousElementSibling.value != ''){
          sendMessage(e.target.parentElement.previousElementSibling, nombre);
        }
      });

      document.querySelector('.reject_friend').addEventListener('click',()=>chats.rejectFriend(nombre));
    })
  } 
}

function sendMessage(input, nombre){
  if (input.value == ""){
    return mensaje("no","No puedes enviar un mensaje vacío");
  }

  if (document.querySelector('.not-any')){
    chatField.removeChild(document.querySelector('.not-any'));
  }

  let actual = Date.now();

  let object =
    {
      "receptor":nombre,
      "content":input.value,
      "date":actual
    };

  chats.sendMessage(object);
  input.value = "";
}

function writeMessages(msj, mode){
  let date = new Date(msj.date);
  let msg = document.createElement('span');
  msg.classList.add('msg');
  msg.classList.add(mode);
  let messageContent = document.createElement('p');
  let messageTime = document.createElement('b');
  messageContent.textContent = msj.content;
  messageTime.textContent = `Hora: ${date.getHours()}:${date.getMinutes() < 10 ? '0' : ''}${date.getMinutes()}`;
  msg.appendChild(messageContent);
  msg.appendChild(messageTime);

  return msg;
}

function getChat(messages, nombre){
  chatField.innerHTML = "";
  if (messages.message){
    let noAny = document.createElement('span');
    noAny.classList.add('floatMsg');
    noAny.classList.add('not-any');
    noAny.textContent = messages.message;
    chatField.appendChild(noAny);
    return;
  }
  let fragment = document.createDocumentFragment();
  messages.map(msg=>{
    let msj = writeMessages(msg, msg.emisor == nombre ? 'receptor' : 'emisor');
    fragment.appendChild(msj);
  });
  chatField.appendChild(fragment);
  chatField.scrollTop = chatField.scrollHeight;
}

fetch('./mvc/controllers/loged_controller.php?userInfo=true',{method:'get'})
  .then(res=>res.json())
  .then(res=>{
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
        .then(res=>res.json())
        .then(res=>{
          console.log(res);
          if (res.mode == "ok"){
            location.reload();
          }
        })
    });
  });

  document.querySelector('.menu-button').addEventListener('click',(e)=>{
    let menu = document.querySelector('.menu-button');
    let menuButton = menu.children[0];
    let backButton = menu.children[1];
    [menuButton.style.display,backButton.style.display] = [backButton.style.display,menuButton.style.display];
    if (!menu.classList.contains('active')){
      document.querySelector('.type').style.top = "100%";
    } else {
      document.querySelector('.type').removeAttribute('style');
    }
    menu.classList.toggle('active');
  });

  document.querySelector('.back-profile').addEventListener('click',()=>{
    document.querySelector('.type').removeAttribute('style');
  });

  document.querySelectorAll('.menu-option').forEach(element=>{
    element.addEventListener("click",(e)=>{
      document.querySelectorAll('.screen').forEach(scrEEn=>{
        let chat = document.querySelectorAll(".screen")[1];
        if (scrEEn.classList.contains(e.target.id)) scrEEn.classList.add('active');  
        else scrEEn.classList.remove('active');

        if (e.target.id != "chats") chat.setAttribute("style","opacity:0");
        else chat.removeAttribute("style");

        document.querySelector('.type').removeAttribute('style');
      });
      let menu = document.querySelector('.menu-button');
      let menuButton = menu.children[0];
      let backButton = menu.children[1];
      [menuButton.style.display,backButton.style.display] = [backButton.style.display,menuButton.style.display];
      menu.classList.remove('active');
    });
  });

  document.querySelector('.solicitud').addEventListener('click',(e)=>{
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({"request":(e.target.textContent == "Enviar solicitud" ? "sendSolicitude" : "acceptSolicitude"),"body":e.target.previousElementSibling.textContent})})  
      .then(res=>res.json())
      .then(res=>{
        if (res.status == "sended"){
          document.querySelector('.solicitud').classList.add('disabled');
          document.querySelector('.other-profile').classList.remove('active');
        } else mensaje(res.status,res.mensaje);
      });
  });

  document.querySelector('.user_search').addEventListener('input',(e)=>{
    fetch(`./mvc/controllers/loged_controller.php?search_user=${e.target.value.trim()}`,{method:"POST"})
      .then(res=>res.json())
      .then(res=>{
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
                document.querySelector('.other-profile').children[1].textContent = res.usuarios[i]['login_user'];
                document.querySelector('.buscar').classList.remove('active');
                document.querySelector('.other-profile').classList.add('active');
                document.querySelector('.solicitud').textContent = "Enviar solicitud";
                document.querySelector('.solicitud').classList.remove('disabled');
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