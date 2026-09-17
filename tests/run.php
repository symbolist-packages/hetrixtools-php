<?php

require __DIR__ . '/../src/Exceptions/ApiException.php';
require __DIR__ . '/../src/HttpClient.php';
require __DIR__ . '/../src/Hetrixtools.php';

use Symbolist\Exceptions\ApiException;
use Symbolist\Hetrixtools;

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$port = 18080 + random_int(0, 999);
$command = sprintf('php -S 127.0.0.1:%d %s >/dev/null 2>&1', $port, escapeshellarg(__DIR__ . '/router.php'));
$pipes = [];
$process = proc_open($command, [], $pipes);
if (!is_resource($process)) {
    fwrite(STDERR, "Unable to start test server.\n");
    exit(1);
}

usleep(300000);

try {
    $client = new Hetrixtools('test-key', sprintf('http://127.0.0.1:%d/v3/', $port));
    $response = $client->getBlacklistMonitors(['listed' => true, 'unused' => null]);
    assertSame('GET', $response['method'], 'GET method was not sent.');
    assertSame('/v3/blacklist-monitors', $response['path'], 'List path is incorrect.');
    assertSame('1', $response['query']['listed'], 'Query was not encoded.');
    assertSame(false, isset($response['query']['unused']), 'Null query value was not removed.');

    $response = $client->getBlacklistMonitorReport('example.com', ['date' => '2026-09-17']);
    assertSame('/v3/blacklist-monitors/example.com/report', $response['path'], 'Report path is incorrect.');
    assertSame('2026-09-17', $response['query']['date'], 'Report date is incorrect.');

    $response = $client->addStatusPageMonitors('page/id', ['monitor_ids' => ['abc']]);
    assertSame('/v3/status-pages/page%2Fid/monitors', $response['path'], 'Identifier was not encoded.');
    assertSame(['monitor_ids' => ['abc']], $response['body'], 'JSON body is incorrect.');

    try {
        $client->request('GET', 'error');
        throw new RuntimeException('Expected ApiException was not thrown.');
    } catch (ApiException $exception) {
        assertSame(422, $exception->getStatusCode(), 'Error status is incorrect.');
        assertSame(['message' => 'bad input'], $exception->getResponseBody(), 'Error body is incorrect.');
    }
} finally {
    proc_terminate($process);
    proc_close($process);
}

echo "All tests passed.\n";
