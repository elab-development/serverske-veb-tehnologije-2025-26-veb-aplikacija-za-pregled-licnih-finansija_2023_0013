<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Izvestaj o transakcijama</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0F172A; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .header { border-bottom: 2px solid #2563EB; padding-bottom: 8px; margin-bottom: 16px; }
        .meta { color: #64748B; font-size: 10px; }
        .logo { font-weight: 700; color: #2563EB; font-size: 14px; }
        h2 { font-size: 14px; margin-top: 18px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { text-align: left; background: #F7F8FA; padding: 6px 8px; border-bottom: 1px solid #E5E7EB; }
        td { padding: 5px 8px; border-bottom: 1px solid #E5E7EB; }
        .right { text-align: right; }
        .income { color: #16A34A; }
        .expense { color: #DC2626; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Licne finansije</div>
        <h1>Izvestaj o transakcijama</h1>
        <div class="meta">
            Period: {{ \Carbon\Carbon::parse($from)->format('d.m.Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('d.m.Y') }}<br>
            Korisnik: {{ $user->name }} ({{ $user->email }})<br>
            Generisano: {{ now()->format('d.m.Y H:i') }}
        </div>
    </div>

    <h2>Rezime po kategorijama</h2>
    <table>
        <thead>
            <tr>
                <th>Kategorija</th>
                <th>Tip</th>
                <th class="right">Br. tx</th>
                <th class="right">Ukupno (RSD)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summary as $row)
                <tr>
                    <td>{{ $row['category'] }}</td>
                    <td class="{{ $row['type'] === 'income' ? 'income' : 'expense' }}">
                        {{ $row['type'] === 'income' ? 'Prihod' : 'Rashod' }}
                    </td>
                    <td class="right">{{ $row['count'] }}</td>
                    <td class="right">{{ number_format($row['total'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Nema podataka.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Sve transakcije u periodu</h2>
    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Kategorija</th>
                <th>Tip</th>
                <th class="right">Iznos</th>
                <th>Napomena</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $tx)
                <tr>
                    <td>{{ $tx->transaction_date->format('d.m.Y') }}</td>
                    <td>{{ $tx->category->name }}</td>
                    <td class="{{ $tx->type === 'income' ? 'income' : 'expense' }}">
                        {{ $tx->type === 'income' ? 'Prihod' : 'Rashod' }}
                    </td>
                    <td class="right">{{ number_format($tx->amount, 2, ',', '.') }}</td>
                    <td>{{ $tx->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
