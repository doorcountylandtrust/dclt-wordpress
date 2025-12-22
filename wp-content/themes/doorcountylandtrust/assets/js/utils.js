/**
 * DCLT Shared Utilities
 * Small helper functions used across the site
 * 
 * @package DoorCountyLandTrust
 */

var DCLT = DCLT || {};

DCLT.utils = {
  
  /**
   * Format a number as currency
   * @param {number} amount 
   * @returns {string} Formatted currency string
   */
  formatCurrency: function(amount) {
    return '$' + Number(amount).toLocaleString('en-US');
  },
  
  /**
   * Validate email format
   * @param {string} email 
   * @returns {boolean}
   */
  isValidEmail: function(email) {
    var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
  },
  
  /**
   * Debounce function calls
   * @param {function} func 
   * @param {number} wait - milliseconds
   * @returns {function}
   */
  debounce: function(func, wait) {
    var timeout;
    return function() {
      var context = this;
      var args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(function() {
        func.apply(context, args);
      }, wait);
    };
  },
  
  /**
   * Make an XHR POST request
   * @param {string} url 
   * @param {object} data 
   * @param {object} headers - optional additional headers
   * @returns {Promise}
   */
  post: function(url, data, headers) {
    return new Promise(function(resolve, reject) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', url, true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      
      if (headers) {
        Object.keys(headers).forEach(function(key) {
          xhr.setRequestHeader(key, headers[key]);
        });
      }
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status >= 200 && xhr.status < 300) {
            try {
              resolve(JSON.parse(xhr.responseText));
            } catch (e) {
              resolve(xhr.responseText);
            }
          } else {
            reject(new Error('Request failed: ' + xhr.status));
          }
        }
      };
      
      xhr.onerror = function() {
        reject(new Error('Network error'));
      };
      
      xhr.send(JSON.stringify(data));
    });
  },
  
  /**
   * Get URL parameters
   * @param {string} param - parameter name
   * @returns {string|null}
   */
  getUrlParam: function(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  },
  
  /**
   * Show an element
   * @param {HTMLElement|string} el - element or selector
   */
  show: function(el) {
    if (typeof el === 'string') {
      el = document.querySelector(el);
    }
    if (el) el.style.display = 'block';
  },
  
  /**
   * Hide an element
   * @param {HTMLElement|string} el - element or selector
   */
  hide: function(el) {
    if (typeof el === 'string') {
      el = document.querySelector(el);
    }
    if (el) el.style.display = 'none';
  },
  
  /**
   * Toggle element visibility
   * @param {HTMLElement|string} el - element or selector
   * @param {boolean} show - optional force state
   */
  toggle: function(el, show) {
    if (typeof el === 'string') {
      el = document.querySelector(el);
    }
    if (!el) return;
    
    if (typeof show === 'boolean') {
      el.style.display = show ? 'block' : 'none';
    } else {
      el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
  }
  
};