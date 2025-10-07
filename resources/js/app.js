import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// --- CARRUSEL ---
const carrusel = document.querySelector('.productos-carrusel');
const flechaIzq = document.querySelector('.flecha-izq');
const flechaDer = document.querySelector('.flecha-der');

flechaIzq.addEventListener('click', () => {
    carrusel.scrollBy({ left: -300, behavior: 'smooth' });
});

flechaDer.addEventListener('click', () => {
    carrusel.scrollBy({ left: 300, behavior: 'smooth' });
});

// --- MODO OSCURO ---
const btnDarkMode = document.getElementById("btnDarkMode");

// Verificar si hay tema guardado
if (localStorage.getItem("tema") === "dark") {
    document.body.classList.add("dark");
    btnDarkMode.textContent = "🌑";
}

// Evento de clic para alternar
btnDarkMode.addEventListener("click", () => {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        btnDarkMode.textContent = "🌑";
        localStorage.setItem("tema", "dark");
    } else {
        btnDarkMode.textContent = "☀️";
        localStorage.setItem("tema", "light");
    }
});

