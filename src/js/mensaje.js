const mensaje = (clase,msg)=>{
	let mssg = document.querySelector(".message");
	mssg.innerHTML = "";
	let ionicon = document.createElement("ION-ICON");
	ionicon.removeAttribute("img");
	ionicon.removeAttribute("class");
	ionicon.setAttribute("name",clase == 'edit' ? 'pencil' : clase == 'add' ? 'person-add': clase == 'delete' ? 'trash' : 'ban');
	let span = document.createElement("SPAN");
	span.classList.add(clase);
	span.appendChild(ionicon);
	mssg.appendChild(span);
	let parraf = document.createElement('p');
	parraf.textContent = msg;
	mssg.appendChild(parraf);

	mssg.style.animation = "mensaje1 0.5s forwards";

	setTimeout(()=>mssg.style.animation = "mensaje2 0.5s forwards",2000);
}
