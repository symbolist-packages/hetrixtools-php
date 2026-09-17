<?php

namespace Symbolist;

use InvalidArgumentException;

class Hetrixtools
{
    /** @var HttpClient */
    private $httpClient;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.hetrixtools.com/v3/')
    {
        $this->httpClient = new HttpClient($apiKey, $baseUrl);
    }

    /** @return array<mixed> */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        return $this->httpClient->request($method, $path, $query, $body);
    }

    public function getAccountLimits(): array { return $this->request('GET', 'account/limits'); }
    public function getContactLists(array $query = []): array { return $this->request('GET', 'contact-lists', $query); }
    public function getBlacklists(array $query = []): array { return $this->request('GET', 'blacklists', $query); }
    public function getBlacklistMonitors(array $query = []): array { return $this->request('GET', 'blacklist-monitors', $query); }

    public function getBlacklistMonitorReport(string $identifier, array $query = []): array
    {
        return $this->request('GET', 'blacklist-monitors/' . $this->identifier($identifier) . '/report', $query);
    }

    public function getUptimeMonitors(array $query = []): array { return $this->request('GET', 'uptime-monitors', $query); }
    public function getUptimeMonitorReport(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'report', $query); }
    public function getUptimeMonitorDowntimes(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'downtimes', $query); }
    public function getUptimeMonitorLocationFailLog(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'location-fail-log', $query); }
    public function getUptimeMonitorNetworkDiagnostics(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'network-diagnostics', $query); }
    public function getUptimeMonitorWebSnapshot(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'web-snapshot', $query); }
    public function getUptimeMonitorPrivateNotes(string $monitorId): array { return $this->uptimeGet($monitorId, 'private-notes'); }
    public function createUptimeMonitorPrivateNotes(string $monitorId, array $body): array { return $this->uptimeWrite('POST', $monitorId, 'private-notes', $body); }
    public function updateUptimeMonitorPrivateNotes(string $monitorId, array $body): array { return $this->uptimeWrite('PUT', $monitorId, 'private-notes', $body); }
    public function deleteUptimeMonitorPrivateNotes(string $monitorId): array { return $this->uptimeWrite('DELETE', $monitorId, 'private-notes'); }
    public function getUptimeMonitorServerAgent(string $monitorId): array { return $this->uptimeGet($monitorId, 'server-agent'); }
    public function createUptimeMonitorServerAgent(string $monitorId): array { return $this->uptimeWrite('POST', $monitorId, 'server-agent'); }
    public function deleteUptimeMonitorServerAgent(string $monitorId): array { return $this->uptimeWrite('DELETE', $monitorId, 'server-agent'); }
    public function getUptimeMonitorServerAgentMetrics(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'server-agent/metrics', $query); }
    public function getUptimeMonitorServerAgentProcesses(string $monitorId, array $query = []): array { return $this->uptimeGet($monitorId, 'server-agent/processes', $query); }
    public function getUptimeMonitorServerAgentWarningPolicies(string $monitorId): array { return $this->uptimeGet($monitorId, 'server-agent/warning-policies'); }
    public function updateUptimeMonitorServerAgentWarningPolicies(string $monitorId, array $body): array { return $this->uptimeWrite('PUT', $monitorId, 'server-agent/warning-policies', $body); }
    public function getStatusPages(array $query = []): array { return $this->request('GET', 'status-pages', $query); }

    public function addStatusPageMonitors(string $statusPageId, array $body): array
    {
        return $this->request('POST', 'status-pages/' . $this->identifier($statusPageId) . '/monitors', [], $body);
    }

    public function removeStatusPageMonitors(string $statusPageId, array $body): array
    {
        return $this->request('DELETE', 'status-pages/' . $this->identifier($statusPageId) . '/monitors', [], $body);
    }

    public function getScheduledMaintenances(array $query = []): array { return $this->request('GET', 'schedule-maintenance', $query); }
    public function createScheduledMaintenance(array $body): array { return $this->request('POST', 'schedule-maintenance', [], $body); }
    public function deleteScheduledMaintenance(string $id): array { return $this->request('DELETE', 'schedule-maintenance/' . $this->identifier($id)); }

    private function uptimeGet(string $monitorId, string $suffix, array $query = []): array
    {
        return $this->request('GET', 'uptime-monitors/' . $this->identifier($monitorId) . '/' . $suffix, $query);
    }

    private function uptimeWrite(string $method, string $monitorId, string $suffix, ?array $body = null): array
    {
        return $this->request($method, 'uptime-monitors/' . $this->identifier($monitorId) . '/' . $suffix, [], $body);
    }

    private function identifier(string $identifier): string
    {
        if ($identifier === '') {
            throw new InvalidArgumentException('Identifier must not be empty.');
        }

        return rawurlencode($identifier);
    }
}
