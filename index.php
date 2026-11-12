<?php
require __DIR__ . '/form-session.php';
$formErrors = $_SESSION['form_errors'] ?? [];
$formValues = $_SESSION['form_values'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_values']);
?>
<!doctype html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="NimblyScout’s free guide to finding part-time remote work in the U.S. Learn how to search, read a posting, and spot signs of a job scam.">
  <meta name="theme-color" content="#15362f">
  <title>How to Find Part-Time Remote Jobs | NimblyScout</title>
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="us-s-style.css">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="#main">
        <img src="icon.svg" width="36" height="36" alt="">
        NimblyScout
      </a>
      <nav aria-label="Main navigation">
        <a href="#guide">The guide</a>
        <a href="#scam-checklist">Scam checklist</a>
        <a href="#tracker">Tracker</a>
        <a href="#contact">Contact</a>
      </nav>
    </div>
  </header>
  <main id="main">
    <section class="hero" aria-labelledby="title">
      <div class="wrap hero-grid">
        <div class="hero-copy">
          <p class="eyebrow hero-kicker"><span></span> Free guide for U.S. job seekers</p>
          <h1 id="title">How to Find Part-Time Remote Jobs</h1>
          <p class="intro">Learn where to look for work-from-home jobs, how to check if a schedule fits, and what to look for before you apply.</p>
          <div class="hero-actions">
            <a class="button button-light" href="#guide">Start the free guide <span aria-hidden="true">↓</span></a>
            <a class="plain-link" href="#scam-checklist">View scam checklist <span aria-hidden="true">↗</span></a>
          </div>
        </div>
        <figure class="art" aria-hidden="true">
          <div class="art-sheet art-sheet-back"></div>
          <div class="art-sheet art-sheet-front">
            <div class="art-top"><span>FIELD NOTES</span><span>01</span></div>
            <span class="art-title">My remote<br>work search</span>
            <div class="art-row"><span>01</span><span>Pick a schedule</span><b>✓</b></div>
            <div class="art-row"><span>02</span><span>Check state eligibility</span><b>✓</b></div>
            <div class="art-row"><span>03</span><span>Verify the source</span><b>✓</b></div>
          </div>
          <div class="art-stamp">✳</div>
        </figure>
      </div>
    </section>
    <section class="wrap guide section" id="guide" aria-labelledby="guide-title">
      <div class="section-intro">
        <p class="eyebrow">01 / The guide</p>
        <h2 id="guide-title">Start with a plan</h2>
        <p>Use these three steps to narrow your search and decide which postings are worth a closer look.</p>
      </div>
      <div class="steps">
        <article class="step">
          <p class="step-number" aria-hidden="true">01</p>
          <h3>Decide when you can work</h3>
          <p>Write down the hours you can work each week, the days you’re available, and the time zones you can cover. Include evenings or weekends if they work for you.</p>
          <p>A remote job can still have fixed shifts. Check the schedule and weekly hours before applying.</p>
        </article>
        <article class="step">
          <p class="step-number" aria-hidden="true">02</p>
          <h3>Search by role and schedule</h3>
          <p>Pair a job title with “remote” and “part-time.” Add a shift if you have one in mind. Try these sample searches.</p>
          <ul class="query-list">
            <li>part-time remote customer service jobs</li>
            <li>part-time remote scheduling coordinator jobs</li>
            <li>part-time remote evening customer support jobs</li>
          </ul>
          <p>Try employer careers pages and job boards with location and job-type filters. Compare postings you find with the employer’s official careers page.</p>
        </article>
        <article class="step">
          <p class="step-number" aria-hidden="true">03</p>
          <h3>Read the posting before applying</h3>
          <ul class="details-list">
            <li><strong>Location.</strong> Does the employer hire in your state? Is any training in person?</li>
            <li><strong>Schedule.</strong> How many hours are expected? Are there set shifts or time-zone requirements?</li>
            <li><strong>Employment type.</strong> Does the posting describe a part-time employee role?</li>
            <li><strong>Pay.</strong> If listed, does the range apply to your location? Is it hourly or salaried?</li>
          </ul>
          <p>Write down anything that’s unclear so you can ask the employer directly.</p>
        </article>
      </div>
    </section>
    <section class="safety" id="scam-checklist" aria-labelledby="safety-title">
      <div class="wrap section safety-grid">
        <div class="section-intro">
          <p class="eyebrow">02 / Scam checklist</p>
          <h2 id="safety-title">Check the source before you share</h2>
          <p>Take a few minutes to confirm who’s asking for your time, money, or personal information.</p>
          <a class="plain-link" href="https://consumer.ftc.gov/articles/job-scams">Read the FTC’s job scam guidance <span aria-hidden="true">↗</span></a>
        </div>
        <ol class="checklist">
          <li><strong>Confirm who’s hiring.</strong> Find the employer’s official website independently and use its contact details if you need to verify a posting or recruiter.</li>
          <li><strong>Walk away from payment requests.</strong> Skip offers that require you to pay to get hired, unlock tasks, or withdraw earnings.</li>
          <li><strong>Watch for fake checks.</strong> Stop if someone sends you a check for equipment and asks you to return part of the money or send it to someone else.</li>
          <li><strong>Protect sensitive details.</strong> Keep your Social Security number and bank details out of early job-search messages. Use a verified employer’s secure onboarding process when those details are needed.</li>
        </ol>
      </div>
    </section>
    <section class="wrap section tracker" id="tracker" aria-labelledby="tracker-title">
      <div class="section-intro">
        <p class="eyebrow">03 / Free template</p>
        <h2 id="tracker-title">Keep your search organized</h2>
        <p>Track the role, the original posting link, the schedule, and your next step in one place. Open the CSV in a spreadsheet app or use the column names in your own notes.</p>
        <a class="button button-dark" href="remote-job-search-tracker.csv" download>Download the tracker <span aria-hidden="true">↓</span></a>
      </div>
      <div class="tracker-preview">
        <p class="eyebrow">What’s in the template</p>
        <dl>
          <div><dt>Job details</dt><dd>Employer, role, and posting link</dd></div>
          <div><dt>Schedule</dt><dd>Hours and time zone</dd></div>
          <div><dt>Follow-up</dt><dd>Date applied, status, and next step</dd></div>
        </dl>
      </div>
    </section>
    <section class="contact" id="contact" aria-labelledby="contact-title">
      <div class="wrap section contact-grid">
        <div class="section-intro">
          <p class="eyebrow">04 / Contact</p>
          <h2 id="contact-title">Ask a question about the guide</h2>
          <p>Have a question about something you read or a suggestion for a topic we should cover? We’ll use your email address to reply. You can also email <a href="mailto:contact@nimblyscout.com">contact@nimblyscout.com</a>.</p>
          <img class="contact-photo" src="contact-workspace.webp" width="1040" height="520" loading="lazy" alt="">
        </div>
        <form class="contact-form" method="post" action="contact.php">
          <?php if ($formErrors): ?>
          <div class="form-errors" role="alert" tabindex="-1" id="form-errors">
            <p><strong>Your message hasn’t been sent.</strong></p>
            <ul>
              <?php foreach ($formErrors as $error): ?>
              <li><?= escape($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
          <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">
          <div class="form-trap" aria-hidden="true" inert>
            <label for="contact-website">Leave this field empty</label>
            <input id="contact-website" name="website" tabindex="-1" autocomplete="off">
          </div>
          <fieldset>
            <div class="contact-field">
              <label for="contact-name">Name</label>
              <input id="contact-name" name="name" autocomplete="name" maxlength="100" value="<?= escape($formValues['name'] ?? '') ?>" required>
            </div>
            <div class="contact-field">
              <label for="contact-email">Email address</label>
              <input id="contact-email" name="email" type="email" autocomplete="email" maxlength="254" value="<?= escape($formValues['email'] ?? '') ?>" required>
            </div>
            <div class="contact-field">
              <label for="contact-message">Message <span>(optional)</span></label>
              <textarea id="contact-message" name="message" rows="5" maxlength="3000"><?= escape($formValues['message'] ?? '') ?></textarea>
            </div>
            <button class="button button-dark" type="submit">Send message</button>
          </fieldset>
        </form>
      </div>
    </section>
  </main>
  <footer class="site-footer" id="about">
    <div class="wrap footer-inner">
      <div>
        <a class="brand" href="#main">
          <img src="icon.svg" width="36" height="36" alt="">
          NimblyScout
        </a>
        <p>We publish educational guides for U.S. job seekers. For openings and application instructions, visit the employer’s official careers page.</p>
      </div>
      <div class="footer-bottom">
        <p>© 2026 NimblyScout. All rights reserved.</p>
        <div class="footer-links">
          <a class="plain-link" href="privacy/index.html">Privacy policy</a>
          <a class="plain-link" href="#main">Back to top <span aria-hidden="true">↑</span></a>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
