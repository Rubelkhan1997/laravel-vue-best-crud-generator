<?php
$p = 'src/Commands/MakeCrudModule.php';
$c = file_get_contents($p);

$updatePattern = '/protected function updateRoutes\(\): void\n\s*\{[\s\S]*?\n\s*\}/';
$updateReplacement = <<<'PHP'
protected function updateRoutes(): void
    {
        $apiPath = base_path('routes/api.php');
        $webPath = base_path('routes/web.php');

        $apiUpdated = $this->appendRouteSnippetToFile($apiPath, $this->getApiRouteSnippet(), "api.{$this->routeName}.");
        $webUpdated = $this->appendRouteSnippetToFile($webPath, $this->getWebRouteSnippet(), "{$this->routeName}.");

        if ($apiUpdated) {
            $this->info("API routes added to routes/api.php");
        } else {
            $this->info("Skipped routes/api.php (already exists)");
        }

        if ($webUpdated) {
            $this->info("Web routes added to routes/web.php");
        } else {
            $this->info("Skipped routes/web.php (already exists)");
        }
    }

    protected function appendRouteSnippetToFile(string $filePath, string $snippet, string $routeNameNeedle): bool
    {
        if (!File::exists($filePath)) {
            return false;
        }

        $content = File::get($filePath);
        if (str_contains($content, "->name('{$routeNameNeedle}')")) {
            return false;
        }

        [$useLine, $routeBlock] = $this->splitRouteSnippet($snippet);

        if ($useLine !== '' && !str_contains($content, $useLine)) {
            $content .= "\n" . $useLine . "\n";
        }

        $content .= "\n" . $routeBlock . "\n";
        File::put($filePath, $content);

        return true;
    }

    protected function splitRouteSnippet(string $snippet): array
    {
        $parts = preg_split("/\n\s*\n/", trim($snippet), 2) ?: [];
        $useLine = trim($parts[0] ?? '');
        $routeBlock = trim($parts[1] ?? '');

        if (!str_starts_with($useLine, 'use ') || $routeBlock === '') {
            return ['', trim($snippet)];
        }

        return [$useLine, $routeBlock];
    }
PHP;

$n = preg_replace($updatePattern, $updateReplacement, $c, 1, $count1);
if ($count1 !== 1) {
    fwrite(STDERR, "updateRoutes replacement failed\n");
    exit(1);
}

$n = preg_replace("/info\(\"\s*3\. Add routes to routes\/api\.php and routes\/web\.php\"\);/", "info(\"   3. Routes already added to routes/api.php and routes/web.php\");", $n, 1, $count2);

file_put_contents($p, $n);
