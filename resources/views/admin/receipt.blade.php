<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #e8e0d8;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 16px;
            font-family: 'Courier Prime', 'Courier New', monospace;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background: #1a1a1a;
            color: white;
        }

        .btn-back {
            background: white;
            color: #333;
            border: 1px solid #ccc;
        }

        .receipt {
            width: 100%;
            max-width: 384px;
            background: #fff;
            padding: 24px 20px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .border-dash {
            border-top: 1px dashed #999;
            margin: 10px 0;
        }

        .header-logo {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .header-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .header-sub {
            font-size: 11px;
            line-height: 1.6;
            color: #555;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 3px 0;
        }

        .meta-label {
            color: #555;
        }

        .items-table {
            width: 100%;
            font-size: 12px;
            margin: 4px 0;
        }

        .items-table th {
            text-align: left;
            font-weight: 700;
            padding: 3px 0;
            border-bottom: 1px dashed #999;
        }

        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .items-table .td-name {
            width: 50%;
        }

        .items-table .td-qty {
            width: 12%;
            text-align: center;
        }

        .items-table .td-unit {
            width: 20%;
            text-align: right;
        }

        .items-table .td-amt {
            width: 18%;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 3px 0;
        }

        .total-final {
            font-size: 14px;
            font-weight: 700;
        }

        .footer-text {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-top: 6px;
            line-height: 1.6;
        }

        .footer-brand {
            font-size: 10px;
            color: #aaa;
            margin-top: 8px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                box-shadow: none;
                max-width: 100%;
            }

            img {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                display: block !important;
                visibility: visible !important;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-back">Kembali</a>
        <button id="btn-thermal" class="btn btn-print" onclick="triggerReceiptPrint()">🖨️ Cetak</button>
    </div>

    <div class="receipt">
        <!-- Header -->
        <div class="text-center">
            <div class="header-logo"><img src="/logo-sheza-1bit.png" alt="Sheza Laundry"
                    style="max-width: 100px; height: auto;"></div>
            <div class="header-title">SHEZA LAUNDRY SOLO</div>
            <div class="header-sub">
                Jl. Pucangsawit RT 03 RW 03, Kecamatan Jebres, Kota Surakarta<br>
                Tel: +62 8820-0933-4660
            </div>
        </div>

        <div class="border-dash"></div>

        <!-- Meta -->
        <div class="meta-row"><span class="meta-label">Order ID:</span><strong>{{ $order->order_number }}</strong></div>
        <div class="meta-row"><span
                class="meta-label">Tanggal:</span><span>{{ $order->created_at->translatedFormat('d F Y H:i') }}</span>
        </div>
        <div class="meta-row"><span class="meta-label">Kasir:</span><span>{{ $order->user?->name ?? 'Admin' }}</span>
        </div>
        <div class="meta-row"><span
                class="meta-label">Pelanggan:</span><span>{{ strtoupper($order->member?->name ?? 'TAMU') }}</span></div>

        <div class="border-dash"></div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="td-name">ITEM</th>
                    <th class="td-qty">QTY</th>
                    <th class="td-unit">HARGA</th>
                    <th class="td-amt">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td class="td-name">{{ $item->service_name }}</td>
                        <td class="td-qty">
                            @if ($item->service_type === 'kiloan')
                                {{ $item->weight ?? '?' }}kg
                            @else
                                {{ intval($item->quantity) }}x
                            @endif
                        </td>
                        <td class="td-unit">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="td-amt">
                            @if ($item->service_type === 'kiloan' && !$item->weight)
                                TBD
                            @else
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-dash"></div>

        <!-- Totals -->
        <div class="total-row total-final"><span>TOTAL:</span><span>Rp
                {{ number_format($order->total, 0, ',', '.') }}</span></div>

        @if ($order->payment_status === 'lunas')
            <div class="border-dash"></div>
            <div class="total-row"><span>DIBAYAR ({{ strtoupper($order->payment_method ?? 'tunai') }}):</span><span>Rp
                    {{ number_format($order->paid_amount ?? $order->total, 0, ',', '.') }}</span></div>
        @endif

        <div class="border-dash"></div>

        <!-- Footer -->
        <div class="border-dash"></div>
        <div class="meta-row">
            <span class="meta-label">Tgl Selesai:</span>
            <span>{{ $order->ready_at ? $order->ready_at->translatedFormat('j F Y') : $order->created_at->addDays($order->is_express ? 1 : 3)->translatedFormat('j F Y') }}</span>
        </div>
        <div class="border-dash"></div>
        <div class="footer-text">
            <strong>TERIMAKASIH</strong><br>
            Tidak Menerima Laundry Pakaian Dalam<br>
            Menerima Laundry Alat Gunung<br><br>
            IG : @sheza_laundrysolo<br>
            IG : @krabatadventure
        </div>
        <div class="border-dash"></div>
        <div class="footer-brand" id="print-timestamp"></div>
    </div>
</body>

@php
    $receiptData = [
        'order_number' => $order->order_number,
        'created_at' => $order->created_at->translatedFormat('d F Y H:i'),
        'finish_at' => $order->ready_at
            ? $order->ready_at->translatedFormat('d F Y')
            : $order->created_at->addDays($order->is_express ? 1 : 3)->translatedFormat('d F Y'),
        'cashier' => $order->user?->name ?? 'Admin',
        'customer' => strtoupper($order->member?->name ?? 'TAMU'),
        'items' => $order->items
            ->map(function ($item) {
                return [
                    'name' => $item->service_name,
                    'qty' =>
                        $item->service_type === 'kiloan'
                            ? ($item->weight
                                ? $item->weight . 'kg'
                                : '?kg')
                            : intval($item->quantity) . 'x',
                    'subtotal' =>
                        $item->service_type === 'kiloan' && !$item->weight
                            ? 'TBD'
                            : number_format($item->subtotal, 0, ',', '.'),
                ];
            })
            ->values()
            ->toArray(),
        'total' => number_format($order->total, 0, ',', '.'),
        'payment_status' => $order->payment_status,
        'payment_method' => $order->payment_method ?? 'tunai',
        'paid_amount' => number_format($order->paid_amount ?? $order->total, 0, ',', '.'),
    ];
@endphp
<script>
    // ── Data dari server ─────────────────────────────────────────────────
    const RECEIPT = @json($receiptData);

    // Tampilkan waktu cetak
    (function() {
        var now = new Date();
        var dd = String(now.getDate()).padStart(2, '0');
        var mm = String(now.getMonth() + 1).padStart(2, '0');
        var yyyy = now.getFullYear();
        var hh = String(now.getHours()).padStart(2, '0');
        var min = String(now.getMinutes()).padStart(2, '0');
        var tsId = now.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).replace('.', ':');
        document.getElementById('print-timestamp').textContent = 'Dicetak: ' + tsId;
    })();

    // ── ESC/POS builder (kertas 58mm = 32 karakter per baris) ────────────
    const W = 32;
    const ESC = '\x1B',
        GS = '\x1D',
        LF = '\n';

    function rpad(s, n) {
        s = String(s);
        return s.length >= n ? s.slice(0, n) : s + ' '.repeat(n - s.length);
    }

    function lpad(s, n) {
        s = String(s);
        return s.length >= n ? s.slice(0, n) : ' '.repeat(n - s.length) + s;
    }

    function cols2(a, b, total) {
        total = total || W;
        a = String(a);
        b = String(b);
        if (a.length + b.length + 1 > total) a = a.slice(0, total - b.length - 1);
        return a + ' '.repeat(total - a.length - b.length) + b;
    }

    function buildEscPos(d) {
        const SEP = '-'.repeat(W);
        let t = '';
        t += ESC + '@';
        t += ESC + 'a\x01';
        t += ESC + 'E\x01';
        t += 'SHEZA LAUNDRY SOLO' + LF;
        t += ESC + 'E\x00';
        t += 'Jl. Pucangsawit RT 03 RW 03' + LF;
        t += 'Kec. Jebres, Surakarta' + LF;
        t += 'Tel: +62 8820-0933-4660' + LF;
        t += ESC + 'a\x00';
        t += SEP + LF;
        t += cols2('Order ID :', d.order_number) + LF;
        t += cols2('Tanggal  :', d.created_at) + LF;
        t += cols2('Kasir    :', d.cashier) + LF;
        t += cols2('Pelanggan:', d.customer) + LF;
        t += SEP + LF;
        t += ESC + 'E\x01';
        t += rpad('ITEM', 17) + rpad('QTY', 7) + lpad('SUBTOTAL', 8) + LF;
        t += ESC + 'E\x00';
        for (var i = 0; i < d.items.length; i++) {
            var item = d.items[i];
            t += rpad(item.name, 17) + rpad(item.qty, 7) + lpad(item.subtotal, 8) + LF;
        }
        t += SEP + LF;
        t += ESC + 'E\x01';
        t += cols2('TOTAL    :', 'Rp ' + d.total) + LF;
        t += ESC + 'E\x00';
        if (d.payment_status === 'lunas') {
            t += SEP + LF;
            t += cols2('DIBAYAR (' + d.payment_method.toUpperCase() + '):', 'Rp ' + d.paid_amount) + LF;
        }
        t += SEP + LF;
        t += ESC + 'a\x01';
        t += ESC + 'E\x01';
        t += 'TERIMA KASIH!' + LF;
        t += ESC + 'E\x00';
        t += center('Tidak Menerima Laundry Pakaian Dalam') + '\n';
        t += center('Menerima Laundry Alat Gunung') + '\n';
        t += center('IG : @sheza_laundrysolo') + '\n';
        t += center('IG : @krabatadventure') + '\n';
        t += LF + LF + LF;
        t += GS + 'V\x41\x03';
        return t;
    }

    function bytesToStr(bytes) {
        var out = '';
        for (var i = 0; i < bytes.length; i++) {
            out += String.fromCharCode(bytes[i]);
        }
        return out;
    }

    function rasterizeToEscPos(img, maxWidth) {
        var ratio = img.width > maxWidth ? (maxWidth / img.width) : 1;
        var w = Math.max(1, Math.floor(img.width * ratio));
        var h = Math.max(1, Math.floor(img.height * ratio));

        var canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;

        var ctx = canvas.getContext('2d', {
            willReadFrequently: true
        });
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, w, h);
        ctx.drawImage(img, 0, 0, w, h);

        var imageData = ctx.getImageData(0, 0, w, h).data;
        var widthBytes = Math.ceil(w / 8);
        var bytes = new Uint8Array(widthBytes * h);

        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var idx = (y * w + x) * 4;
                var r = imageData[idx];
                var g = imageData[idx + 1];
                var b = imageData[idx + 2];
                var lum = 0.299 * r + 0.587 * g + 0.114 * b;
                var isBlack = lum < 170;
                if (isBlack) {
                    var offset = y * widthBytes + (x >> 3);
                    bytes[offset] |= (0x80 >> (x & 7));
                }
            }
        }

        var xL = widthBytes & 0xFF;
        var xH = (widthBytes >> 8) & 0xFF;
        var yL = h & 0xFF;
        var yH = (h >> 8) & 0xFF;

        return GS + 'v0' + String.fromCharCode(0, xL, xH, yL, yH) + bytesToStr(bytes);
    }

    function loadImage(src) {
        return new Promise(function(resolve, reject) {
            var img = new Image();
            img.onload = function() {
                resolve(img);
            };
            img.onerror = reject;
            img.src = src;
        });
    }

    async function buildEscPosWithLogo(d, logoSrc) {
        var logo = await loadImage(logoSrc);
        var body = buildEscPos(d);
        var header = ESC + '@' + ESC + 'a\x01';
        var logoRaster = rasterizeToEscPos(logo, 192);
        var spacing = LF + LF + ESC + 'a\x00';
        return header + logoRaster + spacing + body;
    }

    function buildPlainText(d) {
        var SEP = '--------------------------------';
        var W = 32;

        function rp(s, n) {
            s = String(s);
            return s.length >= n ? s.slice(0, n) : s + ' '.repeat(n - s.length);
        }

        function lp(s, n) {
            s = String(s);
            return s.length >= n ? s.slice(0, n) : ' '.repeat(n - s.length) + s;
        }

        function c2(a, b) {
            a = String(a);
            b = String(b);
            if (a.length + b.length + 1 > W) a = a.slice(0, W - b.length - 1);
            return a + ' '.repeat(W - a.length - b.length) + b;
        }

        function center(s) {
            s = String(s);
            var pad = Math.max(0, Math.floor((W - s.length) / 2));
            return ' '.repeat(pad) + s;
        }
        var t = '';
        t += center('SHEZA LAUNDRY SOLO') + '\n';
        t += center('Jl. Pucangsawit RT 03 RW 03') + '\n';
        t += center('Kec. Jebres, Surakarta') + '\n';
        t += center('Tel: +62 8820-0933-4660') + '\n';
        t += SEP + '\n';
        t += c2('Order ID :', d.order_number) + '\n';
        t += c2('Tanggal  :', d.created_at) + '\n';
        t += c2('Kasir    :', d.cashier) + '\n';
        t += c2('Pelanggan:', d.customer) + '\n';
        t += SEP + '\n';
        t += rp('ITEM', 17) + rp('QTY', 7) + lp('SUBTOTAL', 8) + '\n';
        t += SEP + '\n';
        for (var i = 0; i < d.items.length; i++) {
            var item = d.items[i];
            t += rp(item.name, 17) + rp(item.qty, 7) + lp(item.subtotal, 8) + '\n';
        }
        t += SEP + '\n';
        t += c2('TOTAL    :', 'Rp ' + d.total) + '\n';
        if (d.payment_status === 'lunas') {
            t += SEP + '\n';
            t += c2('DIBAYAR (' + d.payment_method.toUpperCase() + '):', 'Rp ' + d.paid_amount) + '\n';
        }
        t += SEP + '\n';
        t += c2('Tgl Selesai:', d.finish_at) + '\n';
        t += SEP + '\n';
        t += center('TERIMA KASIH') + '\n';
        t += center('Tidak Menerima Laundry Pakaian Dalam') + '\n';
        t += center('Menerima Laundry Alat Gunung') + '\n';
        t += center('IG : @sheza_laundrysolo') + '\n';
        t += center('IG : @krabatadventure') + '\n';
        t += SEP + '\n';
        var now = new Date();
        var tsCetak = String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' +
            now.getFullYear() + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(
                2, '0');
        t += center('Dicetak: ' + tsCetak);
        return t;
    }

    async function triggerReceiptPrint() {
        var btn = document.getElementById('btn-thermal');
        var origHTML = btn.innerHTML;

        btn.disabled = true;
        btn.textContent = 'Menyiapkan cetak...';

        var payload;
        try {
            payload = await buildEscPosWithLogo(RECEIPT, '/logo-sheza-1bit.png');
        } catch (e) {
            payload = buildPlainText(RECEIPT);
        }

        btn.textContent = 'Membuka RawBT...';

        var openedAt = Date.now();
        window.location.href = 'rawbt:' + encodeURIComponent(payload);

        // Jika setelah 2 detik halaman masih aktif, RawBT kemungkinan tidak terinstall
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = origHTML;

            if (!document.hidden && Date.now() - openedAt < 3500) {
                // window.print();
            }
        }, 2000);
    }
</script>

</html>
