<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    $code = random_int(100000, 999999);
    return strval($code);
}

function sendHtmlEmail(string $email, string $subject, string $htmlBody): bool {
    ini_set("SMTP", "localhost");
    ini_set("smtp_port", "1025");

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com" . "\r\n";

    return mail($email, $subject, $htmlBody, $headers);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    $message = "<p>Your verification code is: <strong>{$code}</strong></p>";
    $codeFile = __DIR__ . "/codes/{$email}.txt";
    file_put_contents($codeFile, $code);
    return sendHtmlEmail($email, 'Your Verification Code', $message);
}


// Custom helper usage for unsubscribe
function sendUnsubscribeVerificationEmail(string $email, string $code): bool {
    $message = "<p>To confirm un-subscription, use this code: <strong>{$code}</strong></p>";
    $codeFile = __DIR__ . "/codes/{$email}.txt";
    file_put_contents($codeFile, $code);
    return sendHtmlEmail($email, 'Confirm Un-subscription', $message);
}

/**
 * Verify the code sent to the email.
 */
function verifyCode($email, $code) : bool {
    $codeFile = __DIR__ . "/codes/{$email}.txt";

    if (!file_exists($codeFile)) {
        return false; // Code file doesn't exist
    }

    $savedCode = trim(file_get_contents($codeFile));

    if ($savedCode === $code) {
        // Delete the code file after successful verification
        unlink($codeFile);
        return true;
    }

    return false;
}


/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';

    // If file doesn't exist, create it
    if (!file_exists($file)) {
        file_put_contents($file, '');
    }

    // Read existing emails
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // If already registered, return false
    if (in_array($email, $emails)) {
        return false;
    }

    // Append new email
    $result = file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    
    return $result !== false;
}


/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';

    if (!file_exists($file)) {
        return false;
    }

    // Read current list of emails
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Check if email is in the list
    if (!in_array($email, $emails)) {
        return false; // Email not found
    }

    // Remove the email
    $updatedEmails = array_filter($emails, fn($e) => $e !== $email);

    // Overwrite the file with updated list
    $result = file_put_contents($file, implode(PHP_EOL, $updatedEmails) . PHP_EOL);

    return $result !== false;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string {
    // Step 1: Get latest XKCD comic ID
    $latestComicJson = file_get_contents("https://xkcd.com/info.0.json");
    if (!$latestComicJson) return '';

    $latestComic = json_decode($latestComicJson, true);
    $maxId = $latestComic['num'];

    // Step 2: Get random comic ID between 1 and latest
    $randomId = random_int(1, $maxId);

    // Step 3: Fetch random comic data
    $comicJson = file_get_contents("https://xkcd.com/{$randomId}/info.0.json");
    if (!$comicJson) return '';

    $comic = json_decode($comicJson, true);
    $imgUrl = $comic['img'];


    // Step 4: Format HTML
    $html = "<h2>XKCD Comic</h2>";
    $html .= "<img src=\"{$imgUrl}\" alt=\"XKCD Comic\">";
    $html .= "<p><a href=\"#\" id=\"unsubscribe-button\">Unsubscribe</a></p>";

    return $html;
}


/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';

    ini_set("SMTP", "localhost");
    ini_set("smtp_port", "1025");

    if (!file_exists($file)) {
        return;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) {
        return;
    }

    $comicHtml = fetchAndFormatXKCDData();
    if (empty($comicHtml)) {
        return;
    }

    // Mail headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com" . "\r\n";

    foreach ($emails as $email) {
        mail($email, "Your XKCD Comic", $comicHtml, $headers);
    }
}


// function test():void{
//   $userMail = 'aryanpatil@gmail.com';
//   $code = generateVerificationCode();
//   if (sendVerificationEmail($userMail, $code)){
//       echo "Verification code sent successfully.";
//   } else {
//       echo "Failed to send verification code.";
//   }

//   if (verifyCode($userMail, $code)) {
//       echo "<br> Code verified successfully.";
//       if(registerEmail($userMail)) {
//           echo "<br> Email registered successfully.";
//       } else {
//           echo "<br> Email already registered.";
//       }
//   } else {
//       echo "<br> Code verification failed.";
//   }
// }

// test();
// if(unsubscribeEmail('harsh2504patil@gmail.com')) {
//     echo "<br> Email unsubscribed successfully.";
// } else {
//     echo "<br> Email not found or already unsubscribed.";
// }

// echo fetchAndFormatXKCDData();
// sendXKCDUpdatesToSubscribers();

?>