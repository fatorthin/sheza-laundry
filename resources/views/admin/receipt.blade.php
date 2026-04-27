<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Struk #{{ $order->order_number }}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #e8e0d8; display: flex; flex-direction: column; align-items: center; min-height: 100vh; padding: 16px; font-family: 'Courier Prime', 'Courier New', monospace; }
    .toolbar { display: flex; gap: 12px; margin-bottom: 16px; }
    .btn { padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; }
    .btn-print { background: #1a1a1a; color: white; }
    .btn-back { background: white; color: #333; border: 1px solid #ccc; }
    .receipt { width: 100%; max-width: 384px; background: #fff; padding: 24px 20px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .border-dash { border-top: 1px dashed #999; margin: 10px 0; }
    .header-logo { font-size: 24px; margin-bottom: 4px; }
    .header-title { font-size: 16px; font-weight: 700; letter-spacing: 2px; margin-bottom: 6px; }
    .header-sub { font-size: 11px; line-height: 1.6; color: #555; }
    .meta-row { display: flex; justify-content: space-between; font-size: 12px; margin: 3px 0; }
    .meta-label { color: #555; }
    .items-table { width: 100%; font-size: 12px; margin: 4px 0; }
    .items-table th { text-align: left; font-weight: 700; padding: 3px 0; border-bottom: 1px dashed #999; }
    .items-table td { padding: 4px 0; vertical-align: top; }
    .items-table .td-name { width: 50%; }
    .items-table .td-qty { width: 12%; text-align: center; }
    .items-table .td-unit { width: 20%; text-align: right; }
    .items-table .td-amt { width: 18%; text-align: right; }
    .total-row { display: flex; justify-content: space-between; font-size: 12px; margin: 3px 0; }
    .total-final { font-size: 14px; font-weight: 700; }
    .footer-text { text-align: center; font-size: 11px; color: #555; margin-top: 6px; line-height: 1.6; }
    .footer-brand { font-size: 10px; color: #aaa; margin-top: 8px; }
    @media print {
      body { background: white; padding: 0; }
      .toolbar { display: none; }
      .receipt { box-shadow: none; max-width: 100%; }
    }
  </style>
</head>
<body>
  <div class="toolbar no-print">
    <button class="btn btn-back" onclick="window.history.back()">← Kembali</button>
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
  </div>

  <div class="receipt">
    <!-- Header -->
    <div class="text-center">
      <div class="header-logo">🧺</div>
      <div class="header-title">SHEZA LAUNDRY</div>
      <div class="header-sub">
        Jl. Contoh No. 123, Jakarta<br>
        Tel: +62 812-3456-7890
      </div>
    </div>

    <div class="border-dash"></div>

    <!-- Meta -->
    <div class="meta-row"><span class="meta-label">Order ID:</span><strong>{{ $order->order_number }}</strong></div>
    <div class="meta-row"><span class="meta-label">Tanggal:</span><span>{{ $order->created_at->format('Y-m-d H:i') }}</span></div>
    <div class="meta-row"><span class="meta-label">Kasir:</span><span>{{ $order->user?->name ?? 'Admin' }}</span></div>
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
        @foreach($order->items as $item)
        <tr>
          <td class="td-name">{{ $item->service_name }}</td>
          <td class="td-qty">
            @if($item->service_type === 'kiloan')
              {{ $item->weight ?? '?' }}kg
            @else
              {{ intval($item->quantity) }}x
            @endif
          </td>
          <td class="td-unit">{{ number_format($item->price, 0, ',', '.') }}</td>
          <td class="td-amt">
            @if($item->service_type === 'kiloan' && !$item->weight)
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
    <div class="total-row"><span>Subtotal:</span><span>{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>PPN (11%):</span><span>{{ number_format($order->tax, 0, ',', '.') }}</span></div>
    <div class="border-dash"></div>
    <div class="total-row total-final"><span>TOTAL:</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>

    @if($order->payment_status === 'lunas')
    <div class="border-dash"></div>
    <div class="total-row"><span>DIBAYAR ({{ strtoupper($order->payment_method ?? 'tunai') }}):</span><span>Rp {{ number_format($order->paid_amount ?? $order->total, 0, ',', '.') }}</span></div>
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
</html>