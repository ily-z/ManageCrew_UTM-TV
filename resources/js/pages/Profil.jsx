import React, { useState } from 'react';
import { api } from '../api';
import { useAuth } from '../auth.jsx';
import { Card } from './Dashboard.jsx';

export default function Profil() {
    const { user, setUser } = useAuth();
    const [noHp, setNoHp] = useState(user.no_hp || '');
    const [divisi, setDivisi] = useState(user.divisi || '');
    const [angkatan, setAngkatan] = useState(user.angkatan || '');
    const [foto, setFoto] = useState(null);
    const [msg, setMsg] = useState('');
    const [error, setError] = useState('');

    async function submit(e) {
        e.preventDefault();
        setMsg('');
        setError('');

        const form = new FormData();
        form.append('no_hp', noHp);
        form.append('divisi', divisi);
        form.append('angkatan', angkatan);
        if (foto) form.append('foto', foto);

        try {
            const d = await api('/me', { method: 'PUT', body: form, isForm: true });
            setUser(d.user);
            setMsg(d.message);
        } catch (err) {
            setError(err.message);
        }
    }

    return (
        <div className="space-y-6">
            <Card>
                <div className="flex items-center gap-4">
                    <img
                        src={user.foto ? (user.foto.startsWith('http') ? user.foto : `/storage/${user.foto}`) : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`}
                        alt={user.name}
                        className="h-16 w-16 rounded-full object-cover"
                    />
                    <div>
                        <div className="font-semibold">{user.name}</div>
                        <div className="text-sm text-slate-500">{user.email}</div>
                        <div className="mt-1 flex gap-2 text-xs">
                            <span className="rounded-full bg-amber-100 px-2 py-0.5 capitalize text-amber-700">{user.role}</span>
                            <span className={`rounded-full px-2 py-0.5 ${user.status_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}`}>
                                {user.status_aktif ? 'aktif' : 'nonaktif'}
                            </span>
                        </div>
                    </div>
                </div>
            </Card>

            <Card>
                <form onSubmit={submit} className="space-y-3">
                    <Field label="No. HP">
                        <input value={noHp} onChange={(e) => setNoHp(e.target.value)} className={cls} />
                    </Field>
                    <Field label="Divisi">
                        <input value={divisi} onChange={(e) => setDivisi(e.target.value)} className={cls} />
                    </Field>
                    <Field label="Angkatan">
                        <input value={angkatan} onChange={(e) => setAngkatan(e.target.value)} className={cls} />
                    </Field>
                    <Field label="Ganti foto profil">
                        <input type="file" accept="image/*" onChange={(e) => setFoto(e.target.files[0])} className="text-sm" />
                    </Field>
                    <p className="text-xs text-slate-400">Nama & email berasal dari akun Google dan tidak bisa diubah manual.</p>

                    {msg && <div className="rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{msg}</div>}
                    {error && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{error}</div>}

                    <button className="w-full rounded-xl bg-amber-500 py-2.5 font-semibold text-white hover:bg-amber-600">
                        Simpan Perubahan
                    </button>
                </form>
            </Card>
        </div>
    );
}

const cls = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500';

function Field({ label, children }) {
    return (
        <label className="block">
            <span className="mb-1 block text-xs font-medium text-slate-500">{label}</span>
            {children}
        </label>
    );
}
