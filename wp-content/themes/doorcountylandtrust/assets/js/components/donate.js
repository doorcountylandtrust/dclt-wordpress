/**
 * DCLT Donation Form Component
 * Handles donation form interactions and Stripe checkout
 * 
 * @package DoorCountyLandTrust
 */

var DCLT = DCLT || {};

DCLT.donate = {
  
  // Configuration
  config: {
    checkoutBase: 'https://lwetcjfjbcfepcgveglc.supabase.co',
    checkoutPath: '/functions/v1/create-checkout-session',
    restPath: '/rest/v1/pending_checks',
    // anonKey: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx3ZXRjamZqYmNmZXBjZ3ZlZ2xjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzMzNDk1ODAsImV4cCI6MjA0ODkyNTU4MH0.LCz89ausV-HvJfYDgCFRB_FTzo5WwjiCQLCeWdaJ9rU',
    anonKey: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx3ZXRjamZqYmNmZXBjZ3ZlZ2xjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTI4NjU3MTMsImV4cCI6MjA2ODQ0MTcxM30.bNx0n5_shCrnukibEGAENPuvPHs4c5NeMBDqZowuKcQ',
    publishableKey: 'sb_publishable_nJCGbWLvl20zgfoBMEKV3g_0L8enq2t',
    campaignMap: {
      'unrestricted': null,
      'conservation_stewardship': '701Vo00000W8abmIAB',
      'land_acquisition': null,
      'education': null
    }
  },
  
  // State
  state: {
    selectedAmount: null,
    isMatch: false
  },
  
  // DOM elements cache
  els: {},
  
  /**
   * Initialize the donation form
   */
  init: function() {
    // Check if form exists on page
    if (!document.getElementById('dclt-donate-form')) {
      return;
    }
    
    // Cache DOM elements
    this.cacheElements();
    
    // Bind events
    this.bindEvents();
    
    // Check for URL parameters (for campaign-specific forms)
    this.checkUrlParams();
    
    // Initial UI update
    this.updateUI();
  },
  
  /**
   * Cache DOM elements for performance
   */
  cacheElements: function() {
    this.els = {
      form: document.getElementById('dclt-donate-form'),
      submitBtn: document.getElementById('dclt-submit'),
      amountBtns: document.querySelectorAll('.dclt-amt'),
      customAmount: document.getElementById('dclt-custom-amount'),
      designation: document.getElementById('dclt-designation'),
      coverFees: document.getElementById('dclt-cover-fees'),
      feeLabel: document.getElementById('dclt-fee-label'),
      anonymous: document.getElementById('dclt-anonymous'),
      impact: document.getElementById('dclt-impact'),
      error: document.getElementById('dclt-error'),
      // Tribute fields
      tributeToggle: document.getElementById('dclt-tribute-toggle'),
      tributeFields: document.getElementById('dclt-tribute-fields'),
      honoree: document.getElementById('dclt-honoree'),
      notify: document.getElementById('dclt-notify'),
      // Business fields
      businessToggle: document.getElementById('dclt-business-toggle'),
      businessFields: document.getElementById('dclt-business-fields'),
      businessName: document.getElementById('dclt-business-name'),
      // DAF fields
      dafToggle: document.getElementById('dclt-daf-toggle'),
      dafFields: document.getElementById('dclt-daf-fields'),
      // Check fields
      checkToggle: document.getElementById('dclt-check-toggle'),
      checkFields: document.getElementById('dclt-check-fields'),
      checkName: document.getElementById('dclt-check-name'),
      checkEmail: document.getElementById('dclt-check-email'),
      checkAmount: document.getElementById('dclt-check-amount'),
      checkSubmit: document.getElementById('dclt-check-submit'),
      checkSuccess: document.getElementById('dclt-check-success'),
      // Match banner
      matchBanner: document.getElementById('match-banner')
    };
  },
  
  /**
   * Bind event listeners
   */
  bindEvents: function() {
    var self = this;
    
    // Amount buttons
    this.els.amountBtns.forEach(function(btn) {
      btn.onclick = function() {
        self.selectAmount(parseInt(this.getAttribute('data-amount')));
      };
    });
    
    // Custom amount input
    this.els.customAmount.oninput = function() {
      self.clearAmountButtons();
      self.state.selectedAmount = parseInt(this.value) || 0;
      self.updateUI();
    };
    
    // Cover fees checkbox
    this.els.coverFees.onchange = function() {
      self.updateUI();
    };
    
    // Toggles
    this.els.tributeToggle.onchange = function() {
      DCLT.utils.toggle(self.els.tributeFields, this.checked);
    };
    
    this.els.businessToggle.onchange = function() {
      DCLT.utils.toggle(self.els.businessFields, this.checked);
    };
    
    this.els.dafToggle.onchange = function() {
      DCLT.utils.toggle(self.els.dafFields, this.checked);
    };
    
    this.els.checkToggle.onchange = function() {
        DCLT.utils.toggle(self.els.checkFields, this.checked);
        // Hide Give Now button when check is selected
        self.els.submitBtn.style.display = this.checked ? 'none' : 'block';
        // Also hide cover fees (not relevant for checks)
        self.els.coverFees.parentElement.style.display = this.checked ? 'none' : 'flex';
        };
    
    // Check pledge submit
    this.els.checkSubmit.onclick = function() {
      self.submitCheckPledge();
    };
    
    // Main submit
    this.els.submitBtn.onclick = function() {
      self.submitDonation();
    };
  },
  
  /**
   * Check URL parameters for pre-fill
   */
  checkUrlParams: function() {
    var amount = DCLT.utils.getUrlParam('amount');
    var campaign = DCLT.utils.getUrlParam('campaign');
    var match = DCLT.utils.getUrlParam('match');
    
    if (amount) {
      this.selectAmount(parseInt(amount));
    }
    
    if (campaign) {
      this.els.designation.value = campaign;
    }
    
    if (match === 'true' || match === '1') {
      this.state.isMatch = true;
      DCLT.utils.show(this.els.matchBanner);
    }
  },
  
  /**
   * Select a preset amount
   */
  selectAmount: function(amount) {
    this.clearAmountButtons();
    
    // Highlight selected button
    var self = this;
    this.els.amountBtns.forEach(function(btn) {
      if (parseInt(btn.getAttribute('data-amount')) === amount) {
        btn.style.borderColor = '#2d5016';
        btn.style.background = '#f0f7ec';
      }
    });
    
    this.state.selectedAmount = amount;
    this.els.customAmount.value = '';
    this.updateUI();
  },
  
  /**
   * Clear amount button styles
   */
  clearAmountButtons: function() {
    this.els.amountBtns.forEach(function(btn) {
      btn.style.borderColor = '#ddd';
      btn.style.background = 'white';
    });
  },
  
  /**
   * Get membership tier based on amount
   */
  getTier: function(amount) {
    if (amount >= 500) return 'Guardian';
    if (amount >= 250) return 'Steward';
    if (amount >= 50) return 'Member';
    return null;
  },
  
  /**
   * Calculate final amount with fees
   */
  getFinalAmount: function() {
    var amount = this.state.selectedAmount || 0;
    if (this.els.coverFees.checked) {
      return Math.round(amount * 1.03);
    }
    return amount;
  },
  
  /**
   * Update UI based on current state
   */
  updateUI: function() {
    var amount = this.state.selectedAmount || 0;
    var tier = this.getTier(amount);
    var feeAmount = Math.round(amount * 0.03);
    var displayAmount = this.getFinalAmount();
    
    // Update fee label
    this.els.feeLabel.textContent = 'Add ' + DCLT.utils.formatCurrency(feeAmount) + ' to cover transaction fees';
    
    // Update button text
    this.els.submitBtn.textContent = amount > 0 ? 'Give ' + DCLT.utils.formatCurrency(displayAmount) : 'Give Now';
    
    // Update impact message
    if (amount >= 50 && tier) {
      var msg = 'Your <strong>' + DCLT.utils.formatCurrency(displayAmount) + '</strong> gift makes you a <strong>' + tier + '</strong>';
      if (this.state.isMatch) {
        msg += ' - doubled to <strong>' + DCLT.utils.formatCurrency(displayAmount * 2) + '</strong> impact!';
      } else {
        msg += ' - thank you!';
      }
      this.els.impact.innerHTML = msg;
      DCLT.utils.show(this.els.impact);
    } else if (amount > 0 && this.state.isMatch) {
      this.els.impact.innerHTML = 'Your gift will be doubled to <strong>' + DCLT.utils.formatCurrency(displayAmount * 2) + '</strong>!';
      DCLT.utils.show(this.els.impact);
    } else {
      DCLT.utils.hide(this.els.impact);
    }
  },
  
  /**
   * Show error message
   */
  showError: function(message) {
    this.els.error.textContent = message;
    DCLT.utils.show(this.els.error);
  },
  
  /**
   * Hide error message
   */
  hideError: function() {
    DCLT.utils.hide(this.els.error);
  },
  
  /**
   * Validate form before submission
   */
  validate: function() {
    var amount = this.state.selectedAmount;
    
    if (!amount || amount < 1) {
      this.showError('Please select or enter an amount.');
      return false;
    }
    
    if (this.els.tributeToggle.checked && !this.els.honoree.value.trim()) {
      this.showError('Please enter the honoree name.');
      return false;
    }
    
    if (this.els.businessToggle.checked && !this.els.businessName.value.trim()) {
      this.showError('Please enter the business name.');
      return false;
    }
    
    this.hideError();
    return true;
  },
  
  /**
   * Submit check pledge notification
   */
  submitCheckPledge: function() {
    var self = this;
    var name = this.els.checkName.value.trim();
    var email = this.els.checkEmail.value.trim();
    var amount = this.els.checkAmount.value;
    var designation = this.els.designation.value;
    
    if (!name || !email) {
      window.alert('Please enter your name and email.');
      return;
    }
    
    if (!DCLT.utils.isValidEmail(email)) {
      window.alert('Please enter a valid email address.');
      return;
    }
    
    this.els.checkSubmit.textContent = 'Sending...';
    this.els.checkSubmit.disabled = true;
    
    DCLT.utils.post(
      this.config.checkoutBase + this.config.restPath,
      {
        name: name,
        email: email,
        amount: amount || null,
        designation: designation
      },
      {
        'apikey': this.config.publishableKey,
        'Prefer': 'return=minimal'
      }
    ).then(function() {
      DCLT.utils.show(self.els.checkSuccess);
      DCLT.utils.hide(self.els.checkSubmit);
    }).catch(function() {
      window.alert('Something went wrong. Please try again.');
      self.els.checkSubmit.textContent = 'Notify Us';
      self.els.checkSubmit.disabled = false;
    });
  },
  
  /**
   * Submit donation to Stripe
   */
  submitDonation: function() {
    var self = this;
    
    if (!this.validate()) {
      return;
    }
    
    var amount = this.state.selectedAmount;
    var finalAmount = this.getFinalAmount();
    var displayAmount = finalAmount;
    
    this.els.submitBtn.textContent = 'Processing...';
    this.els.submitBtn.disabled = true;
    
    // Build payload
    var payload = {
      amount: finalAmount,
      designation: this.els.designation.value,
      gift_type: amount >= 50 ? 'membership' : 'donation',
      source: 'website',
      is_anonymous: this.els.anonymous.checked,
      campaign_id: this.config.campaignMap[this.els.designation.value] || null,
      success_url: window.location.origin + '/thank-you/',
      cancel_url: window.location.href
    };
    
    // Add tribute info
    if (this.els.tributeToggle.checked) {
      var tributeRadio = document.querySelector('input[name="dclt_tribute_type"]:checked');
      payload.tribute_type = tributeRadio ? tributeRadio.value : 'honor';
      payload.honoree_name = this.els.honoree.value;
      payload.notify_email = this.els.notify.value;
    }
    
    // Add business info
    if (this.els.businessToggle.checked) {
      payload.business_name = this.els.businessName.value;
      payload.is_business = true;
    }
    
    // Submit to checkout
    DCLT.utils.post(
      this.config.checkoutBase + this.config.checkoutPath,
      payload
    ).then(function(data) {
      if (data.url) {
        window.location.href = data.url;
      } else {
        self.showError(data.error || 'Failed to create checkout');
        self.els.submitBtn.textContent = 'Give ' + DCLT.utils.formatCurrency(displayAmount);
        self.els.submitBtn.disabled = false;
      }
    }).catch(function(err) {
      self.showError('Connection error. Please try again.');
      self.els.submitBtn.textContent = 'Give ' + DCLT.utils.formatCurrency(displayAmount);
      self.els.submitBtn.disabled = false;
    });
  }
  
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    DCLT.donate.init();
  });
} else {
  DCLT.donate.init();
}