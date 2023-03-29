let chat = [];
let userState;
let waitingMessage = [];
let notis;
let viewWindow;

function getChats(){
  fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"getChats"
  })}).then(res=>res.json())
      .then(res=>{
        if (!res.mensaje){
          res.map(user=>{
            chat.push({
              name : user.name,
              info : (user.info == "" || user.info == null ? "Friend" : user.info) 
            });
          });
        }
      });
}

function usersState(e){
  return setInterval(()=>{
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
      "request":"getUserState"
    })}).then(res=>res.json())
        .then(res=>{
          if (userState){
            if (JSON.stringify(userState) != JSON.stringify(res)){
              e.source.postMessage("reboot");
            }
          }
        });
  },500);
}

function lastMessage(usuario,e){
  let encounterIndex = chat.filter(arr=> arr.name == usuario);
  let user = chat.indexOf(encounterIndex[0]);
  waitingMessage.push(setInterval(()=>{
    fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"getLastMessage",
    "body":usuario
  })}).then(res=>res.json())
      .then(async res=>{
          console.log(res);
          if (res.error){
            e.source.postMessage("sessionClosed");
          }
          if (chat[user].info.date != res.date){
            chat[user].info = res;
            e.source.postMessage([res,'newMessage',chat[user].name]);
            if (notis == 'allow' || viewWindow){
              if (chat[user].info.emisor == usuario){
                self.registration.showNotification(chat[user].info.emisor,{
                  body: chat[user].info.content, 
                  tag: "Ha llegado un nuevo mensaje", 
                  silent: true});
              }
            }
          }
      });
  },500));
}

function getChat(nombre,e){
  fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"getCurrentChat",
    "body":nombre
  })}).then(res=>res.json())
      .then(res=>{
        e.source.postMessage([res,'chatRecieved',nombre]);
      });
}

function sendMessage(object){
  fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"sendMessage",
    "body":object
  })});
}

function rejectFriend(nombre,e){
  fetch('./mvc/controllers/chat_controller.php',{method:'POST',body:JSON.stringify({
    "request":"rejectFriend",
    "body":nombre
  })}).then(res=>res.json())
      .then(res=>{
        if (res.status){
          getChats();
          e.source.postMessage([nombre,'rejected']);
        }
      });
}

self.addEventListener('install',()=>{
  console.log("Se ha instalado el service worker");
  self.skipWaiting();
});

self.addEventListener('activate',(e)=>{
  getChats();
});

self.addEventListener('message',async (e)=>{
  if (e.data[0] == 'start'){
    notis = e.data[1];
    e.source.postMessage([chat,"started"]);
  }

  if (e.data[0] == 'viewing'){
    viewWindow = e.data[1];
  }

  if (e.data[0] == 'list'){
    chat.map(async user=>{
      await lastMessage(user.name,e);
    });
    usersState(e);
  }

  if (e.data[0] == 'getCurrentChat'){
    getChat(e.data[1],e);
  }

  if (e.data[0] == 'send'){
    sendMessage(e.data[1]);
  }

  if (e.data[0] == 'reject_friend'){
    rejectFriend(e.data[1],e);
  }
})