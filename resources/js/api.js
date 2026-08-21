const TOKEN_KEY = 'redaksi_token';

export function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken() {
    localStorage.removeItem(TOKEN_KEY);
}

export async function api(path, { method = 'GET', body, isForm = false } = {}) {
    const headers = { Accept: 'application/json' };
    const token = getToken();
    if (token) headers.Authorization = `Bearer ${token}`;
    if (body && !isForm) headers['Content-Type'] = 'application/json';

    const res = await fetch(`/api${path}`, {
        method,
        headers,
        body: body ? (isForm ? body : JSON.stringify(body)) : undefined,
    });

    if (res.status === 401) {
        clearToken();
        window.location.href = '/login';
        throw new Error('Sesi berakhir. Silakan login ulang.');
    }

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const message =
            data.message ||
            Object.values(data.errors || {}).flat().join(', ') ||
            'Terjadi kesalahan.';
        const err = new Error(message);
        err.status = res.status;
        err.errors = data.errors;
        throw err;
    }
    return data;
}
