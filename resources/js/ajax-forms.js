const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

const parseErrors = (errors) => Object.values(errors || {}).flat().filter(Boolean);

const setFeedback = (form, messages) => {
    const box = form.querySelector('[data-ajax-feedback]');
    if (!box) return;

    if (!messages.length) {
        box.innerHTML = '';
        box.classList.add('hidden');
        return;
    }

    box.innerHTML = `
        <ul class="space-y-1">
            ${messages.map((message) => `<li>${message}</li>`).join('')}
        </ul>
    `;
    box.classList.remove('hidden');
};

const setSubmitting = (form, submitting) => {
    const button = form.querySelector('[type="submit"]');
    if (!button) return;

    button.disabled = submitting;
    button.classList.toggle('opacity-60', submitting);
    button.classList.toggle('cursor-not-allowed', submitting);
};

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-ajax-form]');
    if (!form) return;

    event.preventDefault();
    setFeedback(form, []);
    setSubmitting(form, true);

    try {
        const response = await fetch(form.action, {
            method: form.method || 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });

        if (response.ok) {
            const data = await response.json().catch(() => ({}));
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            window.location.reload();
            return;
        }

        if (response.status === 422) {
            const data = await response.json();
            setFeedback(form, parseErrors(data.errors));
            return;
        }

        const data = await response.json().catch(() => ({}));
        setFeedback(form, [data.message || 'No se pudo completar la operacion.']);
    } catch (error) {
        setFeedback(form, ['No se pudo conectar con el servidor.']);
    } finally {
        setSubmitting(form, false);
    }
});
