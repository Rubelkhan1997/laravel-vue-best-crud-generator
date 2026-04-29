<?php
$p = 'src/Commands/MakeCrudModule.php';
$c = file_get_contents($p);
$pattern = '/protected function collectFields\(\): void\n\s*\{[\s\S]*?\n\s*\}\n\n\s*protected function collectRelationships\(\): void/';
$replacement = <<<'PHP'
protected function collectFields(): void
    {
        info("\n?? Define your database fields:");
        info("Format: field_name:type:constraints");
        info("Use comma or new line between fields");
        info("Example: name:string:required, email:string:nullable:unique");
        info("If third parameter is missing, it will NOT be required by default");
        info("Type 'done' when finished\n");

        while (true) {
            $input = text(
                label: count($this->fields) === 0 ? 'Fields (single/bulk)' : 'More fields',
                required: false,
                hint: 'Format: field_name:type:constraints (or done). Supports comma/newline list.'
            );

            if (strtolower($input) === 'done' || empty($input)) {
                break;
            }

            foreach ($this->parseFieldDefinitions($input) as $field) {
                if (in_array($field['name'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    info("Skipped reserved field: {$field['name']}");
                    continue;
                }

                $this->fields[] = $field;
                info("Added: {$field['name']}");
            }
        }

        // Add timestamps
        $this->fields[] = ['name' => 'created_at', 'type' => 'timestamp', 'constraints' => ''];
        $this->fields[] = ['name' => 'updated_at', 'type' => 'timestamp', 'constraints' => ''];

        if ($this->softDeletes) {
            $this->fields[] = ['name' => 'deleted_at', 'type' => 'timestamp', 'constraints' => 'nullable'];
        }
    }

    protected function parseFieldDefinitions(string $input): array
    {
        $rawItems = preg_split('/[\r\n,]+/', $input) ?: [];
        $fields = [];

        foreach ($rawItems as $rawItem) {
            $line = trim($rawItem);
            if ($line === '' || strtolower($line) === 'done') {
                continue;
            }

            $parts = array_map('trim', explode(':', $line));
            $fieldName = $parts[0] ?? '';
            if ($fieldName === '') {
                continue;
            }

            $fieldType = $parts[1] ?? 'string';
            $constraints = $this->normalizeConstraints(array_slice($parts, 2));

            $fields[] = [
                'name' => $fieldName,
                'type' => $fieldType,
                'constraints' => implode(':', $constraints),
                'nullable' => in_array('nullable', $constraints, true),
                'required' => in_array('required', $constraints, true),
                'unique' => in_array('unique', $constraints, true),
            ];
        }

        return $fields;
    }

    protected function normalizeConstraints(array $parts): array
    {
        if (empty($parts)) {
            return [];
        }

        $tokens = preg_split('/[|:,]+/', implode(':', $parts)) ?: [];

        return collect($tokens)
            ->map(fn($token) => strtolower(trim($token)))
            ->filter(fn($token) => $token !== '')
            ->unique()
            ->values()
            ->all();
    }

    protected function collectRelationships(): void
PHP;

$n = preg_replace($pattern, $replacement, $c, 1, $count);
if ($count !== 1) {
    fwrite(STDERR, "replace failed\n");
    exit(1);
}

$n = str_replace("label: 'Generate all files?',\n            default: true", "label: 'Generate all files?',\n            default: false", $n, $count2);

file_put_contents($p, $n);
