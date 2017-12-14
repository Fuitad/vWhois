<?php

namespace vWhois\Parsers;

use vWhois\Record;
use StrScan\StringScanner;

class Base
{
    public $record;

    protected $recordKv = [];
    protected $stringScanner;

    public function __construct($response, $host)
    {
        $this->stringScanner = new StringScanner;
        $this->record = new Record();

        $this->record->raw = $response;
        $this->record->server = $host;
    }

    public function parse()
    {
        $this->captureAllKv($this->record->raw, $this->recordKv);
    }

    public function findBlock($header, $ending = '\n\n', $returnRaw = false)
    {
        $this->stringScanner->setSource($this->record->raw);
        $findStart = $this->stringScanner->scanUntil("/${header}/");

        if (!$findStart) {
            return false;
        }

        $blockText = $this->stringScanner->scanUntil("/${ending}/");

        if (!$blockText || $returnRaw) {
            return $blockText;
        } else {
            $blockKv = [];
            $this->captureAllKv($blockText, $blockKv);
            return $blockKv;
        }
    }

    protected function captureAllKv($source, &$target)
    {
        if (preg_match_all('/(?<key>.+?):\n?(?<value>.*?)(\n|\z)/', $source, $recordKv, PREG_SET_ORDER)) {
            foreach ($recordKv as $record) {
                $k = trim($record['key']);
                $v = trim($record['value']);

                if (array_key_exists($k, $target)) {
                    if (!is_array($target[$k])) {
                        $oldValue = $target[$k];

                        $target[$k] = [
                            $oldValue
                        ];
                    }

                    $target[$k][] = $v;
                } else {
                    $target[$k] = $v;
                }
            }
        }
    }

    protected function valForKey($key)
    {
        return array_get($this->recordKv, $key, '');
    }
}
