# NimblyScout

Rebranded from the supplied website archive. The landing page is **index.php**.
The original page layout, forest green (#15362f), lime (#e9efad), guide,
photograph, and downloadable job tracker are retained. The shared SVG logo
and favicon use an N monogram with a compass-style spark.

## Hosting

Use PHP 8.1 or newer with sessions and PHP `mail()` enabled. Upload these
website files to your domain's document root:

- `index.php`, `contact.php`, `form-session.php`, and `.htaccess`
- `icon.svg`, `us-s-style.css`, `contact-workspace.webp`
- `remote-job-search-tracker.csv`, `privacy-policy/`, and `thank-you/`

Apache reads the included `DirectoryIndex index.php index.html`, serving
the PHP landing page and HTML subpages at their directory URLs. All site
links use `privacy-policy/`, and successful form submissions go to
`thank-you/`. With Apache mod_rewrite enabled, old requests ending in
`/privacy-policy/index.html` or `/thank-you/index.html` redirect permanently
to the clean directory URL. Upload the included `.htaccess` file as well.
For Nginx or IIS, configure both index.php and index.html as default
documents and equivalent redirects through the host settings.
There is no landing index.html. GitHub stores the source; GitHub Pages
cannot run this PHP form.

## Contact email

All inquiries are addressed to **contact@nimblyscout.com**. The fixed From
address is also contact@nimblyscout.com; the validated visitor email is
Reply-To. The handler never sends messages to a visitor-supplied recipient.

Create the contact mailbox and have your host configure PHP's outbound
mail transport for nimblyscout.com. Ask the host for its required SPF/DKIM
DNS records. No mail password or third-party delivery account is embedded.

The form validates the fields, uses a session CSRF token and honeypot,
escapes retained entries, and redirects to confirmation only when the mail
transport accepts the message. Acceptance does not guarantee inbox delivery.
If transport fails, the site retains the entries for the next page view and
shows an error with the direct contact address. Production delivery must
be checked on the actual hosting account.

## Local preview and checks

```text
php -S 127.0.0.1:8080
python checks/check_destination.py
python -m unittest discover -s checks -p "test_*.py" -v
php -l index.php
php -l contact.php
php -l form-session.php
```

Open http://127.0.0.1:8080/index.php. The integration tests run a separate
PHP server and capture outbound mail with a local SMTP sink, so no test
messages are sent over the internet. They require PHP on PATH and permission
to connect over localhost. The local preview needs a configured mail
transport to send real mail.
