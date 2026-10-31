// Notification JavaScript Handlers

/**
 * Mark a single notification as read and navigate to its URL
 */
function markNotificationRead(notificationId, url) {
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
}

/**
 * Mark all notifications as read
 */
function markAllAsRead() {
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
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

/**
 * Auto-refresh notification count every 30 seconds
 */
function startNotificationAutoRefresh() {
    setInterval(() => {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const indicator = document.querySelector('.notification-indicator-number');
                
                if (data.count > 0) {
                    if (indicator) {
                        indicator.textContent = data.count;
                    } else {
                        // Create indicator if it doesn't exist
                        const bellIcon = document.querySelector('.notification-indicator .fa-bell');
                        if (bellIcon && bellIcon.parentElement) {
                            const newIndicator = document.createElement('span');
                            newIndicator.className = 'notification-indicator-number';
                            newIndicator.textContent = data.count;
                            bellIcon.parentElement.appendChild(newIndicator);
                        }
                    }
                } else {
                    // Remove indicator if count is 0
                    if (indicator) {
                        indicator.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    }, 30000); // 30 seconds
}

// Start auto-refresh when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startNotificationAutoRefresh);
} else {
    startNotificationAutoRefresh();
}
