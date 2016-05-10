<?php

$count = 1;
$phone_nos = 0;
$email_nos = 0;
$skipped = 0;

$contacts_string = file_get_contents("../../uploads/1.txt");

$contacts = explode("\n", $contacts_string);

foreach ($contacts as $contact) {
    $details = explode("|", $contact);

    if (is_array($details)) {
        //echo "S No: " . $count++ . PHP_EOL;
        //echo "Name: " . $details[0] . PHP_EOL;
        //echo "Phone: ";

        $phones = explode(",", $details[1]);
        foreach ($phones as $phone) {
            echo $phone . PHP_EOL;
            $phone_nos++;
        }
        //echo PHP_EOL;
        //echo "Email: ";

        /*
          $emails = explode(",", $details[2]);
          foreach ($emails as $email) {
          echo $email . PHP_EOL;
          $email_nos++;
          }
          echo PHP_EOL;
         *
         */
    } else
        $skipped++;

}

//print_r("Contacts: " . count($contacts) . ", Phones: $phone_nos, Emails: $email_nos");
?>