<?php
/**
 * Template Name: Donation Form
 * 
 * Clean donation page template
 * JavaScript loaded via wp_enqueue_script in functions.php
 */

get_header();
?>

<main class="donate-page" style="padding: 40px 20px; background: #f8f8f6; min-height: 80vh;">

<div id="dclt-donate-form" style="max-width: 480px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #333;">
  
  <!-- Header -->
  <div style="text-align: center; margin-bottom: 24px;">
    <h2 style="color: #2d5016; font-size: 26px; margin: 0 0 8px 0; font-weight: 600;" id="donate-headline">
      Protect Door County's Wild Places
    </h2>
    <p style="color: #666; font-size: 15px; margin: 0;" id="donate-subtext">
      Your gift helps preserve the lands and waters we all love.
    </p>
  </div>

  <!-- Match Banner (hidden by default, shown via URL param ?match=true) -->
  <div id="match-banner" style="display: none; background: #fef9e7; border: 1px solid #f4d03f; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <span style="font-size: 20px;">✓</span>
      <div>
        <strong style="color: #9a7b0a; font-size: 14px; display: block;" id="match-title">Matching gift active</strong>
        <span style="color: #7d6608; font-size: 13px;" id="match-desc">Your gift will be doubled.</span>
      </div>
    </div>
  </div>

  <!-- Amount Selection -->
  <div style="margin-bottom: 20px;">
    <div style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">Choose your gift amount</div>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 10px;">
      <button type="button" class="dclt-amt" data-amount="50" style="padding: 14px 8px; border: 2px solid #ddd; background: white; border-radius: 8px; cursor: pointer; text-align: center;">
        <span style="font-size: 22px; font-weight: 600; color: #2d5016; display: block;">$50</span>
        <span style="font-size: 11px; color: #888;">Member</span>
      </button>
      <button type="button" class="dclt-amt" data-amount="100" style="padding: 14px 8px; border: 2px solid #ddd; background: white; border-radius: 8px; cursor: pointer; text-align: center;">
        <span style="font-size: 22px; font-weight: 600; color: #2d5016; display: block;">$100</span>
        <span style="font-size: 11px; color: #888;">Member</span>
      </button>
      <button type="button" class="dclt-amt" data-amount="250" style="padding: 14px 8px; border: 2px solid #ddd; background: white; border-radius: 8px; cursor: pointer; text-align: center;">
        <span style="font-size: 22px; font-weight: 600; color: #2d5016; display: block;">$250</span>
        <span style="font-size: 11px; color: #2d5016; font-weight: 500;">Steward</span>
      </button>
      <button type="button" class="dclt-amt" data-amount="500" style="padding: 14px 8px; border: 2px solid #ddd; background: white; border-radius: 8px; cursor: pointer; text-align: center;">
        <span style="font-size: 22px; font-weight: 600; color: #2d5016; display: block;">$500</span>
        <span style="font-size: 11px; color: #2d5016; font-weight: 500;">Guardian</span>
      </button>
    </div>
    <div style="position: relative;">
      <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #666; font-size: 16px;">$</span>
      <input type="number" id="dclt-custom-amount" placeholder="Enter custom amount" min="1" style="width: 100%; padding: 12px 12px 12px 30px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
    </div>
  </div>

  <!-- Designation -->
  <div style="margin-bottom: 20px;">
    <div style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">Direct my gift to</div>
    <select id="dclt-designation" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; background: white;">
      <option value="unrestricted">General Fund (where needed most)</option>
      <option value="conservation_stewardship">Conservation and Stewardship</option>
      <option value="land_acquisition">Land Acquisition</option>
      <option value="education">Education Programs</option>
    </select>
  </div>

  <!-- Tribute Toggle -->
  <div style="margin-bottom: 12px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f8f6; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-tribute-toggle" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #555;">This gift is in honor or memory of someone</span>
    </label>
    <div id="dclt-tribute-fields" style="display: none; padding: 14px; background: #f8f8f6; border-radius: 0 0 8px 8px; margin-top: -8px;">
      <div style="display: flex; gap: 16px; margin-bottom: 10px;">
        <label style="display: flex; align-items: center; gap: 5px; font-size: 13px; color: #555; cursor: pointer;">
          <input type="radio" name="dclt_tribute_type" value="honor" checked> In Honor
        </label>
        <label style="display: flex; align-items: center; gap: 5px; font-size: 13px; color: #555; cursor: pointer;">
          <input type="radio" name="dclt_tribute_type" value="memory"> In Memory
        </label>
      </div>
      <input type="text" id="dclt-honoree" placeholder="Honoree's name" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 8px; box-sizing: border-box;">
      <input type="email" id="dclt-notify" placeholder="Send notification to (email, optional)" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
    </div>
  </div>

  <!-- Business Toggle -->
  <div style="margin-bottom: 12px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f8f6; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-business-toggle" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #555;">This is a business or organization gift</span>
    </label>
    <div id="dclt-business-fields" style="display: none; padding: 14px; background: #f8f8f6; border-radius: 0 0 8px 8px; margin-top: -8px;">
      <input type="text" id="dclt-business-name" placeholder="Business or organization name" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
    </div>
  </div>

  <!-- DAF / IRA Toggle -->
  <div style="margin-bottom: 12px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f8f6; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-daf-toggle" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #555;">Give from a Donor Advised Fund or IRA</span>
    </label>
    <div id="dclt-daf-fields" style="display: none; padding: 14px; background: #f8f8f6; border-radius: 0 0 8px 8px; margin-top: -8px;">
      <p style="font-size: 13px; color: #555; margin: 0 0 12px 0;">
        <strong>To give from your DAF:</strong> Contact your fund administrator and direct your gift to Door County Land Trust, EIN 39-1424359.
      </p>
      <p style="font-size: 13px; color: #555; margin: 0 0 12px 0;">
        <strong>To give from your IRA:</strong> Contact your IRA custodian to make a Qualified Charitable Distribution.
      </p>
      <p style="font-size: 13px; color: #555; margin: 0;">
        Questions? Call <a href="tel:920-746-1359" style="color: #2d5016;">(920) 746-1359</a>
      </p>
    </div>
  </div>

  <!-- Sending a Check Toggle -->
  <div style="margin-bottom: 12px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f8f6; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-check-toggle" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #555;">I am sending a check by mail</span>
    </label>
    <div id="dclt-check-fields" style="display: none; padding: 14px; background: #f8f8f6; border-radius: 0 0 8px 8px; margin-top: -8px;">
      <p style="font-size: 13px; color: #555; margin: 0 0 12px 0;">
        <strong>Please make checks payable to:</strong><br>
        Door County Land Trust<br>
        P.O. Box 65<br>
        Sturgeon Bay, WI 54235
      </p>
      <input type="text" id="dclt-check-name" placeholder="Your name" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 8px; box-sizing: border-box;">
      <input type="email" id="dclt-check-email" placeholder="Your email" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 8px; box-sizing: border-box;">
      <input type="number" id="dclt-check-amount" placeholder="Expected amount" min="1" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 12px; box-sizing: border-box;">
      <button type="button" id="dclt-check-submit" style="width: 100%; padding: 12px; background: #2d5016; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">
        Notify Us
      </button>
      <p id="dclt-check-success" style="display: none; color: #2d5016; font-size: 13px; margin-top: 10px; text-align: center;">Thanks! We'll watch for your check.</p>
    </div>
  </div>

  <!-- Cover Fees -->
  <div style="margin-bottom: 12px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f0f7ec; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-cover-fees" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #2d5016;" id="dclt-fee-label">Add $0 to cover transaction fees</span>
    </label>
  </div>

  <!-- Anonymous -->
  <div style="margin-bottom: 20px;">
    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f8f6; border-radius: 8px; cursor: pointer;">
      <input type="checkbox" id="dclt-anonymous" style="width: 18px; height: 18px;">
      <span style="font-size: 14px; color: #555;">Make my gift anonymous</span>
    </label>
  </div>

  <!-- Impact Message -->
  <div id="dclt-impact" style="display: none; text-align: center; padding: 12px; background: #f0f7ec; border-radius: 8px; margin-bottom: 12px; font-size: 14px; color: #2d5016;"></div>

  <!-- Submit Button -->
  <button id="dclt-submit" style="width: 100%; padding: 16px; background: #2d5016; color: white; border: none; border-radius: 8px; font-size: 17px; font-weight: 600; cursor: pointer;">
    Give Now
  </button>

  <p id="dclt-error" style="color: #c41e3a; text-align: center; margin-top: 10px; font-size: 14px; display: none;"></p>

  <p style="text-align: center; margin-top: 16px; font-size: 12px; color: #888;">
    Door County Land Trust is a 501(c)(3) nonprofit.<br>Your gift is tax-deductible. Secure payment via Stripe.
  </p>

</div>

</main>

<?php get_footer(); ?>