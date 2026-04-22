<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Revenus</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { text-align: center; color: #333; }
    </style>
</head>
<body>
    <h2>Rapport des Revenus</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Description</th>
                <th>Montant (Ar)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenues as $revenue)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($revenue->revenue_date)->format('d/m/Y') }}</td>
                    <td>{{ $revenue->source }}</td>
                    <td>{{ $revenue->description }}</td>
                    <td>{{ number_format($revenue->amount, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
