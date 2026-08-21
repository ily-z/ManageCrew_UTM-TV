import React, { useEffect, useState } from 'react';
import { api } from '../api';
import { Card, Empty, Section, StatusBadge } from './Dashboard.jsx';

export default function Logbook() {
    const [kegiatan, setKegiatan] = useState([]);
    const [logbooks, setLogbooks] = useState([]);

    function refresh() {
        api('/kegiatan/saya').then((d) => setKegiatan(d.kegiatan));
        api('/logbook').then((d) => setLogbooks(d.logbook));
    }

    useEffect(refresh, []);

    return (
        <div className="space-y-6">
            <Section title="Tulis Logbook Kegiatan">
                {kegiatan.length === 0 && <Empty>Belum ada kegiatan yang diikuti.</Empty>}
                {kegiatan.map((k) => (
                    <LogbookForm key={k.id} kegiatan={k} onSaved={refresh} />
                ))}
            </Section>

            <Section title="Riwayat Logbook">
                {logbooks.length === 0 && <Empty>Belum ada logbook terkirim.</Empty>}
                {logbooks.map((l) => (
                    <Card key={l.id}>
                        <div className="flex items-center justify-between">
                            <div className="font-medium">{l.kegiatan?.nama}</div>
                            <StatusBadge status={l.status === 'approved' ? 'hadir' : l.status === 'revisi' ? 'alpa' : 'izin'} />
                        </div>
                        <p className="mt-1 whitespace-pre-line text-sm text-slate-600">{l.isi}</p>
                        {l.catatan_admin && (
                            <div className={`mt-2 rounded-lg px-3 py-2 text-sm ${l.status === 'revisi' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-700'}`}>
                                Catatan admin: {l.catatan_admin}
                            </div>
                        )}
                    </Card>
                ))}
            </Section>
        </div>
    );
}

function LogbookForm({ kegiatan, onSaved }) {
    const existing = kegiatan.logbook;
    const locked = existing && existing.status === 'approved';

    if (locked) {
        return (
            <Card>
                <div className="flex items-center justify-between">
                    <div className="font-medium">{kegiatan.nama}</div>
                    <span className="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">logbook approved</span>
                </div>
            </Card>
        );
    }

    async function submit(e) {
        e.preventDefault();
        const formEl = e.target;
        const isi = formEl.isi.value;
        if (!isi || isi.length < 10) return;

        const form = new FormData();
        form.append('isi', isi);
        if (formEl.lampiran.files[0]) form.append('lampiran', formEl.lampiran.files[0]);

        try {
            if (existing) {
                await api(`/logbook/${existing.id}`, { method: 'PUT', body: form, isForm: true });
            } else {
                form.append('kegiatan_id', kegiatan.id);
                await api('/logbook', { method: 'POST', body: form, isForm: true });
            }
            onSaved();
        } catch (err) {
            alert(err.message);
        }
    }

    return (
        <Card>
            <div className="mb-2 flex items-center justify-between">
                <div className="font-medium">{kegiatan.nama}</div>
                {existing && <span className="text-xs font-semibold text-yellow-600">perlu revisi — edit di bawah</span>}
            </div>
            {existing?.catatan_admin && (
                <div className="mb-2 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">Catatan admin: {existing.catatan_admin}</div>
            )}
            <form onSubmit={submit} className="space-y-2">
                <textarea
                    name="isi"
                    rows={3}
                    defaultValue={existing?.isi || ''}
                    placeholder="Ceritakan apa yang kamu kerjakan pada kegiatan ini…"
                    className={cls}
                    required
                    minLength={10}
                />
                <input type="file" name="lampiran" className="text-sm" />
                <button className="w-full rounded-lg bg-amber-500 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                    {existing ? 'Update & Kirim Ulang' : 'Kirim Logbook'}
                </button>
            </form>
        </Card>
    );
}

const cls = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500';
