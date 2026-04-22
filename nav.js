//nav function 

function toggleMenu() {
    document.querySelector('.nav-burger .nav-links').classList.toggle('active');
}

document.addEventListener("click", function () {
    document.querySelector('.nav-burger .nav-links').classList.remove('active');
});
function login() {
    window.location.href = "registerform.php";
}