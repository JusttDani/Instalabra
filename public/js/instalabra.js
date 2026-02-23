// ================== FUNCIONES PRINCIPALES ==================

// Script de funcionalidades base para la interfaz de usuario

document.addEventListener("turbo:load", () => {
  console.log("Instalabra JS loaded (Cleaned).");

  // Mostrar u ocultar la contraseña al pulsar el ojito
  document.querySelectorAll(".toggle-password").forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.parentElement.querySelector("input");
      const eyeOpen = this.querySelector(".eye-open");
      const eyeClosed = this.querySelector(".eye-closed");

      if (input.type === "password") {
        input.type = "text";
        eyeOpen.style.display = "none";
        eyeClosed.style.display = "block";
      } else {
        input.type = "password";
        eyeOpen.style.display = "block";
        eyeClosed.style.display = "none";
      }
    });
  });
});

// Ajustamos visualmente la barra de votos según la cantidad de likes
document.addEventListener("turbo:load", function () {
  const bars = document.querySelectorAll(".vote-bar");

  bars.forEach((bar) => {
    const likes = parseInt(bar.dataset.likes);
    const maxLikes = parseInt(bar.dataset.max) || 1;
    const fill = bar.querySelector(".fill");

    const porcentaje = likes > 0 ? (likes / maxLikes) * 100 : 0;

    setTimeout(function () {
      fill.style.width = porcentaje + "%";
    }, 100);
  });
});

// Actualizamos la lista de tendencias y rankings
function updateRankings() {
  console.log("Updating rankings...");
  fetch("/api/trending", {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
  })
    .then((res) => res.json())
    .then((data) => {
      const renderList = (id, items) => {
        const list = document.getElementById(id);
        if (!list) return;
        list.innerHTML = "";
        if (items.length === 0) {
          list.innerHTML = "<p>No hay tendencias.</p>";
          return;
        }

        items.forEach((item) => {
          const li = document.createElement("li");

          const percentage = item.likes > 0 ? (item.likes / item.max) * 100 : 0;

          li.innerHTML = `
                        <span class="word-name">
                            <a href="/palabra/${item.id}">${item.palabra}</a>
                        </span>
                        <div class="vote-bar" data-likes="${item.likes}" data-max="${item.max}">
                            <div class="fill" style="width: ${percentage}%"></div>
                        </div>
                        <span class="vote-number">${item.likes}</span>
                    `;
          list.appendChild(li);
        });
      };

      if (data.daily) renderList("ranking-daily", data.daily);
      if (data.monthly) renderList("ranking-monthly", data.monthly);
    })
    .catch((err) => console.error("Error updating rankings:", err));
}

document.addEventListener("turbo:load", () => {
  // ================== GESTIÓN DE LIKES ==================
  const likeForms = document.querySelectorAll(".ajax-like-form");
  likeForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const url = this.action;
      const btn = this.querySelector("button");
      const img = btn.querySelector("img");
      const countSpan = this.parentElement.querySelector(".count");

      // Damos un pequeño efecto visual al instante para que se sienta rápido
      if (img) img.style.transform = "scale(0.8)";

      fetch(url, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.liked !== undefined) {
            // Actualizamos el número de likes en pantalla
            if (countSpan) countSpan.textContent = data.count;

            // Efecto de rebote chulo en el corazón
            if (img) {
              img.style.transform = "scale(1.2)";
              setTimeout(() => (img.style.transform = "scale(1)"), 200);
              // Aplicamos los estilos de "me gusta"
              if (data.liked) {
                btn.classList.add("liked"); // Can add CSS for this later
              } else {
                btn.classList.remove("liked");
              }
            } else {
              // Texto para el botón si estamos en el perfil
              btn.innerHTML = data.liked ? "Ya te gusta" : "Like";
            }

            // Refrescamos los rankings top para reflejar el nuevo like
            updateRankings();
          }
        })
        .catch((err) => {
          console.error("Error fetching like:", err);
        });
    });
  });

});

// ================== GESTIÓN DE SEGUIDORES (GLOBAL) ==================
// Se define fuera de turbo:load porque usamos delegación de eventos en el document.
// Si estuviera dentro, Turbo añadiría un listener extra cada vez que cambiamos de página,
// provocando que el botón se pulse múltiples veces.
document.addEventListener("click", function (e) {
  const link = e.target.closest(".ajax-follow-link");
  if (!link) return;

  e.preventDefault();
  e.stopPropagation(); // Evitar que Turbo intercepte el click globalmente y dispare una visita a la URL
  
  // Prevenir doble clic rápido (debounce rudimentario)
  if (link.dataset.isFetching === "true") return;
  link.dataset.isFetching = "true";

  const url = link.getAttribute("href");
  const btn = link.querySelector("button");
  if (btn) {
    btn.style.opacity = "0.7";
    btn.textContent = "...";
  }

  fetch(url, {
    method: "POST", // IMPRESCINDIBLE para que el navegador no cacheé la respuesta
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.following !== undefined) {
        if (btn) {
          btn.style.opacity = "1";
          if (data.following) {
            btn.textContent = "Dejar de seguir";
          } else {
            btn.textContent = "Seguir";
          }
        }

        // Actualizamos la cifra de seguidores en el perfil si estamos viéndolo
        // Importante: Solo si el ID del usuario seguido coincide con el del perfil
        const profileUserId = document.querySelector('main.feed')?.dataset?.userId;
        const followedUserId = url.split('/').filter(p => !isNaN(p) && p !== "").pop();

        const followersDisplay = document.querySelector(".js-followers-count");
        if (data.followersCount !== undefined && followersDisplay && (!profileUserId || profileUserId === followedUserId)) {
          followersDisplay.textContent = data.followersCount;
        }

        // Refrescar caja de sugerencias de la derecha sin recargar la página
        fetch('/api/sidebar/right', { headers: { "X-Requested-With": "XMLHttpRequest" }})
          .then(res => res.text())
          .then(html => {
              const temp = document.createElement('div');
              temp.innerHTML = html;
              const newSuggestions = temp.querySelector('#user-suggestions');
              const oldSuggestions = document.getElementById('user-suggestions');
              if (newSuggestions && oldSuggestions) {
                  oldSuggestions.innerHTML = newSuggestions.innerHTML;
              }
          })
          .catch(err => console.error("Error refrescando sugerencias:", err));
      }
    })
    .catch((err) => {
      console.error("Error follow:", err);
      if (btn) btn.style.opacity = "1";
    })
    .finally(() => {
	    link.dataset.isFetching = "false";
	});
});

// Ventanas modales para hojear los seguidores y a quién sigue alguien
function openFollowers() {
  document.getElementById("followers-modal").style.display = "flex";
}

function openFollowing() {
  document.getElementById("following-modal").style.display = "flex";
}

function closeModal() {
  document.getElementById("followers-modal").style.display = "none";
  document.getElementById("following-modal").style.display = "none";
}

// ================== MOTOR DE BÚSQUEDA ==================
document.addEventListener("turbo:load", () => {
  const searchInput = document.querySelector('.search-form input[name="q"]');
  const searchFilters = document.getElementById("search-filters");
  const resultsContainer = document.getElementById("search-results-container");

  // Prevenimos errores de consola asegurándonos de que hay un buscador en pantalla
  if (!searchInput) return;

  let searchTimeout = null;
  let currentQuery = searchInput.value || "";

  // Dejamos el cursor listo al final de la palabra si venimos de otra página
  if (window.location.pathname.includes("/buscar")) {
    searchInput.focus();
    if (currentQuery !== "") {
      searchInput.value = "";
      searchInput.value = currentQuery;
    }
  }

  // Averiguamos qué pestaña de filtro está presionada, o usamos 'usuarios' por defecto
  let currentFilter = "usuarios";
  if (searchFilters) {
    const activeBtn = searchFilters.querySelector(".active");
    if (activeBtn) currentFilter = activeBtn.dataset.filter;
  }

  function performSearch(query, filter) {
    // Nos ahorramos hacer búsquedas en vano si no estamos en la vista de búsqueda
    if (!window.location.pathname.includes("/buscar")) {
      return;
    }

    fetch(`/buscar?q=${encodeURIComponent(query)}&filter=${filter}`, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
      },
    })
      .then((res) => res.json())
      .then((data) => {
        if (!resultsContainer) {
          return;
        }

        let html = `<h3>${filter.charAt(0).toUpperCase() + filter.slice(1)}</h3>`;
        html +=
          filter === "definiciones"
            ? '<ul class="results-list">'
            : '<ul class="results-list">';

        if (data.results.length === 0) {
          if (query.trim() !== "") {
            html += `<li>No se encontraron ${filter}</li>`;
          }
          html += "</ul>";
        } else {
          data.results.forEach((item) => {
            if (filter === "usuarios") {
              html += `<li class="suggestion-item">
                        <img class="suggestion-avatar" src="${item.fotoPerfil}" alt="${item.nombre}">
                        <div class="user-info-suggestion">
                           <span class="suggestion-name">
                             <a style="text-decoration:none; color:inherit;" href="/usuario/${item.id}">${item.nombre}</a>
                           </span>
                        </div>`;
              if (!item.isMe) {
                html += `<a href="/usuario/${item.id}/follow" class="ajax-follow-link">
                           <button class="suggestion-follow-btn">
                             ${item.isFollowing ? 'Dejar de seguir' : 'Seguir'}
                           </button>
                         </a>`;
              }
              html += `</li>`;
            } else if (filter === "palabras") {
              html += `<li><a style="text-decoration:none; color:inherit;" href="/palabra/${item.id}">${item.palabra}</a></li>`;
            } else if (filter === "definiciones") {
              html += `<li>${item.definicion} <br><small>(${item.palabra})</small></li>`;
            }
          });
          html += "</ul>";
        }

        resultsContainer.innerHTML = html;

        // Actualizamos la URL limpiamente para poder compartir enlaces sin recargar
        const newUrl = `/buscar?q=${encodeURIComponent(query)}&filter=${filter}`;
        window.history.replaceState({}, "", newUrl);
      })
      .catch((err) => console.error("Error en búsqueda AJAX:", err));
  }

  // Te llevamos directamente a la página de búsqueda al pinchar en la barra, muy rollo app
  searchInput.addEventListener('focus', () => {
    if (!window.location.pathname.includes('/buscar')) {
      const url = `/buscar?q=${encodeURIComponent(searchInput.value)}&filter=${currentFilter}`;
      if (window.Turbo) {
        window.Turbo.visit(url);
      } else {
        // Por si Turbo se pone rebelde
        const tempLink = document.createElement('a');
        tempLink.href = url;
        document.body.appendChild(tempLink);
        tempLink.click();
        tempLink.remove();
      }
    }
  });

  // Lanzamos la búsqueda mientras el usuario teclea (evitando saturar el servidor)
  searchInput.addEventListener("input", (e) => {
    currentQuery = e.target.value;

    // Respondemos en tiempo real solo si ya estamos en la página de resultados
    if (window.location.pathname.includes("/buscar")) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        performSearch(currentQuery, currentFilter);
      }, 300); // Pequeña pausa de 300ms para esperar a que termine de escribir
    }
  });

  // Interceptamos el "Enter" para que no recargue la página si ya estamos buscando
  const searchForm = document.querySelector(".search-form");
  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      if (window.location.pathname.includes("/buscar")) {
        e.preventDefault(); // Delegamos el trabajo al evento de tipeo de arriba
        performSearch(currentQuery, currentFilter);
      }
    });
  }

  // Magia al tocar los botoncitos de filtros
  if (searchFilters) {
    searchFilters.addEventListener("click", (e) => {
      if (
        e.target.tagName === "BUTTON" ||
        e.target.classList.contains("filter-btn")
      ) {
        e.preventDefault();
        // Despintamos todos los filtros
        searchFilters
          .querySelectorAll("button")
          .forEach((b) => b.classList.remove("active"));
        // Pintamos el que el usuario acaba de tocar
        e.target.classList.add("active");

        currentFilter = e.target.dataset.filter;
        performSearch(currentQuery, currentFilter);
      }
    });
  }

  // ================== FILTRADO EN MODALES DE SEGUIDORES ==================
  const modalSearchInputs = document.querySelectorAll('.search-followers');
  modalSearchInputs.forEach(input => {
    input.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();
      const modal = this.closest('.modal');
      const users = modal.querySelectorAll('.modal-user');

      users.forEach(user => {
        const username = user.querySelector('span').textContent.toLowerCase();
        if (username.includes(query)) {
          user.style.display = 'flex';
        } else {
          user.style.display = 'none';
        }
      });
    });
  });
});

// ================== EDITAR PERFIL - PREVIEW FOTO ==================
const fotoInput = document.getElementById('foto-preview');
if (fotoInput) {
  const fileInput = document.querySelector('.editar-form input[type="file"]');
  if (fileInput) {
    fileInput.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('foto-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
}