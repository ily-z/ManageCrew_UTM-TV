import React, { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Html5Qrcode } from 'html5-qrcode';
import { api } from '../api';

export default function Scan() {
    const [result, setResult] = useState(null);
    const [error, setError] = useState('');
    const [scanning, setScanning] = useState(false);
    const scannerRef = useRef(null);
    const navigate = useNavigate();

    useEffect(() => {
        return () => {
            if (scannerRef.current?.isScanning) {
                scannerRef.current.stop().catch(() => {});
            }
        };
    }, []);

    async function startScan() {
        setError('');
        setResult(null);
        try {
            const scanner = new Html5Qrcode('qr-reader');
            scannerRef.current = scanner;
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                async (decodedText) => {
                    await scanner.stop().catch(() => {});
                    setScanning(false);
                    await submitToken(decodedText);
                },
                () => {} // abaikan frame tanpa QR
            );
            setScanning(true);
        } catch (err) {
            setError('Tidak bisa membuka kamera. Pastikan izin kamera diberikan, atau gunakan input manual.');
        }
    }

    async function submitToken(token) {
        try {
            const d = await api('/absen/scan', { method: 'POST', body: { token } });
            setResult(d);
        } catch (err) {
            setError(err.message);
        }
    }

    function manualSubmit(e) {
        e.preventDefault();
        const token = e.target.token.value.trim();
        if (token) submitToken(token);
        e.target.reset();
    }

    return (
        <div className="space-y-4">
            <h1 className="text-lg font-bold">Scan QR Absensi</h1>
            <p className="text-sm text-slate-500">Arahkan kamera ke QR yang tampil di layar kantor / lokasi kegiatan.</p>

            <div id="qr-reader" className="mx-auto w-full max-w-xs overflow-hidden rounded-2xl bg-slate-800" />

            {!scanning && !result && (
                <button
                    onClick={startScan}
                    className="mx-auto block rounded-xl bg-amber-500 px-6 py-2.5 font-semibold text-white hover:bg-amber-600"
                >
                    Buka Kamera
                </button>
            )}

            <form onSubmit={manualSubmit} className="rounded-xl bg-white p-4 shadow-sm">
                <label className="mb-1 block text-xs font-medium text-slate-400">Input token manual (fallback)</label>
                <div className="flex gap-2">
                    <input name="token" className="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-sm outline-none focus:border-amber-500" />
                    <button className="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white hover:bg-slate-700">Kirim</button>
                </div>
            </form>

            {result && (
                <div className="rounded-xl bg-green-50 p-4 text-center">
                    <div className="text-3xl">✅</div>
                    <div className="mt-1 font-semibold text-green-700">{result.message}</div>
                    <div className="text-sm text-green-600 capitalize">{String(result.status).replace('_', ' ')} · {result.waktu_scan}</div>
                    <button onClick={() => navigate('/')} className="mt-3 rounded-lg bg-green-600 px-4 py-1.5 text-sm text-white">
                        Selesai
                    </button>
                </div>
            )}

            {error && (
                <div className="rounded-xl bg-red-50 p-4 text-center">
                    <div className="text-sm text-red-600">{error}</div>
                    <button onClick={() => setError('')} className="mt-2 rounded-lg bg-red-600 px-4 py-1.5 text-sm text-white">
                        Coba Lagi
                    </button>
                </div>
            )}
        </div>
    );
}
