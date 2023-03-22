class Chat{
  writeMessages(msj, mode){
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

  sendMessage(input, nombre){
    if (input.value == ""){
      return mensaje("no","No puedes enviar un mensaje vacío");
    }

    if (this.noAny){
      document.querySelector('.messages').removeChild(this.noAny);
    }

    let actual = Date.now();
    document.querySelector('.messages').appendChild(this.writeMessages({"content":input.value,"date":actual}, 'emisor'));

    let object =
      {
        "receptor":nombre,
        "content":input.value,
        "date":actual
      };
      fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
        "request":"sendMessage",
        "body":object
        })}).then(res=>res.json())
            .then(res=>{
              this.updateChats(res.emisor);
            });
    input.value = "";
  }

  chatConstructor(nombre, data){
    if (data != "not"){
      let chat = document.createElement('div');
      chat.classList.add('chat');
      
      let image = document.createElement('span');

      let name = document.createElement('h2');
      name.textContent = nombre;
      let chatInfo = document.createElement('h3');

      if (data == "Accept" || data == "Friend" || data.length == 0){
        chatInfo.classList.add('bold');
        chatInfo.textContent = data == "Accept" ? "Te ha enviado una solicitud de amistad" : data == "Friend" ? "¡Salúdalo!" : "¡Escribe el primer mensaje!";
      } else chatInfo.textContent = data.emisor != nombre ? 'Tu: ' + data.content : data.content;

      let info = document.createElement('div');
      info.appendChild(name);
      info.appendChild(chatInfo);

      chat.appendChild(image);
      chat.appendChild(info);

      document.querySelector('.search').appendChild(chat);
    
      chat.addEventListener('click',(e)=>{
        if (data == 'Accept'){
          document.querySelector('.other-profile').children[1].textContent = nombre;
          document.querySelector('.buscar').classList.remove('active');
          document.querySelector('.other-profile').classList.add('active');
          document.querySelector('.solicitud').textContent = "Aceptar solicitud";
          document.querySelector('.solicitud').classList.remove('disabled');
          return
        }
        this.getChat(nombre);

        document.querySelector('.body').classList.add('active');

        document.querySelector('.back-chat').addEventListener('click',()=>{
          document.querySelector('.body').classList.remove('active')
          clearInterval(this.waitingMessage);
        });

        document.querySelector('.otherUser').textContent = nombre;
        document.querySelector('.user').textContent = nombre;

        document.querySelector('.send').previousElementSibling.addEventListener('keyup',(e)=>{
          if (e.code == 'Enter' && document.querySelector('.send').previousElementSibling.value != ''){
            this.sendMessage(e.target, nombre);
          }
        });
        document.querySelector('.send').addEventListener('click',(e)=>{
          if (document.querySelector('.send').previousElementSibling.value != ''){
            this.sendMessage(e.target.parentElement.previousElementSibling, nombre);
          }
        });

        document.querySelector('.reject_friend').addEventListener('click',()=>{
          fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
            "request":"rejectFriend",
            "body":nombre
          })}).then(res=>res.json())
              .then(res=>{
                console.log(res);
                if (res.status){
                  clearInterval(this.waitingMessage);
                  this.updateChats();
                  document.querySelector('.body').classList.remove('active');
                }
              });
        });

        document.querySelector('.search').appendChild(chat);

        this.waitingMessage = setInterval(()=>{
          fetch('./mvc/controllers/chat_controller.php',{method: 'PHP', body:JSON.stringify({
            "request":"getLastMessage",
            "body":nombre
          })}).then(res=>res.json())
              .then(res=>{
                if (res.length > 0){
                    this.lastMessage = res;
                    document.querySelector('.messages').appendChild(this.writeMessages(res, res.emisor == nombre ? 'receptor' : 'emisor'));
                  }
                  document.querySelector('.messages').scrollTop = document.querySelector('.messages').scrollHeight;
              });
        },500);
      })
    } 
  }
  
  getChat(nombre){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getCurrentChat",
      "body":nombre
    })}).then(res=>res.json())
        .then(res=>{
          console.log(res);
          document.querySelector('.messages').innerHTML = "";
          if (res.message){
            this.noAny = document.createElement('span');
            this.noAny.classList.add('floatMsg');
            this.noAny.textContent = res.message;
            document.querySelector('.messages').appendChild(this.noAny);
            return;
          }
          let fragment = document.createDocumentFragment();
          this.lastMessage = res[res.length-1];
          res.map(msg=>{
            let msj = this.writeMessages(msg, msg.emisor == nombre ? 'receptor' : 'emisor');
            fragment.appendChild(msj);
          });
          document.querySelector('.messages').appendChild(fragment);
          document.querySelector('.messages').scrollTop = document.querySelector('.messages').scrollHeight;
        });
  }

  updateChats(nombre){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getChats"
    })}).then(res=>res.json())
        .then(res=>{
          document.querySelector('.search').innerHTML = "";
          if (res.mensaje){
            return console.log(res.mensaje);
          }
          res.map(user=>{
            console.log(user);
            this.chatConstructor(user.name,user.info == "" || user.info == null ? "Friend" : user.info);
          })
        });
  }  

    constructor(){
      this.updateChats();
    }
  }

let ChaT;

fetch('./mvc/controllers/loged_controller.php?userInfo=true',{method:'get'})
  .then(res=>res.json())
  .then(res=>{
    document.querySelector('.emailPHP').textContent = res.login_email;
    document.querySelectorAll(".nombrePHP").forEach(element=>{
      element.textContent = res.login_user;
    });
    ChaT = new Chat();
  });

window.addEventListener("load",()=>{
  if (history.state){
		mensaje(history.state.mode,history.state.mensaje);
		history.replaceState(null,'','');
	}

  document.querySelector('.back').addEventListener('submit',e=>{
    e.preventDefault();

    fetch('./mvc/controllers/loged_controller.php?back=true',{method:"GET"})
      .then(res=>res.json())
      .then(res=>{
        if (res.mode == "ok"){
          location.reload();
        }
      })
  });

  document.querySelectorAll('.menu-option').forEach(element=>{
    element.addEventListener("click",(e)=>{
      document.querySelectorAll('.screen').forEach(scrEEn=>{
        let chat = document.querySelectorAll(".screen")[1];
        if (scrEEn.classList.contains(e.target.id)) scrEEn.classList.add('active');  
        else scrEEn.classList.remove('active');

        if (e.target.id == "chats") ChaT.updateChats(document.querySelectorAll(".nombrePHP")[0].textContent);

        if (e.target.id != "chats") chat.setAttribute("style","opacity:0");
        else chat.removeAttribute("style"); 
      });
    });
  });

  document.querySelector('.solicitud').addEventListener('click',(e)=>{
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({"request":(e.target.textContent == "Enviar solicitud" ? "sendSolicitude" : "acceptSolicitude"),"body":e.target.previousElementSibling.textContent})})  
      .then(res=>res.json())
      .then(res=>{
        if (res.status == "sended"){
          ChaT.updateChats(document.querySelectorAll(".nombrePHP")[0].textContent)
          document.querySelector('.solicitud').classList.add('disabled');
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
              document.querySelector('.solicitud').textContent = "Enviar solicitud";
              document.querySelector('.solicitud').classList.remove('disabled');
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