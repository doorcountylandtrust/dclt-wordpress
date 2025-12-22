/**
 * DCLT Modal Component
 * Reusable accessible modal dialog
 * 
 * @package DoorCountyLandTrust
 */

var DCLT = DCLT || {};

DCLT.modal = {
  
  activeModal: null,
  previousFocus: null,
  
  /**
   * Open a modal
   * @param {string} modalId - ID of modal element
   * @param {object} options - optional settings
   */
  open: function(modalId, options) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    
    options = options || {};
    
    // Store previous focus for restoration
    this.previousFocus = document.activeElement;
    this.activeModal = modal;
    
    // Show modal
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    
    // Focus first focusable element
    var focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
      focusable.focus();
    }
    
    // Bind escape key
    this.bindEscape();
    
    // Bind click outside
    if (options.closeOnOverlay !== false) {
      this.bindOverlayClick(modal);
    }
    
    // Callback
    if (options.onOpen) {
      options.onOpen(modal);
    }
  },
  
  /**
   * Close the active modal
   */
  close: function() {
    if (!this.activeModal) return;
    
    this.activeModal.style.display = 'none';
    this.activeModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    
    // Restore focus
    if (this.previousFocus) {
      this.previousFocus.focus();
    }
    
    // Unbind events
    document.removeEventListener('keydown', this.escapeHandler);
    
    this.activeModal = null;
  },
  
  /**
   * Bind escape key to close
   */
  bindEscape: function() {
    var self = this;
    this.escapeHandler = function(e) {
      if (e.key === 'Escape') {
        self.close();
      }
    };
    document.addEventListener('keydown', this.escapeHandler);
  },
  
  /**
   * Bind overlay click to close
   */
  bindOverlayClick: function(modal) {
    var self = this;
    modal.onclick = function(e) {
      if (e.target === modal) {
        self.close();
      }
    };
  },
  
  /**
   * Create a modal element dynamically
   * @param {object} options
   * @returns {HTMLElement}
   */
  create: function(options) {
    options = options || {};
    
    var id = options.id || 'dclt-modal-' + Date.now();
    
    var modal = document.createElement('div');
    modal.id = id;
    modal.className = 'dclt-modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-hidden', 'true');
    if (options.label) {
      modal.setAttribute('aria-label', options.label);
    }
    
    modal.style.cssText = 'display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;';
    
    var content = document.createElement('div');
    content.className = 'dclt-modal-content';
    content.style.cssText = 'background: white; border-radius: 12px; max-width: ' + (options.maxWidth || '500px') + '; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;';
    
    // Close button
    if (options.showClose !== false) {
      var closeBtn = document.createElement('button');
      closeBtn.className = 'dclt-modal-close';
      closeBtn.innerHTML = '&times;';
      closeBtn.setAttribute('aria-label', 'Close modal');
      closeBtn.style.cssText = 'position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666; line-height: 1;';
      closeBtn.onclick = this.close.bind(this);
      content.appendChild(closeBtn);
    }
    
    // Inner content wrapper
    var inner = document.createElement('div');
    inner.className = 'dclt-modal-inner';
    inner.style.cssText = 'padding: 24px;';
    
    if (options.content) {
      if (typeof options.content === 'string') {
        inner.innerHTML = options.content;
      } else {
        inner.appendChild(options.content);
      }
    }
    
    content.appendChild(inner);
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    return modal;
  },
  
  /**
   * Destroy a modal element
   * @param {string} modalId
   */
  destroy: function(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
      if (this.activeModal === modal) {
        this.close();
      }
      modal.parentNode.removeChild(modal);
    }
  }
  
};