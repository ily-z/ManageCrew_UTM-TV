import QRCode from 'qrcode';

const params = new URLSearchParams(window.location.search);
const tipe = params.get('tipe') || 'piket';
const id = params.get('id');

const canvas = document.getElementById('qr-canvas');
const status = document.getElementById('status');
const title = document.getElementById('title');

title.textContent = tipe === 'piket' ? 'Scan QR Absen Piket' : 'Scan QR Absen Kegiatan';

let lastToken = null;

async function refresh() {
    try {
        const res = await fetch(`/api/qr/aktif?tipe=${tipe}&id=${id}`, {
            headers: { Accept: 'application/json', Authorization: `Bearer ${window.qrToken}` },
        });
        if (!res.ok) throw new Error('gagal');
        const data = await res.json();
        if (data.token !== lastToken) {
            lastToken = data.token;
            QRCode.toCanvas(canvas, data.token, { width: 360 }, () => {});
        }
        const sisa = Math.max(0, Math.round(data.ttl));
        status.textContent = `QR diperbarui otomatis · berlaku ${sisa}s`;
    } catch {
        status.textContent = 'Gagal memuat QR. Periksa koneksi.';
    }
}

refresh();
setInterval(refresh, 5000);
