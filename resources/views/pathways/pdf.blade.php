<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clinical Pathway PDF</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        h2 { font-size: 14px; margin: 20px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px 8px; vertical-align: top; }
        th { background: #f2f2f2; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta th, .meta td { border: none; padding: 4px 6px; }
        .right { text-align: right; }
        .small { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <h1>Clinical Pathway</h1>

    <h2>Pathway Information</h2>
    <table class="meta">
        <tr>
            <th style="width: 160px">Name</th>
            <td>{{ $pathway->name }}</td>
        </tr>
        <tr>
            <th>Diagnosis Code</th>
            <td>{{ $pathway->diagnosis_code }}</td>
        </tr>
        <tr>
            <th>Version</th>
            <td>{{ $pathway->version }}</td>
        </tr>
        <tr>
            <th>Effective Date</th>
            <td>{{ optional($pathway->effective_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($pathway->status) }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>{{ optional($pathway->creator)->name }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $pathway->description }}</td>
        </tr>
    </table>

    <h2>Pathway Steps</h2>
    @if($pathway->steps->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Day</th>
                    <th style="width: 18%">Activity</th>
                    <th style="width: 34%">Description</th>
                    <th style="width: 20%">Criteria</th>
                    <th style="width: 10%" class="right">Standard Cost</th>
                    <th style="width: 10%" class="right">Full Standard Cost</th>
                </tr>
            </thead>
            <tbody>
            @php
                $total = 0;
            @endphp
            @foreach($pathway->steps->sortBy('step_order') as $step)
                @php
                    $lineTotal = (int)($step->estimated_cost ?? 0) * (int)($step->quantity ?? 1);
                    $total += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $step->step_order }}</td>
                    <td>{{ $step->service_code }}</td>
                    <td>{{ $step->description }}</td>
                    <td>{{ $step->criteria }}</td>
                    <td class="right">Rp{{ number_format($step->estimated_cost, 0, ',', '.') }}</td>
                    <td class="right">Rp{{ number_format($lineTotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="right"><strong>Total Standard Cost:</strong></td>
                    <td class="right"><strong>Rp{{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="small">No steps defined for this pathway yet.</p>
    @endif

    <p class="small" style="margin-top: 16px">Generated on {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
