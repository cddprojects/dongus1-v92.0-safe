<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Please submit the contact form from the NimblyScout website.');
}

require __DIR__ . '/form-session.php';

function postedText(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function textLength(string $value): int
{
    $length = preg_match_all('/./us', $value);
    return $length === false ? PHP_INT_MAX : $length;
}

function returnToForm(array $errors, array $values): never
{
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_values'] = $values;
    header('Location: index.php#contact', true, 303);
    exit;
}

$values = [
    'name' => postedText('name'),
    'email' => postedText('email'),
    'message' => postedText('message'),
];

$token = postedText('csrf_token');
if ($token === '' || !hash_equals($_SESSION['csrf_token'], $token)) {
    returnToForm(['Your form session expired. Please try sending your message again.'], $values);
}
if (!is_string($_POST['website'] ?? '') || postedText('website') !== '') {
    returnToForm(['Please leave the website field empty and try again.'], $values);
}

$errors = [];
if ($values['name'] === '' || textLength($values['name']) > 100 || preg_match('/[\r\n\x00]/', $values['name'])) {
    $errors[] = 'Enter your name using 100 characters or fewer on a single line.';
}
if (strlen($values['email']) > 254 || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)
    || preg_match('/[\r\n\x00]/', $values['email'])) {
    $errors[] = 'Enter a valid email address so we can reply.';
}
if (!is_string($_POST['message'] ?? '') || textLength($values['message']) > 3000 || str_contains($values['message'], "\0")) {
    $errors[] = 'Keep your message to 3,000 characters or fewer.';
}
if ($errors) {
    returnToForm($errors, $values);
}

$recipient = 'contact@nimblyscout.com';
$body = "New NimblyScout inquiry\n\nName: {$values['name']}\nEmail: {$values['email']}\n\nMessage:\n"
    . ($values['message'] !== '' ? $values['message'] : '(No message provided)');
$headers = [
    'From' => 'NimblyScout <contact@nimblyscout.com>',
    'Reply-To' => $values['email'],
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8',
    'Content-Transfer-Encoding' => 'base64',
];

// The host must configure PHP mail transport for the NimblyScout domain.
$sent = function_exists('mail') && @mail(
    $recipient,
    'New inquiry | NimblyScout',
    chunk_split(base64_encode($body)),
    $headers
);
if (!$sent) {
    error_log('NimblyScout contact: mail transport did not accept the inquiry.');
    returnToForm(['We couldn’t send your message right now. Please try again or email contact@nimblyscout.com directly.'], $values);
}

unset($_SESSION['form_errors'], $_SESSION['form_values']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header('Location: thank-you/index.html', true, 303);
exit;
