document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form.searchandfilter');
    if (form) {
        const currentURL = window.location.origin + window.location.pathname;

        form.setAttribute('action', currentURL); // força envio para a mesma URL

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value && value !== '0') params.append(key, value);
            }

            window.location.href = `${currentURL}?${params.toString()}`;
        });
    }
});
