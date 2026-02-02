<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>M-Pesa Sandbox Test</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; margin: 2rem; color: #111827; }
        .card { max-width: 720px; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 12px; }
        .muted { color: #6b7280; font-size: 0.95rem; }
        .btn { background: #0f766e; color: #fff; border: 0; padding: 0.6rem 1rem; border-radius: 8px; cursor: pointer; }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }
        pre { background: #111827; color: #f9fafb; padding: 1rem; border-radius: 10px; overflow: auto; }
        .ok { color: #065f46; }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="card">
        <h1>M-Pesa Sandbox Test</h1>
        <p class="muted">Generate an OAuth access token using your sandbox credentials.</p>

        <form method="POST" action="{{ url('/mpesa/test/token') }}">
            @csrf
            <button class="btn" type="submit">Generate Access Token</button>
        </form>

        <hr>

        <h2>B2C Payout</h2>
        <p class="muted">Send money to a phone number using your B2C credentials.</p>
        <form method="POST" action="{{ url('/mpesa/test/b2c') }}">
            @csrf
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <input name="phone" placeholder="2547XXXXXXXX" style="flex: 1; padding: 0.6rem; border: 1px solid #e5e7eb; border-radius: 8px;">
                <input name="amount" type="number" min="1" step="1" placeholder="Amount" style="width: 140px; padding: 0.6rem; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <input name="remarks" placeholder="Remarks (optional)" style="width: 100%; padding: 0.6rem; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            <button class="btn" type="submit">Send B2C Payment</button>
        </form>

        @if (session('mpesa_result'))
            @php($result = session('mpesa_result'))
            <h2>Token Result</h2>
            <p class="{{ $result['ok'] ? 'ok' : 'error' }}">
                {{ $result['ok'] ? 'Success' : 'Failed' }}
                @if (!empty($result['status']))
                    (HTTP {{ $result['status'] }})
                @endif
            </p>
            <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
        @endif

        @if (session('mpesa_b2c_result'))
            @php($result = session('mpesa_b2c_result'))
            <h2>B2C Result</h2>
            <p class="{{ $result['ok'] ? 'ok' : 'error' }}">
                {{ $result['ok'] ? 'Success' : 'Failed' }}
                @if (!empty($result['status']))
                    (HTTP {{ $result['status'] }})
                @endif
            </p>
            <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
        @endif

        <hr>

        <h2>Latest Callback</h2>
        <div style="margin-bottom: 1rem;">
            <button class="btn" type="button" id="mpesa-callback-check">Check Latest Callback</button>
        </div>
        <div id="mpesa-callback-meta" class="muted" style="margin-bottom: 0.5rem;">
            @if (!empty($latestCallback))
                Type: {{ $latestCallback->type ?? 'unknown' }} | Received: {{ $latestCallback->created_at?->toDateTimeString() ?? '-' }}
            @endif
        </div>
        <pre id="mpesa-callback-payload">{{ !empty($latestCallback) ? json_encode($latestCallback->payload ?? [], JSON_PRETTY_PRINT) : 'No callback received yet.' }}</pre>
    </div>
    <script>
        (function () {
            const button = document.getElementById('mpesa-callback-check');
            const meta = document.getElementById('mpesa-callback-meta');
            const payload = document.getElementById('mpesa-callback-payload');

            if (!button || !meta || !payload) {
                return;
            }

            button.addEventListener('click', async () => {
                meta.textContent = 'Checking...';
                try {
                    const response = await fetch('/api/mpesa/callback/latest');
                    const data = await response.json();

                    if (!data.ok || !data.data) {
                        meta.textContent = 'No callback received yet.';
                        payload.textContent = 'No callback received yet.';
                        return;
                    }

                    meta.textContent = 'Type: ' + (data.data.type || 'unknown') + ' | Received: ' + (data.data.received_at || '-');
                    payload.textContent = JSON.stringify(data.data.payload || {}, null, 2);
                } catch (error) {
                    meta.textContent = 'Failed to fetch latest callback.';
                    payload.textContent = '';
                }
            });
        })();
    </script>
</body>
</html>
