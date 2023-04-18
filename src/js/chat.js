"use strict";

class Chat {
  constructor(){
    this.limit = "";
    this.chat = [];
    this.userState = {};
    this.getChats();
    this.chatChanges();
    this.usersState();
  }

  getChats(){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getChats"
    })}).then(res=>res.json())
        .then(res=>{
          if (!res.mensaje){
            console.log(res);
            res.map(async user=>{
              if (user['time_limit']){
                this.limit = setTimeout(()=>{
                  fetch("./src/html/closed.html",{method:'GET'})
                    .then(res=>res.text())
                    .then(res=>document.body.innerHTML += res);
                  loadCss("./src/styles/disabled.css");
                },user['time_limit'] + "000");
                return;
              }
              await this.chat.push({
                name : user.name,
                info : (user.info == "" || user.info == null ? "Friend" : user.info) 
              });
            });
            this.chat.map(user=>{
              chatConstructor(user.name, user.info);
            });
          }
        }).catch(err=>{
          console.log(err);
        });
  }

  usersState(){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getUserState"
    })}).then(res=>res.json())
        .then(async res=>{
          if ((JSON.stringify(this.userState) !== JSON.stringify(res)) && (JSON.stringify(this.userState) != "{}")){
            document.querySelector('.search').innerHTML = "";
            this.chat = [];
            this.getChats();
          }
          this.userState = res;
        });
  }

  getCurrentChat(nombre){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getCurrentChat",
      "body":nombre
    })}).then(res=>res.json())
        .then(res=>{
          getChat(res,nombre);
        });
  }

  sendMessage(object){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"sendMessage",
      "body":object
    })});
  }

  chatChanges(){
    fetch('./mvc/controllers/listener.php')
      .then(res=>res.json())
      .then(async res=>{
        console.log(res);
        if (res.status == "changed"){
          this.chat.map(user=>{
            this.lastMessage(user.name);
          });
        }
        this.usersState();
        this.chatChanges();
      });
  }

  lastMessage(usuario){
    let encounterIndex = this.chat.filter(arr=> arr.name == usuario);
    let user = this.chat.indexOf(encounterIndex[0]);
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"getLastMessage",
    "body":usuario
    })}).then(res=>res.json())
        .then(async res=>{
            if (this.chat[user].info.date != res.date){
              this.chat[user].info = res;
              if (document.querySelector('.user').textContent == usuario) {
                document.getElementById(usuario).nextElementSibling.textContent = (res.emisor != usuario ? "Tú: " : "") + res.content;
                chatField.appendChild(writeMessages(res, res.emisor == usuario ? 'receptor' : 'emisor'));
                chatField.scrollTop = chatField.scrollHeight;
              }
            }
        }).catch();
  }

  rejectFriend(nombre){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"rejectFriend",
      "body":nombre
    })}).then(res=>{
      body.classList.remove('active');
    })
  }
}

let chats = new Chat();