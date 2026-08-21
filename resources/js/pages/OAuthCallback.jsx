import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../auth.jsx';

export default function OAuthCallback() {
    const { loginWithGoogleToken } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        // Token dikirim backend via URL fragment: /oauth/callback#token=xxx
        const hash = window.location.hash;
        const match = hash.match(/token=([^&]+)/);
        if (!match) {
            navigate('/login?error=google', { replace: true });
            return;
        }
        loginWithGoogleToken(decodeURIComponent(match[1]))
            .then(() => navigate('/', { replace: true }))
            .catch(() => navigate('/login?error=google', { replace: true }));
    }, []);

    return <div className="p-8 text-center text-slate-500">Menyelesaikan login…</div>;
}
