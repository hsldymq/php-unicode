<?php

(new class () {
    private $args;

    private $fp;

    public function __construct()
    {
        global $argv;

        $this->args = $argv;
        array_shift($this->args);
        $this->fp = fopen($this->args[0], 'r');
    }

    public function run()
    {
        $result = [];

        while ($line = fgets($this->fp)) {
            $parsed = $this->parse($line);
            if (!$parsed) {
                continue;
            }

            $ref = &$result;
            for ($i = 0, $len = count($parsed); $i < $len; $i++) {
                if (!isset($ref[$parsed[$i]])) {
                    $ref[$parsed[$i]] = [];
                }

                if ($i === $len - 1) {
                    $ref[$parsed[$i]][''] = true;
                }

                $refTmp = &$ref;
                unset($ref);
                $ref = &$refTmp[$parsed[$i]];
                unset($refTmp);
            }
            unset($ref);
        }

        $this->output($result);
    }

    private function output(array $result)
    {
        echo "<?php\n\n";
        echo "return [\n";
        $this->outputFields($result, 1);
        echo "];\n";
    }

    private function outputFields(array $result, int $indentLevel)
    {
        $indentStr = str_repeat(" ", $indentLevel * 4);
        foreach ($result as $key => $value) {
            if ($key !== '') {
                $key = sprintf("%04X", hexdec($key));
            }

            if (is_array($value)) {
                echo "{$indentStr}'{$key}' => [\n";
                $this->outputFields($value, $indentLevel + 1);
                echo "{$indentStr}],\n";
            } else {
                echo "{$indentStr}'{$key}' => true,\n";
            }
        }
    }

    private function parse(string $line)
    {
        $line = trim($line);
        if (!$line || $line[0] === '#') {
            return null;
        }

        // 去掉注释
        $line = explode('#', $line)[0];
        list($codePoint, $status) = explode(';', $line);
        list($codePoint, $status) = [trim($codePoint), trim($status)];

        if (!in_array($status, ['fully-qualified', 'minimally-qualified'])) {
            return null;
        }

        return explode(' ', $codePoint);
    }
})->run();