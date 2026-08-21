import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../auth.jsx';

export default function Login() {
    const { loginWithPassword } = useAuth();
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [busy, setBusy] = useState(false);

    const params = new URLSearchParams(window.location.search);
    const googleError = params.get('error');

    async function submit(e) {
        e.preventDefault();
        setBusy(true);
        setError('');
        try {
            await loginWithPassword(email, password);
            navigate('/');
        } catch (err) {
            setError(err.message);
        } finally {
            setBusy(false);
        }
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-slate-100 p-4">
            <div className="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg">
                <h1 className="mb-1 text-xl font-bold text-slate-800">📰 Redaksi — Portal Anggota</h1>
                <p className="mb-5 text-sm text-slate-500">Masuk untuk absen piket & kegiatan.</p>

                {googleError === 'nonaktif' && <Alert>Akun Anda dinonaktifkan admin.</Alert>}
                {googleError === 'google' && <Alert>Login Google gagal. Coba lagi.</Alert>}

                <a
                    href="/api/auth/google/redirect"
                    className="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 py-2.5 font-medium text-slate-700 hover:bg-slate-50"
                >
                    <span className="text-lg">🔵</span> Masuk dengan Google
                </a>

                <div className="my-4 flex items-center gap-3 text-xs text-slate-400">
                    <span className="h-px flex-1 bg-slate-200" /> atau email/password (dev) <span className="h-px flex-1 bg-slate-200" />
                </div>

                {error && <Alert>{error}</Alert>}

                <form onSubmit={submit} className="space-y-3">
                    <input
                        type="email"
                        required
                        placeholder="Email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        className="w-full rounded-xl border border-slate-300 px-3 py-2 outline-none focus:border-amber-500"
                    />
                    <input
                        type="password"
                        required
                        placeholder="Password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        className="w-full rounded-xl border border-slate-300 px-3 py-2 outline-none focus:border-amber-500"
                    />
                    <button
                        disabled={busy}
                        className="w-full rounded-xl bg-amber-500 py-2.5 font-semibold text-white hover:bg-amber-600 disabled:opacity-50"
                    >
                        {busy ? 'Memproses…' : 'Masuk'}
                    </button>
                </form>
            </div>
        </div>
    );
}

function Alert({ children }) {
    return <div className="mb-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{children}</div>;
}
