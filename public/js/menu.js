document.addEventListener('turbo:load', function () {
    const menuBar = document.querySelector('.menu-bar-left');
    const toggleIcon = document.getElementById('toggle-menu');
    const toggleArrow = document.getElementById('toggle-arrow');
    const btnPublish = document.querySelector(".publicar");
    const overlay = document.getElementById("blur");
    const windoww = document.getElementById("window");
    const closeWindow = document.getElementById("close-window");

    // Si el usuario toca el icono, escondemos la barra lateral izquierda
    if (toggleIcon) {
        toggleIcon.addEventListener('click', function () {
            if (menuBar) {
                menuBar.style.transform = 'translateX(-260px)';
                menuBar.style.opacity = '0';
            }
            if (toggleArrow) toggleArrow.style.opacity = '1'; // Mostramos la flechita con suavidad
        });
    }

    // Y si toca la flechita la barra vuelve a aparecer
    if (toggleArrow) {
        toggleArrow.addEventListener('click', function () {
            if (menuBar) {
                menuBar.style.transform = 'translateX(0)';
                menuBar.style.opacity = '1';
            }
            if (toggleArrow) toggleArrow.style.opacity = '0'; // Ocultamos la flechita con elegancia
        });
    }



    // --- Modo Día / Noche ---
    const btnLight = document.getElementById('btn-light');
    const btnDark = document.getElementById('btn-dark');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark-mode');
            if (btnDark) btnDark.classList.add('active');
            if (btnLight) btnLight.classList.remove('active');
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark-mode');
            if (btnLight) btnLight.classList.add('active');
            if (btnDark) btnDark.classList.remove('active');
        }
    }

    // Inicializar estado de los botones según el tema actual
    const currentTheme = localStorage.getItem('theme') || 'light';
    applyTheme(currentTheme);

    if (btnLight) {
        btnLight.addEventListener('click', () => {
            localStorage.setItem('theme', 'light');
            applyTheme('light');
        });
    }

    if (btnDark) {
        btnDark.addEventListener('click', () => {
            localStorage.setItem('theme', 'dark');
            applyTheme('dark');
        });
    }
});