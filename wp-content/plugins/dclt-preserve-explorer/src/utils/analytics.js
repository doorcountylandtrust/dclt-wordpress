// src/utils/analytics.js
export const trackEvent = (eventName, eventData = {}, preserveName = '') => {
  if (window.location.hostname === 'localhost' && !window.DCLT_DEBUG_ANALYTICS) {
    console.log('📊 Analytics (dev):', eventName, eventData);
    return;
  }
  
  fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      action: 'dclt_track_event',
      nonce: window.preserveExplorerData?.analyticsNonce,
      event_name: eventName,
      event_data: JSON.stringify(eventData),
      preserve_name: preserveName
    })
  });
};