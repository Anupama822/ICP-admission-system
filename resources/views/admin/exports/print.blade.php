<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('adminlte.title', 'Print') }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 28px;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Helvetica, Arial, sans-serif;
            color: #484848;
            font-size: 12px;
        }
        h1 {
            font-size: 18px;
            font-weight: 800;
            color: #9F0D1A;
            letter-spacing: -.3px;
            margin: 0;
        }
        .meta { color: #676767; font-size: 11px; margin-top: 4px; }
        .head {
            border-bottom: 2px solid #8D2229;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #676767;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 8px 10px;
            border-bottom: 2px solid #f0f1f3;
            background: rgba(141, 34, 41, .04);
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f2f2f2;
        }
        tbody tr:nth-child(even) td { background: #fbfbfb; }
        .empty { text-align: center; color: #676767; padding: 24px; }
        @media print {
            body { margin: 0; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body>
    <div class="head">
        <h1>{{ config('adminlte.title', 'Admission Management System') }}</h1>
        <div class="meta">{{ $title ?? 'Export' }} &middot; generated {{ now()->format('d M, Y H:i') }}</div>
    </div>

    <table>
        @forelse($data as $row)
            @if($loop->first)
                <thead>
                    <tr>
                        @foreach($row as $key => $value)
                            <th>{{ $key }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
            @endif
            <tr>
                @foreach($row as $value)
                    <td>{{ is_string($value) || is_numeric($value) ? $value : '' }}</td>
                @endforeach
            </tr>
            @if($loop->last)
                </tbody>
            @endif
        @empty
            <tbody><tr><td class="empty">Nothing to print.</td></tr></tbody>
        @endforelse
    </table>
</body>
</html>
