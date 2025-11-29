<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $reference->title }} - KMKB</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.15;
            color: #111827;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        .tag {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 9999px;
            font-size: 9px;
            margin-right: 4px;
            margin-bottom: 2px;
            border: 1px solid #d1d5db;
        }
        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 9999px;
            font-size: 9px;
            margin-right: 4px;
            margin-bottom: 2px;
        }
        .status-draft { background-color: #fef9c3; color: #854d0e; }
        .status-published { background-color: #dcfce7; color: #166534; }
        .status-archived { background-color: #e5e7eb; color: #374151; }
        .content p {
            margin: 0 0 8px 0;
        }
        .content h1, .content h2, .content h3, .content h4 {
            margin-top: 16px;
            margin-bottom: 8px;
        }
        .content ul, .content ol {
            margin: 8px 0 8px 18px;
        }
        .content li {
            margin-bottom: 4px;
        }
        .content code {
            font-family: DejaVu Sans Mono, monospace;
            background-color: #f3f4f6;
            padding: 0 2px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <h1>{{ $reference->title }}</h1>
    <div class="meta">
        Ditulis oleh {{ $reference->author->name ?? '—' }}
        @if($reference->published_at)
            · {{ $reference->published_at->translatedFormat('d M Y H:i') }}
        @endif
    </div>

    <div class="content">
        @php
            $content = $reference->content ?? '';
            $content = preg_replace('/(?<!\n)\n(?!\n)/', "  \n", $content);
            echo \Illuminate\Support\Str::markdown(
                $content,
                [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]
            );
        @endphp
    </div>
</body>
</html>


