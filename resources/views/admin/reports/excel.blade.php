<table>
    <tr><td colspan="4" style="font-size:16px;font-weight:bold;text-align:center;">Laporan Pendapatan - {{ $periodLabel }}</td></tr>
    <tr><td colspan="4"></td></tr>
    <tr>
        <td style="font-weight:bold;background:#f0f0f0;">Total Pendapatan</td>
        <td style="font-weight:bold;text-align:right;">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
        <td style="font-weight:bold;background:#f0f0f0;">Total Pesanan</td>
        <td style="font-weight:bold;text-align:right;">{{ $orderCount }}</td>
    </tr>
    <tr>
        <td style="font-weight:bold;background:#f0f0f0;">Rata-rata per Pesanan</td>
        <td style="font-weight:bold;text-align:right;">Rp {{ number_format($avgPerOrder, 0, ',', '.') }}</td>
        <td colspan="2"></td>
    </tr>
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="4" style="font-weight:bold;font-size:13px;">Menu Terlaris</td></tr>
    <tr>
        <td style="font-weight:bold;background:#FF8C42;color:white;">Nama Menu</td>
        <td style="font-weight:bold;background:#FF8C42;color:white;">Kategori</td>
        <td style="font-weight:bold;background:#FF8C42;color:white;text-align:center;">Terjual</td>
        <td style="font-weight:bold;background:#FF8C42;color:white;text-align:right;">Pendapatan</td>
    </tr>
    @forelse ($topItems as $item)
        <tr>
            <td>{{ $item->menuItem?->name ?? 'Unknown' }}</td>
            <td>{{ $item->menuItem?->category?->name ?? '-' }}</td>
            <td style="text-align:center;">{{ $item->total_qty }}</td>
            <td style="text-align:right;font-weight:bold;">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" style="color:#999;">Belum ada data.</td></tr>
    @endforelse
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="4" style="font-weight:bold;font-size:13px;">Breakdown Pembayaran</td></tr>
    <tr>
        <td style="font-weight:bold;background:#FF8C42;color:white;">Metode</td>
        <td style="font-weight:bold;background:#FF8C42;color:white;text-align:center;">Transaksi</td>
        <td colspan="2" style="font-weight:bold;background:#FF8C42;color:white;text-align:right;">Total</td>
    </tr>
    @forelse ($paymentBreakdown as $pb)
        <tr>
            <td>{{ $paymentLabels[$pb->payment_method] ?? ucfirst($pb->payment_method) }}</td>
            <td style="text-align:center;">{{ $pb->count }}x</td>
            <td colspan="2" style="text-align:right;font-weight:bold;">Rp {{ number_format($pb->total, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" style="color:#999;">Belum ada data.</td></tr>
    @endforelse
</table>
