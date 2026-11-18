// Notification JavaScript Handlers with Cross-Tab Synchronization
(function () {
    'use strict';

    const STORAGE_KEY_LAST_POLL = 'ocei_notification_last_poll';
    const STORAGE_KEY_COUNT = 'ocei_notification_count';
    const POLL_INTERVAL_MS = 30000; // 30 seconds

    let notificationInterval = null;
    let isFetchingNotifications = false;

    // Cross-tab broadcast channel
    const broadcastChannel = typeof window.BroadcastChannel !== 'undefined'
        ? new BroadcastChannel('ocei_notifications_channel')
        : null;

    /**
     * Update the notification badge in the UI
     */
    function updateNotificationBadge(count) {
        const numericCount = parseInt(count, 10) || 0;
        const indicator = document.querySelector('.notification-indicator-number');
        const bellIcon = document.querySelector('.notification-indicator .fa-bell');

        if (numericCount > 0) {
            if (indicator) {
                indicator.textContent = numericCount;
            } else if (bellIcon && bellIcon.parentElement) {
                const newIndicator = document.createElement('span');
                newIndicator.className = 'notification-indicator-number';
                newIndicator.textContent = numericCount;
                bellIcon.parentElement.appendChild(newIndicator);
            }
        } else {
            if (indicator) {
                indicator.remove();
            }
        }
    }

    /**
     * Broadcast notification count update to all other open tabs
     */
    function broadcastCount(count) {
        try {
            localStorage.setItem(STORAGE_KEY_COUNT, count.toString());
        } catch (e) {
            // LocalStorage might fail in rare private browsing edge cases
        }

        if (broadcastChannel) {
            try {
                broadcastChannel.postMessage({ type: 'NOTIFICATION_COUNT_SYNC', count: count });
            } catch (e) {
                // Ignore channel errors
            }
        }
    }

    /**
     * Listen for updates from other open tabs
     */
    if (broadcastChannel) {
        broadcastChannel.onmessage = function (event) {
            if (event.data && event.data.type === 'NOTIFICATION_COUNT_SYNC') {
                updateNotificationBadge(event.data.count);
            }
        };
    }

    // Fallback cross-tab sync using storage event
    window.addEventListener('storage', function (event) {
        if (event.key === STORAGE_KEY_COUNT && event.newValue !== null) {
            updateNotificationBadge(event.newValue);
        }
    });

    /**
     * Fetch unread notification count (coordinated across tabs)
     */
    function checkNotifications() {
        // Only run if notification indicator/bell exists (i.e., user is authenticated)
        const bellIcon = document.querySelector('.notification-indicator');
        if (!bellIcon) {
            return; // Never poll on guest or login pages
        }

        const now = Date.now();
        let lastPoll = 0;
        try {
            lastPoll = parseInt(localStorage.getItem(STORAGE_KEY_LAST_POLL) || '0', 10);
        } catch (e) {
            lastPoll = 0;
        }

        // Check if another tab recently polled within the interval
        if (now - lastPoll < POLL_INTERVAL_MS && lastPoll > 0) {
            // Another tab already checked recently; sync from stored count without network call
            try {
                const cachedCount = localStorage.getItem(STORAGE_KEY_COUNT);
                if (cachedCount !== null) {
                    updateNotificationBadge(cachedCount);
                }
            } catch (e) {}
            return;
        }

        // Avoid concurrent requests from the same tab
        if (isFetchingNotifications) {
            return;
        }

        // Claim leadership for this polling cycle across all tabs
        try {
            localStorage.setItem(STORAGE_KEY_LAST_POLL, now.toString());
        } catch (e) {}

        isFetchingNotifications = true;

        fetch('/notifications/unread-count', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401 || response.status === 403 || response.redirected) {
                        // User logged out or session expired; stop polling in this tab
                        if (notificationInterval) {
                            clearInterval(notificationInterval);
                            notificationInterval = null;
                        }
                    }
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const count = data.count || 0;
                updateNotificationBadge(count);
                broadcastCount(count);
            })
            .catch(error => {
                // Silently ignore expected polling failures when switching pages or logging out
            })
            .finally(() => {
                isFetchingNotifications = false;
            });
    }

    /**
     * Start the notification manager
     */
    function startNotificationAutoRefresh() {
        const bellIcon = document.querySelector('.notification-indicator');
        if (!bellIcon) {
            return;
        }

        // Sync initial state from shared localStorage if available
        try {
            const cachedCount = localStorage.getItem(STORAGE_KEY_COUNT);
            if (cachedCount !== null) {
                updateNotificationBadge(cachedCount);
            }
        } catch (e) {}

        // Schedule periodic check
        if (notificationInterval) {
            clearInterval(notificationInterval);
        }

        // Check every 10 seconds whether it's time for ANY tab to poll (only 1 tab will execute the fetch)
        notificationInterval = setInterval(checkNotifications, 10000);

        // Run one check on initial load (will skip network if another tab polled recently)
        checkNotifications();
    }

    // Refresh when user switches back to this tab if count changed in another tab
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            try {
                const cachedCount = localStorage.getItem(STORAGE_KEY_COUNT);
                if (cachedCount !== null) {
                    updateNotificationBadge(cachedCount);
                }
            } catch (e) {}
            checkNotifications();
        }
    });

    /**
     * Mark a single notification as read and navigate to its URL
     */
    window.markNotificationRead = function (notificationId, url) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            console.error('CSRF token not found');
            if (url && url !== '#') {
                window.location.href = url;
            }
            return;
        }

        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                // Decrement count locally and broadcast to all tabs
                try {
                    const current = parseInt(localStorage.getItem(STORAGE_KEY_COUNT) || '1', 10);
                    const newCount = Math.max(0, current - 1);
                    updateNotificationBadge(newCount);
                    broadcastCount(newCount);
                } catch (e) {}

                if (data.success && url && url !== '#') {
                    window.location.href = url;
                } else {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
    };

    /**
     * Mark all notifications as read
     */
    window.markAllAsRead = function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            console.error('CSRF token not found');
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
                if (data.success) {
                    // Update badge to 0 and broadcast to all tabs immediately
                    updateNotificationBadge(0);
                    broadcastCount(0);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startNotificationAutoRefresh);
    } else {
        startNotificationAutoRefresh();
    }
})();
