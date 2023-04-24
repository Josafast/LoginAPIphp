"use strict";

class ChatMethods {
  constructor(){

  }

  chatConstructor(name, chatData){
    if (chatData != "not"){
      let chat = document.createElement('div');
      chat.classList.add('chat');
        
      let image = document.createElement('span');
  
      let nameElement = document.createElement('h2');
      nameElement.textContent = name;
      nameElement.id = name;
      let chatInfo = document.createElement('h3');
  
      if (chatData == "Accept" || chatData == "Friend" || chatData.length == 0){
        chatInfo.classList.add('bold');
        chatInfo.textContent = chatData == "Accept" ? "Te ha enviado una solicitud de amistad" : chatData == "Friend" ? "¡Salúdalo!" : "¡Escribe el primer mensaje!";
      } else if (chatData['not-any']) {
        chatInfo.classList.add('bold');
        chatInfo.textContent = "¡Salúdalo!";
      } else chatInfo.textContent = chatData.emisor != name ? `Tú: ${chatData.content}` : chatData.content;
  
      let info = document.createElement('div');
      info.appendChild(nameElement);
      info.appendChild(chatInfo);
  
      chat.appendChild(image);
      chat.appendChild(info);
  
      document.querySelector('.search').appendChild(chat);
      
      chat.addEventListener('click',()=>{
        if (chatData == 'Accept'){
          neighborProfile.children[1].textContent = name;
          document.querySelector('.user_find').classList.remove('active');
          neighborProfile.classList.add('active');
          solicitudeButton.textContent = "Aceptar solicitud";
          solicitudeButton.classList.remove('disabled');
          return
        }
        chatFetch.getCurrentChat(name);
  
        body.classList.add('active');
        document.getElementById(name).nextElementSibling.classList.remove('bold');
  
        document.querySelector('.back-chat').addEventListener('click',()=>{
          body.classList.remove('active')
        });
  
        document.querySelector('.otherUser').textContent = name;
        document.querySelector('.user').textContent = name;
  
        sendMessageButton.previousElementSibling.addEventListener('keyup',(e)=>{
          if (e.code == 'Enter' && sendMessageButton.previousElementSibling.value != ''){
            this.sendMessage(e.target, name);
          }
        });
  
        document.querySelector('.user').addEventListener('click',()=>{
          mainSquare.classList.add('active');
        });
  
        sendMessageButton.addEventListener('click',(e)=>{
          if (sendMessageButton.previousElementSibling.value != ''){
            this.sendMessage(e.target.parentElement.previousElementSibling, name);
          }
        });
  
        rejectButton.addEventListener('click',()=>chatFetch.rejectFriend(name));
      })
    }
  }

  sendMessage(input, name){
    if (document.querySelector('.not-any')){
      chatField.removeChild(document.querySelector('.not-any'));
    }
  
    let actual = Date.now();
  
    let object =
      {
        "receptor":name,
        "content":input.value,
        "date":actual
      };
  
    chatFetch.sendMessage(object);
    input.value = "";
  }

  getChat(messages, name){
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
      let msj = this.writeMessages(msg, msg.emisor == name ? 'receptor' : 'emisor');
      fragment.appendChild(msj);
    });
    chatField.appendChild(fragment);
    chatField.scrollTop = chatField.scrollHeight;
  }

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
}

let chatMethods = new ChatMethods();