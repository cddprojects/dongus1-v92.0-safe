# NimblyScout implementation plan

> Execute in this isolated repository checkout using the confirmed user requirements.

**Goal:** Rebrand the supplied ZIP as NimblyScout, require index.php, deliver inquiries to contact@nimblyscout.com, and push to the specified empty GitHub repository.

**Architecture:** Preserve the supplied HTML/CSS layout. Serve the landing page with PHP and process its form through contact.php using the host mail transport. Keep privacy and confirmation pages as HTML.

**Tech stack:** PHP 8.1+, HTML, CSS, SVG; Python standard-library integration tests.

**Spec:** User confirmations in this chat: NimblyScout everywhere, @nimblyscout.com email addresses, similar logo/favicons with original colors, mandatory index.php, push to cddprojects/dongus1-v92.0-safe.

## Constraints
- Keep the current layout, copy purpose, downloadable tracker, and forest/lime colors.
- No obsolete landing index.html or references to it.
- Only show success after the mail transport accepts the message.
- Never put a visitor-supplied address in From; validate it before Reply-To.
- No inherited Git metadata, hosting identity, or instructions from the ZIP.

## Review focus
- Invalid or array-valued input must not send email or crash.
- Failed mail must preserve entered fields and show an accessible error.
- CSRF protection, escaped values, and header injection rejection.
- Local links, page anchors, and mobile brand fit after renaming.
- PHP hosting must be documented; GitHub alone does not execute PHP.

## Task 1: Brand and PHP form
- [x] Add integration tests for real HTTP/PHP and a local SMTP sink; run and observe failures.
- [x] Rename landing page, update brand metadata/links and SVG mark, retain layout.
- [x] Add PHP POST handler with validation, CSRF, honeypot, mail delivery, errors and success redirect.
- [x] Update existing content/link check and run all checks and PHP syntax validation.

## Task 2: Verify and deliver
- [x] Check desktop/mobile pages and form error flow in a browser.
- [x] Review final code, document hosting/mail setup, and create a deployment ZIP.
- [ ] Commit and push the verified site to the requested repository; verify remote commit in the execution log.

## Verification record

All 9 HTTP/PHP/SMTP integration tests passed. Content, local links, anchors, assets, tracker, and PHP syntax checks passed. Browser review at mobile and desktop widths confirmed the refreshed mark and layout; mobile had no horizontal overflow. Independent read-only code review found no Critical or Important issues. Production mailbox delivery requires the actual host mail transport and DNS configuration.
