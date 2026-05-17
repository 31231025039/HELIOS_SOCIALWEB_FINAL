// public/assets/js/network.js

/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

const BASE_URL = window.BASE_URL || '/helios/public/';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function initials(name = '') {
    return name
        .split(' ')
        .map(w => w[0])
        .slice(-2)
        .join('')
        .toUpperCase();
}

function bannerStyle(banner) {
    if (!banner) {
        return `class="member-banner bg-light"`;
    }

    if (banner.startsWith('bg-')) {
        return `class="member-banner ${banner}"`;
    }

    return `class="member-banner" style="background:${banner};"`;
}

/*
|--------------------------------------------------------------------------
| Render Card
|--------------------------------------------------------------------------
*/

function renderConnectCard(user, colClass = 'col-6 col-md-4 col-xl-4') {

    const ini = initials(user.name);

    const verifiedBadge = user.verified
        ? `<i class="bi bi-patch-check-fill text-primary ms-1"></i>`
        : '';

    const avatar = user.img
        ? `${BASE_URL}${user.img}`
        : '';

    return `
    <div class="${colClass}">
        <div class="member-card border rounded-3 overflow-hidden position-relative h-100 bg-white shadow-sm">

            <button class="btn-close-member">
                <i class="bi bi-x-lg"></i>
            </button>

            <div ${bannerStyle(user.banner || 'bg-light')}></div>

            <div class="text-center px-2 pb-3">

                <div class="member-avatar">
                    <img
                        src="${avatar}"
                        alt="${user.name}"
                        class="w-100 h-100 object-fit-cover"
                        onerror="
                            this.style.display='none';
                            this.parentElement.innerHTML='${ini}';
                        "
                    >
                </div>

                <h6 class="fw-bold mb-0 text-truncate">
                    ${user.name}
                    ${verifiedBadge}
                </h6>

                <p class="text-muted extra-small mb-2 member-bio">
                    ${user.bio || ''}
                </p>

                <div class="extra-small text-muted mb-3">
                    <i class="bi bi-people-fill me-1"></i>
                    ${user.sub || ''}
                </div>

                <button
                    class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold btn-connect"
                    data-userid="${user.id}"
                >
                    Kết nối
                </button>

            </div>
        </div>
    </div>
    `;
}

/*
|--------------------------------------------------------------------------
| Render Grid
|--------------------------------------------------------------------------
*/

function renderGrid(elementId, users, colClass) {

    const el = document.getElementById(elementId);

    if (!el) return;

    el.innerHTML = users
        .map(user => renderConnectCard(user, colClass))
        .join('');
}

/*
|--------------------------------------------------------------------------
| Load Suggested Users
|--------------------------------------------------------------------------
*/

async function loadSuggestedUsers() {

    try {

        const response = await fetch(
            `${BASE_URL}network/suggestions`
        );

        const users = await response.json();

        renderGrid(
            'suggestedGrid',
            users,
            'col-6 col-md-4 col-xl-3'
        );

    } catch (error) {

        console.error('Load suggestions error:', error);

    }
}

/*
|--------------------------------------------------------------------------
| Event Actions
|--------------------------------------------------------------------------
*/

document.addEventListener('click', async (e) => {

    /*
    |--------------------------------------------------------------------------
    | Close Card
    |--------------------------------------------------------------------------
    */

    const closeBtn = e.target.closest('.btn-close-member');

    if (closeBtn) {

        closeBtn
            .closest('[class^="col-"]')
            .remove();

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Connect Request
    |--------------------------------------------------------------------------
    */

    const connectBtn = e.target.closest('.btn-connect');

    if (!connectBtn) return;

    const receiverId = connectBtn.dataset.userid;

    try {

        const response = await fetch(
            `${BASE_URL}network/send-request`,
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify({
                    receiver_id: receiverId
                })
            }
        );

        const result = await response.json();

        if (result.success) {

            connectBtn.innerHTML =
                `<i class="bi bi-check-lg me-1"></i> Đã gửi`;

            connectBtn.disabled = true;

            connectBtn.classList.remove(
                'btn-outline-primary'
            );

            connectBtn.classList.add(
                'btn-success'
            );

        } else {

            alert('Không thể gửi lời mời');

        }

    } catch (error) {

        console.error('Send request error:', error);

        alert('Có lỗi xảy ra');

    }
});

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/

loadSuggestedUsers();