import React, { useEffect, useState } from 'react';
import { api } from '../api';
import { Card, Empty, Section, StatusBadge, formatDate } from './Dashboard.jsx';

export default function Izin() {
    const [piket, setPiket] = useState([]);
    const [kegiatan, setKegiatan] = useState([]);
    const [tipe, setTipe] = useState('piket');
    const [jenis, setJenis] = useState('izin');
    const [referenceId, setReferenceId] = useState('');
    const [keterangan, setKeterangan] = useState('');
    const [bukti, setBukti] = useState(null);
    const [msg, setMsg] = useState('');
    const [error, setError] = useState('');
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        api('/piket/saya').then((d) => {
            setPiket(d.jadwal_piket);
            if (d.jadwal_piket[0]) setReferenceId(d.jadwal_piket[0].id);
        });
        api('/kegiatan/saya').then((d) => setKegiatan(d.kegiatan));
    }, []);

    function changeTipe(t) {
        setTipe(t);
        setReferenceId('');
    }

    async function submit(e) {
        e.preventDefault();
        setBusy(true);
        setError('');
        setMsg('');

        const form = new FormData();
        form.append('tipe', tipe);
        form.append('reference_id', referenceId);
        form.append('jenis', jenis);
        form.append('keterangan', keterangan);
        if (bukti) form.append('bukti', bukti);

        try {
            const d = await api('/absen/izin', { method: 'POST', body: form, isForm: true });
            setMsg(d.message);
            setKeterangan('');
            setBukti(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setBusy(false);
        }
    }

    const options = tipe === 'piket' ? piket : kegiatan;

    return (
        <div className="space-y-6">
            <Section title="Ajukan Izin / Sakit">
                <Card>
                    <form onSubmit={submit} className="space-y-3">
                        <div className="grid grid-cols-2 gap-3">
                            <Field label="Untuk">
                                <select value={tipe} onChange={(e) => changeTipe(e.target.value)} className={inputCls}>
                                    <option value="piket">Piket</option>
                                    <option value="kegiatan">Kegiatan</option>
                                </select>
                            </Field>
                            <Field label="Jenis">
                                <select value={jenis} onChange={(e) => setJenis(e.target.value)} className={inputCls}>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </Field>
                        </div>

                        <Field label={tipe === 'piket' ? 'Jadwal piket' : 'Kegiatan'}>
                            <select required value={referenceId} onChange={(e) => setReferenceId(e.target.value)} className={inputCls}>
                                <option value="">— pilih —</option>
                                {options.map((o) => (
                                    <option key={o.id} value={o.id}>
                                        {formatDate(o.hari || o.tanggal)}
                                    </option>
                                ))}
                            </select>
                        </Field>

                        <Field label="Keterangan">
                            <textarea
                                required
                                rows={3}
                                value={keterangan}
                                onChange={(e) => setKeterangan(e.target.value)}
                                placeholder="Jelaskan alasan izin/sakit…"
                                className={inputCls}
                            />
                        </Field>

                        <Field label="Bukti (opsional, foto/surat)">
                            <input
                                type="file"
                                accept="image/*"
                                onChange={(e) => setBukti(e.target.files[0])}
                                className="text-sm"
                            />
                        </Field>

                        {msg && <div className="rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{msg}</div>}
                        {error && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{error}</div>}

                        <button disabled={busy} className="w-full rounded-xl bg-amber-500 py-2.5 font-semibold text-white hover:bg-amber-600 disabled:opacity-50">
                            {busy ? 'Mengirim…' : 'Kirim Pengajuan'}
                        </button>
                    </form>
                </Card>
            </Section>

            <Section title="Riwayat Absensi">
                <Riwayat />
            </Section>
        </div>
    );
}

function Riwayat() {
    const [data, setData] = useState({ piket: [], kegiatan: [] });

    useEffect(() => {
        api('/absen/riwayat').then(setData);
    }, []);

    const rows = [
        ...data.piket.map((a) => ({ ...a, _tipe: 'Piket', _slot: formatDate(a.jadwal_piket?.hari) })),
        ...data.kegiatan.map((a) => ({ ...a, _tipe: 'Kegiatan', _slot: a.kegiatan?.nama })),
    ];

    if (rows.length === 0) return <Empty>Belum ada riwayat.</Empty>;

    return (
        <>
            {rows.map((a) => (
                <Card key={`${a._tipe}-${a.id}`}>
                    <div className="flex items-center justify-between gap-2">
                        <div>
                            <div className="font-medium">{a._slot}</div>
                            <div className="text-xs uppercase tracking-wide text-slate-400">{a._tipe}</div>
                            {a.keterangan && <div className="mt-1 text-sm text-slate-500">{a.keterangan}</div>}
                        </div>
                        <div className="flex flex-col items-end gap-1">
                            <StatusBadge status={a.status} />
                            {a.status_approval_izin && (
                                <span className="text-xs text-slate-400">approval: {a.status_approval_izin}</span>
                            )}
                        </div>
                    </div>
                </Card>
            ))}
        </>
    );
}

const inputCls = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500';

function Field({ label, children }) {
    return (
        <label className="block">
            <span className="mb-1 block text-xs font-medium text-slate-500">{label}</span>
            {children}
        </label>
    );
}
