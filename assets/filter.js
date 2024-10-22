function filterByTag(userID) {
    const startAtInput = document.getElementById('startAt');
    const endAtInput = document.getElementById('endAt');
    const container = document.getElementById("container-events");

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatDateAndHours(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} à ${hours}:${minutes}`;
    }

    function filterEvents() {
        const startDateValue = startAtInput.value;
        const endDateValue = endAtInput.value;

        const startDate = startDateValue ? new Date(startDateValue + 'T00:00:00') : null;
        const endDate = endDateValue ? new Date(endDateValue + 'T23:59:59') : null;

        const params = {};
        if (startDate) {
            params.startAt = startDate.toISOString();
        }
        if (endDate) {
            params.endAt = endDate.toISOString();
        }

        if (params.startAt || params.endAt) {
            const queryString = new URLSearchParams(params).toString();

            fetch(`events/getbytags?${queryString}`)
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';

                    data.forEach(event => {
                        const formattedStartDate = formatDateAndHours(event.startAt);
                        const formattedEndDate = formatDateAndHours(event.endAt);
                        const formattedCreatedAt = formatDate(event.createdAt);

                        const isCurrentUser = event.user.id === Number(userID);
                        const isSubscribed = event.suscribers.some(subscriber => {
                            return Number(subscriber.id) === Number(userID);
                        });

                        let actionButton = '';

                        if (isCurrentUser) {
                            actionButton = `<a href='events/${event.id}/edit' class="text-nowrap btn btn-outline-secondary px-3 py-2 mb-2 fw-semibold rounded-pill">Modifier mes événements</a>`;
                        } else if (!isSubscribed) {
                            actionButton = `<a href='events/suscribe/${event.id}' class="text-nowrap btn btn-outline-success px-3 py-2 mb-2 fw-semibold rounded-pill">Rejoindre l'événement</a>`;
                        } else {
                            actionButton = `<a href='events/unsuscribe/${event.id}' class="text-nowrap btn btn-outline-danger px-3 py-2 mb-2 fw-semibold rounded-pill">Quitter l'événement</a>`;
                        }

                        container.innerHTML += `
                            <div class="col col-xl-3">
                                <div class="card h-100">
                                    <div class="d-flex card-body">
                                        <div class="d-flex flex-column justify-content-center align-items-start">
                                            <div class="d-flex justify-content-around gap-2 mb-2 flex-wrap">
                                                <span class="badge bg-success bg-opacity-25 border-success rounded-pill text-success">Démarre le : ${formattedStartDate}</span>
                                                <span class="badge bg-danger bg-opacity-25 border-danger rounded-pill text-danger">Fin le : ${formattedEndDate}</span>
                                                <span class="badge bg-primary bg-opacity-25 border-primary rounded-pill text-primary mt-1">Participants : ${event.suscribers.length}</span>
                                            </div>
                                            <h5 class="mb-0 card-title text-uppercase">${event.title}</h5>
                                            <p class="card-text mt-2 fw-medium mt-0 mb-0">📍 ${event.location}</p>
                                            <hr>
                                            <p class="card-text mt-2 fw-medium flex-grow-1">${event.content}</p>
                                            <small class="d-block">Créé le : <span class="fw-bold">${formattedCreatedAt}</span> par <span class="fw-bold text-uppercase">${event.user.firstname} ${event.user.lastname}</span></small>
                                            <div class="mt-2">${actionButton}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Il y a eu une erreur!', error);
                });
        }
    }

    startAtInput.addEventListener('change', filterEvents);
    endAtInput.addEventListener('change', filterEvents);
}
