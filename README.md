# HetrixTools PHP SDK

A beginner-friendly, lightweight PHP client for the [HetrixTools API v3](https://docs.hetrixtools.com/api/v3/). It uses PHP's cURL extension directly and has no third-party runtime dependencies.

## Requirements

- PHP 7.2 or higher
- PHP cURL and JSON extensions
- A HetrixTools API key from the [API access page](https://hetrixtools.com/dashboard/account/api/)

## Installation

Install the package with [Composer](https://getcomposer.org/):

```bash
composer require symbolist/hetrixtools-php
```

Then include Composer's autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Symbolist\Hetrixtools;

$hetrix = new Hetrixtools('your-api-key-here');

$limits = $hetrix->getAccountLimits();
print_r($limits);
```

The client sends the key as a Bearer token. Responses are decoded to associative arrays.

## Blacklist monitor examples

### GET `/blacklist-monitors`

Pass any supported query parameters in an associative array. This example requests the first 20 listed IPv4 monitors:

```php
use Symbolist\Hetrixtools;

$hetrix = new Hetrixtools('your-api-key-here');

$result = $hetrix->getBlacklistMonitors([
    'per_page' => 20,
    'page' => 1,
    'type' => 'ipv4',
    'listed' => true,
    'order_by' => 'last_check',
    'order' => 'desc',
]);

foreach ($result['monitors'] ?? [] as $monitor) {
    echo $monitor['name'] . ': ' . $monitor['target'] . PHP_EOL;
}
```

Supported filters are `per_page`, `page`, `name`, `exact_name`, `target`, `exact_target`, `cidr`, `type`, `listed`, `order`, and `order_by`.

### GET `/blacklist-monitors/{identifier}/report`

The identifier may be a monitor ID, IP address, or hostname. The optional report date uses `YYYY-MM-DD` format:

```php
use Symbolist\Hetrixtools;

$hetrix = new Hetrixtools('your-api-key-here');

$report = $hetrix->getBlacklistMonitorReport('203.0.113.1', [
    'date' => '2026-09-17',
]);

echo $report['target'] . PHP_EOL;
foreach ($report['listed'] ?? [] as $listing) {
    echo $listing['rbl'] . ': ' . $listing['delist'] . PHP_EOL;
}
```

## API coverage

The named methods correspond to every operation in the repository's `api.yaml` specification:

| Feature | Client method | Endpoint |
| --- | --- | --- |
| Account limits | `getAccountLimits()` | `GET /account/limits` |
| Contact lists | `getContactLists($query)` | `GET /contact-lists` |
| Blacklists | `getBlacklists($query)` | `GET /blacklists` |
| Blacklist monitors | `getBlacklistMonitors($query)` | `GET /blacklist-monitors` |
| Blacklist report | `getBlacklistMonitorReport($identifier, $query)` | `GET /blacklist-monitors/{identifier}/report` |
| Uptime monitors | `getUptimeMonitors($query)` | `GET /uptime-monitors` |
| Uptime report | `getUptimeMonitorReport($monitorId, $query)` | `GET /uptime-monitors/{monitor_id}/report` |
| Downtimes | `getUptimeMonitorDowntimes($monitorId, $query)` | `GET /uptime-monitors/{monitor_id}/downtimes` |
| Location fail log | `getUptimeMonitorLocationFailLog($monitorId, $query)` | `GET /uptime-monitors/{monitor_id}/location-fail-log` |
| Network diagnostics | `getUptimeMonitorNetworkDiagnostics($monitorId, $query)` | `GET /uptime-monitors/{monitor_id}/network-diagnostics` |
| Web snapshot | `getUptimeMonitorWebSnapshot($monitorId, $query)` | `GET /uptime-monitors/{monitor_id}/web-snapshot` |
| Private notes | `get…`, `create…`, `update…`, `deleteUptimeMonitorPrivateNotes()` | `/uptime-monitors/{monitor_id}/private-notes` |
| Server agent | `get…`, `create…`, `deleteUptimeMonitorServerAgent()` | `/uptime-monitors/{monitor_id}/server-agent` |
| Server metrics | `getUptimeMonitorServerAgentMetrics($monitorId, $query)` | `GET …/server-agent/metrics` |
| Running processes | `getUptimeMonitorServerAgentProcesses($monitorId, $query)` | `GET …/server-agent/processes` |
| Warning policies | `get…`, `updateUptimeMonitorServerAgentWarningPolicies()` | `…/server-agent/warning-policies` |
| Status pages | `getStatusPages($query)` | `GET /status-pages` |
| Status page monitors | `addStatusPageMonitors()`, `removeStatusPageMonitors()` | `POST`, `DELETE /status-pages/{status_page_id}/monitors` |
| Scheduled maintenance | `getScheduledMaintenances()`, `createScheduledMaintenance()`, `deleteScheduledMaintenance()` | `/schedule-maintenance` |

Query parameters and request payloads are passed through as arrays, keeping the SDK compatible when HetrixTools adds optional fields. For an endpoint without a named method, use `request($method, $path, $query, $body)`.

## Handling errors

Transport errors, invalid JSON, and non-2xx responses throw `Symbolist\Exceptions\ApiException`. The exception exposes the HTTP status and decoded response body:

```php
use Symbolist\Exceptions\ApiException;

try {
    $report = $hetrix->getBlacklistMonitorReport('example.com');
} catch (ApiException $exception) {
    echo 'Status: ' . $exception->getStatusCode() . PHP_EOL;
    print_r($exception->getResponseBody());
}
```

## Custom API URL

For a proxy or test server, pass a base URL as the second constructor argument:

```php
$hetrix = new Hetrixtools('your-api-key-here', 'https://example.test/v3/');
```

## Contributing

1. Fork the repository and create a feature branch.
2. Run `composer validate` and lint the PHP files.
3. Submit a pull request describing the change.

## License

This project is available under the [MIT License](LICENSE).
