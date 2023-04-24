window.addEventListener('load',()=>{
  if (history.state != null){
    let forme = new FormData();
    forme.append('recuperar',history.state.email);
    fetch("../controllers/loged_controller.php",{
      method: "POST",
      body: forme
    }).then(res=>res.json()).then(res=>{
      let json = JSON.parse(res);
      let i=0;
      for (let arr in json){
        document.querySelectorAll('.ask')[i].textContent = arr;
        i++;
      }
    });
  } else {
    history.back();
  }

  document.querySelector('.back').addEventListener('click',()=>{
    history.replaceState("","","../../index.php");
    location.reload();
  });

  document.querySelectorAll('.formulario').forEach(formu=>{
    formu.addEventListener('submit',(e)=>{
      e.preventDefault();
      let forme = new FormData(e.target);
      forme.append("consultar-recuperacion",history.state.email);

      let message = e.target.classList.contains('first-form') ? (
        forme.get('response1') == "" || forme.get('response2') == "" || forme.get('response3') == "" ? "Debes rellenar todos los campos" : ""
      ) : (
        forme.get("new-password") == "" ?
          "Debes rellenar los campos de contraseña" :
        forme.get("new-password-confirm") == "" ? 
          "Debes confirmar la contraseña" :
        forme.get('new-password').length < 6 ?
          "La contraseña debe tener como mínimo 6 caracteres" :
        forme.get("new-password") != forme.get("new-password-confirm") ?
          "Las contraseñas no coinciden entre si" : "" 
      );

      if (message) return mensaje("no","Debe rellenar todos los campos");

      fetch(`../controllers/${e.target.classList.contains("first-form") ? "login_controller.php" : "loged_controller.php"}`,{method:"POST",body:forme})
        .then(res=>res.json())
        .then(res=>{
          if (res.mode == "ok-reccovery"){
            document.querySelector('.second-form').style.display = "flex";
            document.querySelector('.first-form').style.display = "none";
          } else if (res.mode == "updated") {
            history.replaceState({mode:"edit",mensaje:res.mensaje},'','../../index.php');
            location.reload();
          } else {
            return mensaje(res.mode,res.mensaje);
          } 
      });
    });  
  });
});