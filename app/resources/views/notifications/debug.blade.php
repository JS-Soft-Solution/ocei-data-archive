@extends('layouts.app')

@section('title', 'Notification Debug')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Notification System Debug Information</h5>
        </div>
        <div class="card-body">
            <h6>1. Check if notifications table exists:</h6>
            <pre>
    @php
        try {
            $tableExists = Schema::hasTable('notifications');
            echo $tableExists ? "✅ notifications table EXISTS" : "❌ notifications table DOES NOT EXIST - Run: php artisan migrate";
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    @endphp
                </pre>

            <h6>2. Check notification count for current user:</h6>
            <pre>
    @php
        try {
            if (auth()->check()) {
                $totalNotifications = \App\Models\Notification::where('user_id', auth()->id())->count();
                $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                echo "Total Notifications: {$totalNotifications}\n";
                echo "Unread Notifications: {$unreadNotifications}\n";
                echo "User ID: " . auth()->id() . "\n";
                echo "User Role: " . auth()->user()->admin_type;
            } else {
                echo "Not authenticated";
            }
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    @endphp
                </pre>

            <h6>3. Sample notifications:</h6>
            <pre>
    @php
        try {
            if (auth()->check()) {
                $notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->take(3)->get();
                if ($notifications->count() > 0) {
                    foreach ($notifications as $notif) {
                        echo "ID: {$notif->id}\n";
                        echo "Type: {$notif->type}\n";
                        echo "Title: {$notif->title}\n";
                        echo "Read: " . ($notif->read_at ? 'Yes' : 'No') . "\n";
                        echo "Data: " . json_encode($notif->data) . "\n";
                        echo "---\n";
                    }
                } else {
                    echo "No notifications found for this user\n";
                }
            }
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    @endphp
                </pre>

            <h6>4. Check JavaScript file loading:</h6>
            <p>Open browser console (F12) and type: <code>typeof markAllAsRead</code></p>
            <p>Expected: "function" | If "undefined" → JavaScript file not loading</p>

            <h6>5. Check CSRF token:</h6>
            <pre>@csrf</pre>
            <p>Meta tag exists: <span id="csrf-check">Checking...</span></p>
            <script>
                document.getElementById('csrf-check').textContent =
                    document.querySelector('meta[name="csrf-token"]') ? '✅ Yes' : '❌ No';
            </script>

            <h6>6. Test mark all as read:</h6>
            <button class="btn btn-primary" onclick="testMarkAllAsRead()">Test Mark All As Read</button>
            <div id="test-result" class="mt-2"></div>

            <script>
                function testMarkAllAsRead() {
                    const result = document.getElementById('test-result');
                    result.innerHTML = 'Testing...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        result.innerHTML = '❌ CSRF token not found!';
                        return;
                    }

                    fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            result.innerHTML = '✅ Success: ' + JSON.stringify(data);
                        })
                        .catch(error => {
                            result.innerHTML = '❌ Error: ' + error.message;
                            console.error(error);
                        });
                }
            </script>
        </div>
    </div>
@endsection