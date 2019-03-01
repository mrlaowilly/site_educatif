const btnClicks = document.querySelectorAll('.btnClick');
btnClicks.forEach(function(btnClick){
	btnClick.addEventListener('click', toggleActive);
});

function toggleActive(){
	this.nextElementSibling.classList.toggle('active');
}

