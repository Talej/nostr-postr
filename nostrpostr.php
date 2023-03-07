<?php

    require_once(__DIR__ . '/vendor/autoload.php');

    class NOSTRpostr {
        protected $keys;
        protected $signer;
        protected $responses = [];
        protected $errors = [];

        function __construct($relays) {
            $this->relays = $relays;
            $this->keys = new swentel\nostr\Keys();
            $this->signer = new swentel\nostr\Sign();
        }

        public function send($privateKeyBech32, $note) {
            $privateKey = $this->keys->convertToHex($privateKeyBech32);
            $publicKey = $this->keys->getPublicKey($privateKey);

            $event = [
                'pubkey' => $publicKey,
                'created_at' => time(),
                'kind' => 1,
                'tags' => [],
                'content' => $note,
            ];

            $event = $this->signer->signEvent($event, $privateKey);
            $message = $this->signer->generateEvent($event);

            $result = FALSE;
            foreach ($this->relays as $relay) {
                try {
                    $client = new WebSocket\Client($relay);
                    $client->text($message);
                    $response = $client->receive();

                    // succeeded posting to at least 1 relay
                    if (preg_match('/^\["OK/', $response)) $result = TRUE;

                    $this->responses[] = [
                        'relay' => $relay,
                        'event' => $event,
                        'message' => $response
                    ];
                    
                    $client->close();
                } catch (Exception $e) {
                    $this->errors[] = [
                        'relay'   => $relay,
                        'event'    => $event,
                        'message' => $e
                    ];
                }
            }

            return $result;
        }

        public function getErrors() {
            $errors = $this->errors;
            $this->errors = [];
            return $errors;
        }

        public function getResponses() {
            $responses = $this->responses;
            $this->responses = [];
            return $responses;
        }
    }
