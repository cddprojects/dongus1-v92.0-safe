"""Exercise real PHP HTTP requests and capture mail on a local SMTP server.

The SMTP sink replaces only the external email transport. No email leaves
the machine. Requires PHP on PATH; no third-party Python dependencies.
"""
import email
import http.cookiejar
import os
from pathlib import Path
import re
import shutil
import socket
import socketserver
import subprocess
import tempfile
import threading
import time
import unittest
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]


class SMTPHandler(socketserver.StreamRequestHandler):
    def handle(self):
        self.wfile.write(b"220 localhost test SMTP\r\n")
        recipients = []
        while line := self.rfile.readline():
            command = line.decode('utf-8', errors='replace').strip()
            verb = command.split(' ', 1)[0].upper()
            if verb == 'DATA':
                self.wfile.write(b"354 End with a dot\r\n")
                data = []
                while (line := self.rfile.readline()) not in (b'.\r\n', b''):
                    data.append(line[1:] if line.startswith(b'..') else line)
                self.server.messages.append((recipients[:], b''.join(data)))
                self.wfile.write(b"250 Queued\r\n")
            elif verb == 'MAIL' and self.server.reject:
                self.wfile.write(b"550 Mail unavailable\r\n")
            elif verb == 'RCPT':
                recipients.append(command)
                self.wfile.write(b"250 Recipient OK\r\n")
            elif verb == 'QUIT':
                self.wfile.write(b"221 Bye\r\n")
                return
            else:
                self.wfile.write(b"250 OK\r\n")


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *args, **kwargs):
        return None


class ContactTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.smtp = socketserver.ThreadingTCPServer(('127.0.0.1', 0), SMTPHandler)
        cls.smtp.daemon_threads = True
        cls.smtp.messages = []
        cls.smtp.reject = False
        cls.thread = threading.Thread(target=cls.smtp.serve_forever, daemon=True)
        cls.thread.start()
        with socket.socket() as listener:
            listener.bind(('127.0.0.1', 0))
            port = listener.getsockname()[1]
        cls.base = f'http://127.0.0.1:{port}/'
        runtime = ROOT / '.test-runtime'
        runtime.mkdir(exist_ok=True)
        cls.temp = tempfile.TemporaryDirectory(dir=runtime)
        cls.log = open(Path(cls.temp.name) / 'php.log', 'w')
        cls.command = [shutil.which('php'), '-d', f'session.save_path={cls.temp.name}']
        if os.name == 'nt':
            cls.command += ['-d', 'SMTP=127.0.0.1', '-d', f'smtp_port={cls.smtp.server_address[1]}']
        else:
            # A portable sendmail adapter forwards to the same SMTP sink.
            import sys
            adapter = Path(cls.temp.name) / 'sendmail.py'
            adapter.write_text('import smtplib,sys\nfrom email import message_from_bytes\n'
                               'data=sys.stdin.buffer.read()\nmsg=message_from_bytes(data)\n'
                               f'with smtplib.SMTP("127.0.0.1",{cls.smtp.server_address[1]}) as smtp:\n'
                               ' smtp.sendmail(msg["From"],msg["To"],data)\n')
            cls.command += ['-d', f'sendmail_path="{sys.executable}" "{adapter}"']
        cls.command += ['-d', 'sendmail_from=contact@nimblyscout.com', '-S', f'127.0.0.1:{port}', '-t', str(ROOT)]
        cls.php = subprocess.Popen(cls.command, stdout=cls.log, stderr=cls.log,
                                   creationflags=subprocess.CREATE_NO_WINDOW if os.name == 'nt' else 0)
        for _ in range(100):
            try:
                with socket.create_connection(('127.0.0.1', port), timeout=.1):
                    break
            except OSError:
                time.sleep(.05)
        else:
            cls.php.terminate()
            cls.php.wait(timeout=5)
            cls.log.close()
            details = (Path(cls.temp.name) / 'php.log').read_text()
            cls.smtp.shutdown()
            cls.smtp.server_close()
            cls.temp.cleanup()
            raise RuntimeError(f'PHP preview failed to start: {details}')

    @classmethod
    def tearDownClass(cls):
        cls.php.terminate()
        cls.php.wait(timeout=5)
        cls.log.close()
        cls.smtp.shutdown()
        cls.smtp.server_close()
        cls.temp.cleanup()

    def setUp(self):
        self.smtp.messages.clear()
        self.smtp.reject = False
        self.browser = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect())

    def request(self, path, fields=None):
        payload = urllib.parse.urlencode(fields).encode() if fields is not None else None
        try:
            response = self.browser.open(self.base + path, payload, timeout=5)
        except urllib.error.HTTPError as response:
            with response:
                return response.code, response.headers, response.read().decode()
        with response:
            return response.status, response.headers, response.read().decode()

    def form(self):
        status, _, body = self.request('index.php')
        self.assertEqual(status, 200, 'The required index.php landing page must be served')
        token = re.search(r'name="csrf_token" value="([a-f0-9]+)"', body)
        self.assertIsNotNone(token, 'The contact form must protect submissions with a session token')
        return {'name': 'Jane Scout', 'email': 'jane@example.org', 'message': 'A question about remote work.',
                'csrf_token': token.group(1), 'website': ''}

    def test_valid_inquiry_is_mailed_to_brand_contact(self):
        fields = self.form()
        status, headers, _ = self.request('contact.php', fields)
        self.assertEqual(status, 303)
        self.assertEqual(headers['Location'], 'thank-you/index.html')
        self.assertEqual(len(self.smtp.messages), 1)
        recipients, data = self.smtp.messages[0]
        self.assertEqual(recipients, ['RCPT TO:<contact@nimblyscout.com>'])
        message = email.message_from_bytes(data)
        self.assertEqual(message['From'], 'NimblyScout <contact@nimblyscout.com>')
        self.assertEqual(message['Reply-To'], 'jane@example.org')
        self.assertIn('NimblyScout', message['Subject'])
        self.assertIn('Jane Scout', message.get_payload(decode=True).decode('utf-8'))
        self.assertIn(fields['message'], message.get_payload(decode=True).decode('utf-8'))

    def test_optional_message_and_unicode_name_are_accepted(self):
        fields = self.form()
        fields.update(name='Zoë 李', message='')
        status, headers, _ = self.request('contact.php', fields)
        self.assertEqual((status, headers['Location']), (303, 'thank-you/index.html'))
        message = email.message_from_bytes(self.smtp.messages[0][1])
        self.assertIn('Zoë 李', message.get_payload(decode=True).decode('utf-8'))

    def test_invalid_fields_never_send_mail(self):
        for change in ({'email': 'invalid'}, {'name': ''}, {'name': 'x' * 101},
                       {'message': 'x' * 3001}, {'email': 'a@b.com\r\nBcc: thief@example.org'},
                       {'email': 'x' * 255 + '@example.org'}):
            with self.subTest(change=list(change)):
                fields = self.form()
                fields.update(change)
                status, headers, _ = self.request('contact.php', fields)
                self.assertEqual((status, headers['Location']), (303, 'index.php#contact'))
                self.assertEqual(self.smtp.messages, [])

    def test_array_values_do_not_crash_or_send_mail(self):
        for field in ('name', 'email', 'message', 'csrf_token', 'website'):
            with self.subTest(field=field):
                fields = self.form()
                del fields[field]
                fields[field + '[]'] = 'unexpected'
                status, headers, _ = self.request('contact.php', fields)
                self.assertEqual((status, headers['Location']), (303, 'index.php#contact'))
                self.assertEqual(self.smtp.messages, [])

    def test_missing_or_wrong_token_never_sends_mail(self):
        for token in ('', 'wrong'):
            fields = self.form()
            fields['csrf_token'] = token
            status, headers, _ = self.request('contact.php', fields)
            self.assertEqual((status, headers['Location']), (303, 'index.php#contact'))
            self.assertEqual(self.smtp.messages, [])

    def test_honeypot_never_sends_mail(self):
        fields = self.form()
        fields['website'] = 'https://spam.example'
        self.request('contact.php', fields)
        self.assertEqual(self.smtp.messages, [])

    def test_errors_preserve_and_escape_values(self):
        fields = self.form()
        fields.update(name='<script>alert(1)</script>', email='bad', message='</textarea><script>alert(2)</script>')
        self.request('contact.php', fields)
        status, _, body = self.request('index.php')
        self.assertEqual(status, 200)
        self.assertIn('role="alert"', body)
        self.assertIn('&lt;script&gt;alert(1)&lt;/script&gt;', body)
        self.assertNotIn('<script>alert', body)

    def test_mail_failure_shows_error_without_success_redirect(self):
        fields = self.form()
        self.smtp.reject = True
        status, headers, _ = self.request('contact.php', fields)
        self.assertEqual((status, headers['Location']), (303, 'index.php#contact'))
        _, _, body = self.request('index.php')
        self.assertIn('contact@nimblyscout.com', body)
        self.assertIn('role="alert"', body)
        self.assertIn('Jane Scout', body)

    def test_get_handler_does_not_send_mail(self):
        status, headers, _ = self.request('contact.php')
        self.assertEqual(status, 405)
        self.assertEqual(headers['Allow'], 'POST')
        self.assertEqual(self.smtp.messages, [])


if __name__ == '__main__':
    unittest.main(verbosity=2)
