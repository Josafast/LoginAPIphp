"use strict";

class ChatFetch {
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
            res['users'].map(async user=>{
              await this.chat.push({
                name : user.name,
                info : (user.info == "" || user.info == null ? "Friend" : user.info) 
              });
            });
            this.limit = setTimeout(()=>{
              fetch("./src/html/closed.html",{method:'GET'})
                .then(res=>res.text())
                .then(res=>document.body.innerHTML += res);
              loadCss("./src/styles/disabled.css");
            },res['time_limit'] + "000");
            this.chat.map(user=>{
              chatMethods.chatConstructor(user.name, user.info);
            });
          } else mensaje("no", res.mensaje);
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

  getCurrentChat(name){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getCurrentChat",
      "body":name
    })}).then(res=>res.json())
        .then(res=>{
          chatMethods.getChat(res,name);
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
        if (res.status == "changed"){
          this.chat.map(user=>{
            this.lastMessage(user.name);
          });
        }
        this.usersState();
        this.chatChanges();
      });
  }

  lastMessage(user){
    let findIndex = this.chat.filter(arr=> arr.name == user);
    let userIndex = this.chat.indexOf(findIndex[0]);
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"getLastMessage",
    "body":user
    })}).then(res=>res.json())
        .then(async res=>{
            if (!this.chat[userIndex] || this.chat[userIndex].info.date != res.date){
              this.chat[userIndex].info = res;
              if (document.querySelector('.user').textContent == user) {
                document.getElementById(user).nextElementSibling.textContent = (res.emisor != user ? "Tú: " : "") + res.content;
                chatField.appendChild(chatMethods.writeMessages(res, res.emisor == user ? 'receptor' : 'emisor'));
                chatField.scrollTop = chatField.scrollHeight;
              }
            }
        });
  }

  rejectFriend(name){
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"rejectFriend",
      "body":name
    })});
    body.classList.remove('active');
  }
}

let chatFetch = new ChatFetch();