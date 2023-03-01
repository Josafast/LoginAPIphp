"use strict";

window.addEventListener("load",()=>{
  const visible = document.querySelectorAll(".visible");
  const not_visible = document.querySelectorAll(".not-visible");
  const password = document.querySelectorAll(".password");

  const visibles = [];
  const not_visibles = [];
  const passwords = [];
  visible.forEach(visible=>visibles.push(visible));
  not_visible.forEach(not_visible=>not_visibles.push(not_visible));
  password.forEach(password=>passwords.push(password));

  for(let i=0;i<visibles.length;i++){
    visibles[i].addEventListener("click",()=>{
      visibles[i].style.display = "none";
      not_visibles[i].style.display = "inline";
      passwords[i].type = "text";
    });
  
    not_visibles[i].addEventListener("click",()=>{
      not_visibles[i].style.display = "none";
      visibles[i].style.display = "inline";
      passwords[i].type = "password";
    });
  }

  document.querySelector('.year').textContent = new Date().getFullYear();

  document.querySelector('.close').addEventListener("click",()=>{
    document.querySelector('footer').classList.remove('active');
  });

  document.querySelector('.help').addEventListener("click",()=>{
    document.querySelector('footer').classList.add('active');
  });

  document.querySelectorAll('.preguntas').forEach(element=>{
    element.addEventListener('change',e=>{
      let array=[];
      document.querySelectorAll('.preguntas').forEach(elementio=>{
        array.push(elementio.value);
      });

      if (e.target.value != 0){
        e.target.children[0].setAttribute("disabled","true");
      }

      document.querySelectorAll('.preguntas_value').forEach(elemento=>{
        if (array.includes(elemento.value)){
          elemento.setAttribute('disabled',"true");
        } else elemento.removeAttribute("disabled");
      });
    });
  });
})