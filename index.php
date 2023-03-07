<?php

    require_once(__DIR__ . '/nostrpostr.php');

    // can have as many relays in here as you need
    $relays = [
        'wss://nostr.pleb.network',
        'wss://relay.damus.io',
        'wss://relay.snort.social',
        'wss://offchain.pub',
        'wss://nos.lol',
        'wss://brb.io',
    ];

    // a sample note to post. Include image URL etc here
    $content = 'Okay one more test posted at ' . time();

    // Can view the testing profile at https://coracle.social/people/npub1unckxgleat9ynw96jgh4jh7awwszp20dm62yqagm4hyf8s9qq25qh3na44/notes
    $privateKey = 'nsec1qqt4as5axgjw4c7ah377krtdc5y8jtau5lcrdsreutdnkrf8c95qrmm2d2';

    // create the NOSTR posting client
    $nostr = new NOSTRpostr($relays);
    
    // sign & send a note to NOSTR relays
    if ($nostr->send($privateKey, $content)) {
        // if we end up here it means the note was sent successfully to at least 1 relay
        print 'NOSTR success<br>';
    } else {
        // if we end up here it means sending failed to all attempted relays
        print 'NOSTR failed<br>';
    }

    // more output for the relay responses and any network level errors that occurred
    print sprintf(
        "<pre>RESPONSES:\n%s\n\n===============\n\nERRORS:\n%s", 
        json_encode($nostr->getResponses(), JSON_PRETTY_PRINT), 
        json_encode($nostr->getErrors(), JSON_PRETTY_PRINT)
    );

