import React from 'react';
import { Navigate, NavLink, Route, Routes, useNavigate } from 'react-router-dom';
import { useAuth } from './auth.jsx';
import Login from './pages/Login.jsx';
import OAuthCallback from './pages/OAuthCallback.jsx';
import Dashboard from './pages/Dashboard.jsx';
import Scan from './pages/Scan.jsx';
import Izin from './pages/Izin.jsx';
import Logbook from './pages/Logbook.jsx';
import Nilai from './pages/Nilai.jsx';
import Profil from './pages/Profil.jsx';

const NAV = [
    { to: '/', label: 'Beranda', icon: '🏠' },
    { to: '/scan', label: 'Scan QR', icon: '📷' },
    { to: '/izin', label: 'Ajukan Izin', icon: '📝' },
    { to: '/logbook', label: 'Logbook', icon: '📖' },
    { to: '/nilai', label: 'Nilai', icon: '⭐' },
    { to: '/profil', label: 'Profil', icon: '👤' },
];

function Shell({ children }) {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    async function handleLogout() {
        await logout();
        navigate('/login');
    }

    return (
        <div className="min-h-screen bg-slate-100 text-slate-800">
            <header className="bg-amber-500 text-white shadow">
                <div className="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
                    <div className="font-semibold tracking-wide">📰 Redaksi — Anggota</div>
                    <button onClick={handleLogout} className="rounded-lg bg-white/20 px-3 py-1 text-sm hover:bg-white/30">
                        Keluar
                    </button>
                </div>
                <nav className="mx-auto max-w-3xl overflow-x-auto px-2 pb-2">
                    <div className="flex gap-1">
                        {NAV.map((n) => (
                            <NavLink
                                key={n.to}
                                to={n.to}
                                className={({ isActive }) =>
                                    `flex shrink-0 items-center gap-1 rounded-t-lg px-3 py-1.5 text-sm ${
                                        isActive ? 'bg-white font-semibold text-amber-600' : 'text-white/90 hover:bg-white/10'
                                    }`
                                }
                                end={n.to === '/'}
                            >
                                <span>{n.icon}</span> {n.label}
                            </NavLink>
                        ))}
                    </div>
                </nav>
            </header>
            <main className="mx-auto max-w-3xl px-4 py-6">{children}</main>
            <footer className="pb-6 text-center text-xs text-slate-400">
                {user?.name} · {user?.divisi || 'tanpa divisi'}
            </footer>
        </div>
    );
}

function Protected({ children }) {
    const { user, loading } = useAuth();
    if (loading) return <div className="p-8 text-center text-slate-400">Memuat…</div>;
    if (!user) return <Navigate to="/login" replace />;
    return <Shell>{children}</Shell>;
}

export default function App() {
    return (
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/oauth/callback" element={<OAuthCallback />} />
            <Route path="/" element={<Protected><Dashboard /></Protected>} />
            <Route path="/scan" element={<Protected><Scan /></Protected>} />
            <Route path="/izin" element={<Protected><Izin /></Protected>} />
            <Route path="/logbook" element={<Protected><Logbook /></Protected>} />
            <Route path="/nilai" element={<Protected><Nilai /></Protected>} />
            <Route path="/profil" element={<Protected><Profil /></Protected>} />
            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    );
}
