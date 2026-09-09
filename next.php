<?php declare(strict_types=1);

use StepSecurity\Github\Actions\NextSemVers\Next;

require __DIR__ . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'autoload.php';

(function (): void {
    $eventPath   = getenv('GITHUB_EVENT_PATH') ?: '';
    $repoPrivate = null;
    if ($eventPath !== '' && file_exists($eventPath)) {
        $eventData = @json_decode((string) file_get_contents($eventPath), true);
        if (is_array($eventData) && array_key_exists('private', $eventData['repository'] ?? [])) {
            $repoPrivate = (bool) $eventData['repository']['private'];
        }
    }

    $upstream = 'wyrihaximus/github-action-next-semvers';
    $action   = getenv('GITHUB_ACTION_REPOSITORY') ?: '';
    $docsUrl  = 'https://docs.stepsecurity.io/actions/stepsecurity-maintained-actions';

    echo "\n";
    echo "\033[1;36mStepSecurity Maintained Action\033[0m\n";
    echo "Secure drop-in replacement for {$upstream}\n";
    if ($repoPrivate === false) {
        echo "\033[32m\u{2713} Free for public repositories\033[0m\n";
    }
    echo "\033[36mLearn more:\033[0m {$docsUrl}\n";
    echo "\n";

    if ($repoPrivate === false) {
        return;
    }

    $serverUrl = getenv('GITHUB_SERVER_URL') ?: 'https://github.com';
    $body      = ['action' => $action];
    if ($serverUrl !== 'https://github.com') {
        $body['ghes_server'] = $serverUrl;
    }

    $apiUrl = 'https://agent.api.stepsecurity.io/v1/github/'
        . (getenv('GITHUB_REPOSITORY') ?: '')
        . '/actions/maintained-actions-subscription';

    $ctx          = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'content'       => (string) json_encode($body),
            'timeout'       => 3,
            'ignore_errors' => true,
        ],
    ]);
    $responseBody = @file_get_contents($apiUrl, false, $ctx);

    if ($responseBody === false) {
        echo "Timeout or API not reachable. Continuing to next step.\n";
        return;
    }

    $statusLine = $http_response_header[0] ?? '';
    preg_match('/HTTP\/\S+\s+(\d+)/', $statusLine, $m);
    $statusCode = isset($m[1]) ? (int) $m[1] : 0;

    if ($statusCode === 403) {
        fwrite(STDERR, "::error::\033[1;31mThis action requires a StepSecurity subscription for private repositories.\033[0m\n");
        fwrite(STDERR, "::error::\033[31mLearn how to enable a subscription: {$docsUrl}\033[0m\n");
        exit(1);
    }
})();

exit((function (): int {
    $versionString = \getenv('INPUT_VERSION');

    if ($versionString === false) {
        return 1;
    }

    file_put_contents(getenv('GITHUB_OUTPUT'), Next::run($versionString, \getenv('INPUT_STRICT') === 'false' ? false : true), FILE_APPEND);

    return 0;
})());
