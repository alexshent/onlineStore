const bootstrap = require('bootstrap');

let totalItems = 0;
let currentOffset = 0;
let lock = false;
const modal = new bootstrap.Modal('#inProgressModal', {});

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
    const productDiv = document.createElement('div');
    let html = `<h4>${item.name}</h4><br>`;
    html += `<h5>${item.description}</h5><br>`;
    item.images.forEach((image) => {
        html += `<img class="product-image" alt="product" src="${imageProductsPath}${image}"><br>`;
    });
    productDiv.innerHTML = html;
    const contentDiv = document.querySelector('.content_div');
    contentDiv.appendChild(productDiv);
}

// ---------------------------------------

function fetchData(limit, offset) {
    if (lock) {
        return;
    }

    lock = true;
    inProgress(true);

    const url = new URL(productListFetchUrl);
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
        modal.show();
    } else {
        setTimeout(() => {
            modal.hide();
        }, 1500);
    }
}
