import anime from "animejs";
import ScrollReveal from "scrollreveal";
const bootstrap = require('bootstrap');

const win = window
const doc = document.documentElement

doc.classList.remove('no-js')
doc.classList.add('js')

// Reveal animations
if (document.body.classList.contains('has-animations')) {
/* global ScrollReveal */
const sr = window.sr = ScrollReveal()

sr.reveal('.feature, .pricing-table-inner', {
  duration: 600,
  distance: '20px',
  easing: 'cubic-bezier(0.5, -0.01, 0, 1.005)',
  origin: 'bottom',
  interval: 100
})

doc.classList.add('anime-ready')
/* global anime */
anime.timeline({
  targets: '.hero-figure-box-05'
}).add({
  duration: 400,
  easing: 'easeInOutExpo',
  scaleX: [0.05, 0.05],
  scaleY: [0, 1],
  perspective: '500px',
  delay: anime.random(0, 400)
}).add({
  duration: 400,
  easing: 'easeInOutExpo',
  scaleX: 1
}).add({
  duration: 800,
  rotateY: '-15deg',
  rotateX: '8deg',
  rotateZ: '-1deg'
})

anime.timeline({
  targets: '.hero-figure-box-06, .hero-figure-box-07'
}).add({
  duration: 400,
  easing: 'easeInOutExpo',
  scaleX: [0.05, 0.05],
  scaleY: [0, 1],
  perspective: '500px',
  delay: anime.random(0, 400)
}).add({
  duration: 400,
  easing: 'easeInOutExpo',
  scaleX: 1
}).add({
  duration: 800,
  rotateZ: '20deg'
})

anime({
  targets: '.hero-figure-box-01, .hero-figure-box-02, .hero-figure-box-03, .hero-figure-box-04, .hero-figure-box-08, .hero-figure-box-09, .hero-figure-box-10',
  duration: anime.random(600, 800),
  delay: anime.random(600, 800),
  rotate: [ anime.random(-360, 360), function (el) { return el.getAttribute('data-rotation') } ],
  scale: [0.7, 1],
  opacity: [0, 1],
  easing: 'easeInOutExpo'
})
}

// ----------------------------------------------------------------------

const orderModal = new bootstrap.Modal('#order-modal', {});

const orderButtons = document.querySelectorAll('.order_button');
orderButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
    event.preventDefault();
      orderModal.show();
  }
  );
});

// -----------------------------------------------------------------

const validate = el => {
  const checkboxes = el.querySelectorAll('input[type="checkbox"]');
  return [...checkboxes].some(e => e.checked);
};

const formEl = document.querySelector('form');
const statusEl = formEl.querySelector('.status-message');
const checkboxGroupEl = formEl.querySelector('#order_products');

formEl.addEventListener('submit', e => {
  if (!validate(checkboxGroupEl)) {
    e.preventDefault();
    statusEl.textContent = "Error: select at least one checkbox";
    statusEl.classList.remove('d-none');
  }
});

// ---------------------------------------------------------------

let totalItems = 0;
let currentOffset = 0;
let lock = false;
const inProgressModal = new bootstrap.Modal('#inProgressModal', {});

window.addEventListener('scroll', (event) => {
  const windowHeight = window.innerHeight;
  const windowScroll = window.scrollY;
  const documentHeight = document.body.scrollHeight;

  if (windowHeight + windowScroll >= documentHeight) {
    if (currentOffset < totalItems) {
      fetchData(ajaxRequestItemsLimit, currentOffset);
    }
  }
});

fetchData(ajaxRequestItemsLimit, currentOffset);

// ---------------------------------------

function addItem(item) {
  const itemDiv = document.createElement('div');
  let html =
      `<div class="card">
        <div class="card-body">`;

  html += `<span class="badge bg-primary">${item.created_at}</span><br>`;
  html += `${item.author}<br>`;
  html += `${item.message}<br>`;

  html += `</div>
             </div>`;
  html += `<br>`;
  itemDiv.innerHTML = html;
  const contentDiv = document.querySelector('.reviews');
  contentDiv.appendChild(itemDiv);
}

// ---------------------------------------

function fetchData(limit, offset) {
  if (lock) {
    return;
  }

  lock = true;
  inProgress(true);

  const url = new URL(reviewListFetchUrl);
  url.searchParams.append('limit', limit);
  url.searchParams.append('offset', offset);
  fetch(url.href)
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        if (data.status === 'ok') {

          totalItems = data.total;

          data.items.forEach((item) => {
            addItem(item);
          });

          if (offset < data.total) {
            currentOffset += ajaxRequestItemsLimit;
          }

          lock = false;
          inProgress(false);
        }
      });
}

// ---------------------------------------

function inProgress(show) {
  if (show) {
    inProgressModal.show();
  } else {
    setTimeout(() => {
      inProgressModal.hide();
    }, 1500);
  }
}
