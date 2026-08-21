import React, { createContext, useContext, useEffect, useState } from 'react';
import { api, getToken, setToken } from './api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!getToken()) {
            setLoading(false);
            return;
        }
        api('/auth/me')
            .then((d) => setUser(d.user))
            .catch(() => {})
            .finally(() => setLoading(false));
    }, []);

    async function loginWithPassword(email, password) {
        const d = await api('/auth/login', { method: 'POST', body: { email, password } });
        setToken(d.token);
        setUser(d.user);
    }

    function loginWithGoogleToken(token) {
        setToken(token);
        return api('/auth/me').then((d) => setUser(d.user));
    }

    async function logout() {
        try {
            await api('/auth/logout', { method: 'POST' });
        } catch {
            /* abaikan */
        }
        localStorage.removeItem('redaksi_token');
        setUser(null);
    }

    return (
        <AuthContext.Provider value={{ user, setUser, loading, loginWithPassword, loginWithGoogleToken, logout }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    return useContext(AuthContext);
}
