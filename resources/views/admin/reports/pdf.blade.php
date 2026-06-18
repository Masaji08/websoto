<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan - {{ $periodLabel }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; color: #FF8C42; margin-bottom: 4px; }
        .subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
        .summary { margin-bottom: 24px; }
        .summary table { width: 100%; }
        .summary td { padding: 6px 12px; border: 1px solid #ddd; font-size: 12px; }
        .summary .label { background: #f5f5f5; font-weight: 600; }
        .summary .value { font-weight: 700; text-align: right; }
        h2 { font-size: 14px; color: #333; margin-top: 24px; margin-bottom: 8px; border-bottom: 2px solid #FF8C42; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data th { background: #FF8C42; color: white; padding: 6px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        table.data td { padding: 5px 10px; border-bottom: 1px solid #eee; }
        table.data tr:nth-child(even) td { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #999; font-size: 9px; border-top: 1px solid #eee; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>Laporan Pendapatan</h1>
    <p class="subtitle">Periode: {{ $periodLabel }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Pendapatan</td>
                <td class="value">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Pesanan</td>
                <td class="value">{{ $orderCount }}</td>
            </tr>
            <tr>
                <td class="label">Rata-rata per Pesanan</td>
                <td class="value">Rp {{ number_format($avgPerOrder, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <h2>Menu Terlaris</h2>
    @if (count($topItems) > 0)
        <table class="data">
            <thead>
                <tr>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th class="text-center">Terjual</th>
                    <th class="text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topItems as $item)
                    <tr>
                        <td>{{ $item->menuItem?->name ?? 'Unknown' }}</td>
                        <td>{{ $item->menuItem?->category?->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->total_qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #999;">Belum ada data.</p>
    @endif

    <h2>Breakdown Pembayaran</h2>
    @if (count($paymentBreakdown) > 0)
        <table class="data">
            <thead>
                <tr>
                    <th>Metode</th>
                    <th class="text-center">Transaksi</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentBreakdown as $pb)
                    <tr>
                        <td>{{ $paymentLabels[$pb->payment_method] ?? ucfirst($pb->payment_method) }}</td>
                        <td class="text-center">{{ $pb->count }}x</td>
                        <td class="text-right">Rp {{ number_format($pb->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #999;">Belum ada data.</p>
    @endif

    <div class="footer">{{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }} — Laporan otomatis generated {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
