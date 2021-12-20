<?php 
return [ 
    'client_id' => 'AWDRVcr19pCaXiI5KsAvEIEByLRfbZS5edkAmz2kwF8tLi0UtJF2s0K31wK7AznlAZpaOcRNBHXp41N2',
    'secret' => 'EEr6WhdMSCpYWCUQ6bGQyL4BEBR714KwAAt6y2EH0p3EsvOk-YrHNAFxOyNt94zUhTjkd7PqCsU6vCpE',
    'settings' => array(
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];