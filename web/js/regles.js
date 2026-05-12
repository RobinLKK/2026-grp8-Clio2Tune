const openRules = document.getElementById("openRules");
const overlay = document.getElementById("overlay");
const closeRules = document.getElementById("closeRules");

openRules.addEventListener("click", (e) => {
	e.preventDefault();
	overlay.classList.add("active");
	document.body.style.overflow = "hidden";
});

closeRules.addEventListener("click", (e) => {
	e.preventDefault();
	overlay.classList.remove("active");
	document.body.style.overflow = "auto";
});

overlay.addEventListener("click", (e) => {
	if (e.target === overlay) {
		overlay.classList.remove("active");
		document.body.style.overflow = "auto";
	}
});