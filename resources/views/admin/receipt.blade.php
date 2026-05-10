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
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-back">← Kembali</a>
        <button id="btn-thermal" class="btn btn-print" onclick="triggerReceiptPrint()">🖨️ Cetak</button>
    </div>

    <div class="receipt">
        <!-- Header -->
        <div class="text-center">
            <div class="header-logo"><img src="/logo-sheza.png" alt="Sheza Laundry" style="max-width: 100px; height: auto;"></div>
            <div class="header-title">SHEZA LAUNDRY SOLO</div>
            <div class="header-sub">
                Jl. Pucangsawit RT 03 RW 03, Kecamatan Jebres, Kota Surakarta<br>
                Tel: +62 8820-0933-4660
            </div>
        </div>

        <div class="border-dash"></div>

        <!-- Meta -->
        <div class="meta-row"><span class="meta-label">Order ID:</span><strong>{{ $order->order_number }}</strong></div>
        <div class="meta-row"><span class="meta-label">Tanggal:</span><span>{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
        <div class="meta-row"><span class="meta-label">Kasir:</span><span>{{ $order->user?->name ?? 'Admin' }}</span>
        </div>
        <div class="meta-row"><span class="meta-label">Pelanggan:</span><span>{{ strtoupper($order->member?->name ?? 'TAMU') }}</span></div>

        <div class="border-dash"></div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="td-name">ITEM</th>
                    <th class="td-qty">QTY</th>
                    <th class="td-unit">HARGA</th>
                    <th class="td-amt">TOTAL</th>
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
        <div class="total-row"><span>Subtotal:</span><span>{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="border-dash"></div>
        <div class="total-row total-final"><span>TOTAL:</span><span>Rp
                {{ number_format($order->total, 0, ',', '.') }}</span></div>

        @if ($order->payment_status === 'lunas')
            <div class="border-dash"></div>
            <div class="total-row"><span>DIBAYAR ({{ strtoupper($order->payment_method ?? 'tunai') }}):</span><span>Rp
                    {{ number_format($order->paid_amount ?? $order->total, 0, ',', '.') }}</span></div>
        @endif

        <div class="border-dash"></div>

        <!-- Footer -->
        <div class="footer-text">
            <strong>TERIMA KASIH!</strong><br>
            Simpan struk ini sebagai bukti pengambilan.
        </div>
        <div class="footer-brand">ShezaLaundry System · shezalaundry.com</div>
    </div>
</body>

@php
    $receiptData = [
        'order_number' => $order->order_number,
        'created_at' => $order->created_at->format('d/m/Y H:i'),
        'cashier' => $order->user?->name ?? 'Admin',
        'customer' => strtoupper($order->member?->name ?? 'TAMU'),
        'items' => $order->items
            ->map(function ($item) {
                return [
                    'name' => $item->service_name,
                    'qty' => $item->service_type === 'kiloan' ? ($item->weight ? $item->weight . 'kg' : '?kg') : intval($item->quantity) . 'x',
                    'subtotal' => $item->service_type === 'kiloan' && !$item->weight ? 'TBD' : number_format($item->subtotal, 0, ',', '.'),
                ];
            })
            ->values()
            ->toArray(),
        'subtotal' => number_format($order->subtotal, 0, ',', '.'),
        'total' => number_format($order->total, 0, ',', '.'),
        'payment_status' => $order->payment_status,
        'payment_method' => $order->payment_method ?? 'tunai',
        'paid_amount' => number_format($order->paid_amount ?? $order->total, 0, ',', '.'),
    ];
@endphp
<script>
    // ── Data dari server ─────────────────────────────────────────────────
    const RECEIPT = @json($receiptData);

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
        t += rpad('ITEM', 17) + rpad('QTY', 7) + lpad('TOTAL', 8) + LF;
        t += ESC + 'E\x00';
        for (var i = 0; i < d.items.length; i++) {
            var item = d.items[i];
            t += rpad(item.name, 17) + rpad(item.qty, 7) + lpad(item.subtotal, 8) + LF;
        }
        t += SEP + LF;
        t += cols2('Subtotal :', 'Rp ' + d.subtotal) + LF;
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
        t += 'Simpan struk ini sebagai' + LF;
        t += 'bukti pengambilan.' + LF;
        t += LF + LF + LF;
        t += GS + 'V\x41\x03';
        return t;
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
        t += rp('ITEM', 17) + rp('QTY', 7) + lp('TOTAL', 8) + '\n';
        t += SEP + '\n';
        for (var i = 0; i < d.items.length; i++) {
            var item = d.items[i];
            t += rp(item.name, 17) + rp(item.qty, 7) + lp(item.subtotal, 8) + '\n';
        }
        t += SEP + '\n';
        t += c2('Subtotal :', 'Rp ' + d.subtotal) + '\n';
        t += SEP + '\n';
        t += c2('TOTAL    :', 'Rp ' + d.total) + '\n';
        if (d.payment_status === 'lunas') {
            t += SEP + '\n';
            t += c2('DIBAYAR (' + d.payment_method.toUpperCase() + '):', 'Rp ' + d.paid_amount) + '\n';
        }
        t += SEP + '\n';
        t += center('TERIMA KASIH!') + '\n';
        t += center('Simpan struk sebagai') + '\n';
        t += center('bukti pengambilan.') + '\n';
        t += '\n\n\n';
        return t;
    }

    function triggerReceiptPrint() {
        var btn = document.getElementById('btn-thermal');
        var origHTML = btn.innerHTML;

        // Kirim plain text ke RawBT — format yang benar dan langsung dicetak printer
        var plainText = buildPlainText(RECEIPT);

        btn.disabled = true;
        btn.textContent = 'Membuka RawBT...';

        var openedAt = Date.now();
        window.location.href = 'rawbt:' + encodeURIComponent(plainText);

        // Jika setelah 2 detik halaman masih aktif, RawBT kemungkinan tidak terinstall
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = origHTML;

            if (!document.hidden && Date.now() - openedAt < 3500) {
                var goInstall = confirm(
                    'Aplikasi RawBT tidak terdeteksi di perangkat ini.\n\n' +
                    'RawBT diperlukan agar bisa cetak langsung ke printer thermal Bluetooth.\n\n' +
                    'Buka Play Store untuk install RawBT?\n' +
                    '(Tekan Batal untuk cetak via dialog browser biasa)'
                );
                if (goInstall) {
                    window.open('https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter', '_blank');
                } else {
                    window.print();
                }
            }
        }, 2000);
    }
</script>

</html>
