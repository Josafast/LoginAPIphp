"use strict";

/*if (navigator.serviceWorker){
	navigator.serviceWorker.register('sw.js').then(registration=>registration.update());
}*/

window.addEventListener('load',()=>{
	const body = document.querySelector(".body");

	if (history.state){
		mensaje(history.state.mode,history.state.mensaje);
		history.replaceState(null,'','');
	}
	
	document.querySelector(".sign").addEventListener("click",()=>{
		body.classList.add("active");
	});
	
	document.querySelector(".log").addEventListener("click",()=>{
		body.classList.remove("active");
		if (document.querySelector('.pregunta').classList.contains('active')){
			document.querySelector('.pregunta').classList.remove('active');
			document.querySelector('.signer').style.display = 'block';
			document.querySelector('.signer').style.opacity = 1;
		}
	});
	
	document.querySelector('.back-register').addEventListener('click',()=>{
		document.querySelector('.pregunta').classList.remove('active');
		document.querySelector('.signer').style.display = 'block';
		document.querySelector('.signer').style.opacity = 1;
	});

	document.querySelector('.recuperar').addEventListener('click',(e)=>{
		e.preventDefault();
		if (document.getElementById('login-email').value != ''){
			history.pushState({email:document.getElementById('login-email').value},'','mvc/views/recuperar-password.php');
			history.go(0);
		} else mensaje("no","Debe completar el campo del email para recuperar su contraseña");
	})

	document.querySelectorAll('.formulario').forEach(form=>{
		form.addEventListener('submit',async (e)=>{
			e.preventDefault();
			let forme_one = new FormData(e.target.id == 'asks' ? document.querySelector('.signer') : e.target);
			let forme_two = new FormData(document.querySelector('.pregunta'));
			forme_one.append('mode',e.target.id == 'loger' ? 'login' : 'register');
		
			let message = forme_one.get('mode') == 'login' ?
				(!forme_one.get('login-email') ? 
					'El campo del correo está vacío' : 
				!forme_one.get('login-password') ? 
					'El campo de la contraseña está vacío' : '') : 
				(!forme_one.get('sign-user') ?
					'El campo del usuario está vacío' :
				!forme_one.get('sign-email') ?
					'El campo del correo está vacío' :
				!forme_one.get('sign-password') ?
					'El campo de la contraseña está vacío' :
				!forme_one.get('sign-password-confirm') ? 
					'El campo de confirmación debe llenarse obligatoriamente' : 
				forme_one.get('sign-password') != forme_one.get('sign-password-confirm') ? 
					'Las contraseñas no coinciden entre sí' : 
				(document.querySelector('.pregunta').classList.contains('active') ? (
					forme_two.get('ask1') == '0' || forme_two.get('ask2') == '0' || forme_two.get('ask3') == '0' ?
						'Debes elegir 3 preguntas obligatoriamente' :
					forme_two.get('response1') == '' || forme_two.get('response2') == '' || forme_two.get('response3') == '' ? 
						'Debes responder a las preguntas obligatoriamente' : ''
				) : '' ));
				
			if (message) return mensaje('no',message);
			
			if (e.target.id == "signer"){
				document.querySelector('.pregunta').classList.add('active');
				document.querySelector('.signer').style.opacity = 0;
				setTimeout(()=>document.querySelector('.signer').style.display = "none",1000);
				return;
			}

			if (e.target.id == "asks"){
				for (let i=1;i<=3;i++){
					forme_one.append(`pregunta${i}`,document.querySelector(`.ask${i}`).value);
					forme_one.append(`respuesta${i}`,forme_two.get(`response${i}`));
				}
			}

			fetch('./mvc/controllers/login_controller.php',{method:'POST',body: forme_one})
				.then(res=>res.json())
				.then(res=>{
					if (res.mode == "add"){
						history.replaceState({'mode':res.mode,'mensaje':res.mensaje}, '', 'index.php');
						location.reload();
					} else if (res.mode == "ok"){
						location.reload();
					} else mensaje(res.mode,res.mensaje);
				});
		});
	});
});