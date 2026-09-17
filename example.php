<?php

require __DIR__ . '/vendor/autoload.php';

use Symbolist\Hetrixtools;

try {
    //bash
    //export HETRIXTOOLS_API_KEY=your-api-key
    //php example.php
    $api_key = getenv('HETRIXTOOLS_API_KEY');
    $hetrix = new Hetrixtools( $api_key );
    
    //$limits = $hetrix->getAccountLimits();
    //print_r($limits);

    $result = $hetrix->getBlacklistMonitors([
        'per_page' => 20,
        'page' => 1,
        'type' => 'ipv4',
        'listed' => true,
        'order_by' => 'last_check',
        'order' => 'desc',
    ]);
    
    foreach ($result['monitors'] ?? [] as $monitor) {
        //echo $monitor['name'] . ': ' . $monitor['target'] . 'listed('. count($monitor['listed']) .')' . PHP_EOL;
        echo $monitor['id'].':'.str_pad($monitor['target'], 20) . '--> listed('. count($monitor['listed']) .')' . PHP_EOL;
    }
}
catch(Exception $e){
    //var_dump($e);
    print "message(" . $e->getMessage() . ")\n";
    print "responseBody(" . json_encode($e->getResponseBody()) . ")\n";
}
