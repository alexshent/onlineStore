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
    const itemDiv = document.createElement('div');
    let html =
        `<div class="card">
        <div class="card-body">`;

    html += `${item.customer}<br><br>`;
    html += `${item.email}<br><br>`;
    html += `${item.address}<br><br>`;
    html += `<div class="alert alert-success" role="alert">`;
    item.products.forEach((product) => {
        html += `${product}<br>`;
    });
    html += `</div>`;
    html += `</div>
             </div>`;
    html += `<br>`;
    itemDiv.innerHTML = html;
    const contentDiv = document.querySelector('.content_div');
    contentDiv.appendChild(itemDiv);
}

// ---------------------------------------

function fetchData(limit, offset) {
    if (lock) {
        return;
    }

    lock = true;
    inProgress(true);

    const url = new URL(orderListFetchUrl);
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
