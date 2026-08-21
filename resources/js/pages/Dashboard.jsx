import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api';

const STATUS_STYLE = {
    hadir: 'bg-green-100 text-green-700',
    terlambat: 'bg-yellow-100 text-yellow-700',
    izin: 'bg-blue-100 text-blue-700',
    sakit: 'bg-sky-100 text-sky-700',
    alpa: 'bg-red-100 text-red-700',
    belum_absen: 'bg-slate-200 text-slate-500',
};

export function StatusBadge({ status }) {
    return (
        <span className={`rounded-full px-2 py-0.5 text-xs font-medium ${STATUS_STYLE[status] || STATUS_STYLE.belum_absen}`}>
            {(status || 'belum_absen').replace('_', ' ')}
        </span>
    );
}

export default function Dashboard() {
    const [piket, setPiket] = useState([]);
    const [kegiatan, setKegiatan] = useState([]);

    useEffect(() => {
        api('/piket/saya').then((d) => setPiket(d.jadwal_piket));
        api('/kegiatan/saya').then((d) => setKegiatan(d.kegiatan));
    }, []);

    return (
        <div className="space-y-6">
            <div className="grid grid-cols-2 gap-3">
                <Link to="/scan" className="rounded-2xl bg-amber-500 p-5 text-white shadow hover:bg-amber-600">
                    <div className="text-3xl">📷</div>
                    <div className="mt-2 font-semibold">Absen Sekarang</div>
                    <div className="text-xs opacity-80">Scan QR di lokasi</div>
                </Link>
                <Link to="/izin" className="rounded-2xl bg-white p-5 shadow hover:bg-slate-50">
                    <div className="text-3xl">📝</div>
                    <div className="mt-2 font-semibold text-slate-800">Izin / Sakit</div>
                    <div className="text-xs text-slate-400">Ajukan & pantau approval</div>
                </Link>
            </div>

            <Section title="Jadwal Piket Saya">
                {piket.length === 0 && <Empty>Belum ada jadwal piket.</Empty>}
                {piket.map((j) => (
                    <Card key={j.id}>
                        <div className="flex items-center justify-between gap-2">
                            <div>
                                <div className="font-medium">{formatDate(j.hari)}</div>
                                <div className="text-sm text-slate-500">{j.jam_mulai?.slice(0, 5)} – {j.jam_selesai?.slice(0, 5)}</div>
                            </div>
                            <StatusBadge status={j.absensi?.status} />
                        </div>
                    </Card>
                ))}
            </Section>

            <Section title="Kegiatan Saya">
                {kegiatan.length === 0 && <Empty>Belum ada kegiatan.</Empty>}
                {kegiatan.map((k) => (
                    <Card key={k.id}>
                        <div className="flex items-center justify-between gap-2">
                            <div>
                                <div className="font-medium capitalize">{k.nama}</div>
                                <div className="text-sm text-slate-500">
                                    {formatDate(k.tanggal)} · {k.lokasi}
                                </div>
                                {k.tugas_saya && <div className="mt-1 text-xs text-amber-600">Tugas: {k.tugas_saya}</div>}
                            </div>
                            <div className="flex flex-col items-end gap-1">
                                <StatusBadge status={k.absensi?.status} />
                                {k.logbook && <StatusBadge status={k.logbook.status} />}
                            </div>
                        </div>
                        {!k.logbook && k.status !== 'draft' && (
                            <Link to="/logbook" className="mt-2 inline-block text-sm font-medium text-amber-600 hover:underline">
                                + Isi logbook
                            </Link>
                        )}
                    </Card>
                ))}
            </Section>
        </div>
    );
}

export function Section({ title, children }) {
    return (
        <section>
            <h2 className="mb-2 text-sm font-semibold uppercase tracking-wide text-slate-400">{title}</h2>
            <div className="space-y-2">{children}</div>
        </section>
    );
}

export function Card({ children }) {
    return <div className="rounded-xl bg-white p-4 shadow-sm">{children}</div>;
}

export function Empty({ children }) {
    return <div className="rounded-xl bg-white p-4 text-center text-sm text-slate-400 shadow-sm">{children}</div>;
}

export function formatDate(iso) {
    if (!iso) return '-';
    const d = new Date(iso);
    return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}
