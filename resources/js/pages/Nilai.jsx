import React, { useEffect, useState } from 'react';
import { api } from '../api';
import { Card, Empty, Section } from './Dashboard.jsx';

export default function Nilai() {
    const [nilai, setNilai] = useState([]);
    const [rataRata, setRataRata] = useState(0);

    useEffect(() => {
        api('/nilai/saya').then((d) => {
            setNilai(d.nilai);
            setRataRata(d.rata_rata);
        });
    }, []);

    return (
        <div className="space-y-6">
            {nilai.length > 0 && (
                <div className="rounded-2xl bg-gradient-to-r from-amber-500 to-orange-400 p-6 text-center text-white shadow">
                    <div className="text-xs uppercase tracking-wide opacity-80">Rata-rata nilai keseluruhan</div>
                    <div className="text-5xl font-bold">{rataRata.toFixed(2)}</div>
                    <div className="text-xs opacity-80">dari {nilai.length} kegiatan dinilai</div>
                </div>
            )}

            <Section title="Nilai per Kegiatan">
                {nilai.length === 0 && <Empty>Belum ada nilai. Nilai muncul setelah admin menilai kegiatanmu.</Empty>}
                {nilai.map((n) => (
                    <Card key={n.id}>
                        <div className="flex items-start justify-between gap-3">
                            <div>
                                <div className="font-medium">{n.kegiatan?.nama}</div>
                                <div className="mt-1 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-slate-500">
                                    <Kriteria label="Partisipasi" v={n.kriteria?.partisipasi} />
                                    <Kriteria label="Ketepatan waktu" v={n.kriteria?.ketepatan_waktu} />
                                    <Kriteria label="Kualitas tugas" v={n.kriteria?.kualitas_tugas} />
                                    <Kriteria label="Kualitas logbook" v={n.kriteria?.kualitas_logbook} />
                                </div>
                                {n.catatan && <div className="mt-2 rounded-lg bg-slate-50 px-3 py-1.5 text-sm text-slate-600">{n.catatan}</div>}
                            </div>
                            <div
                                className={`shrink-0 rounded-xl px-3 py-2 text-lg font-bold ${
                                    n.nilai_total >= 80 ? 'bg-green-100 text-green-700' : n.nilai_total >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'
                                }`}
                            >
                                {Number(n.nilai_total).toFixed(1)}
                            </div>
                        </div>
                    </Card>
                ))}
            </Section>
        </div>
    );
}

function Kriteria({ label, v }) {
    return (
        <div className="flex justify-between gap-2">
            <span>{label}</span>
            <span className="font-semibold text-slate-700">{v ?? '-'}</span>
        </div>
    );
}
