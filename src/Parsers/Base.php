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

        $this->record->content = $response;
    }

    /*
     * Checks whether this is an incomplete response.
     *
     * This method is just a stub.
     * Define it in your parser class.
     *
     * @return bool
     */
    protected function isIncomplete()
    {
        return false;
    }

    /*
     * Checks whether this is a throttle response.
     *
     * This method is just a stub.
     * Define it in your parser class.
     *
     * @return bool
     */
    protected function isThrottled()
    {
        return false;
    }

    /*
     * Checks whether this response contains a message
     * that can be reconducted to a "WHOIS Server Unavailable" status.
     *
     * Some WHOIS servers returns error messages
     * when they are experiencing failures.
     *
     * This method is just a stub.
     * Define it in your parser class.
     *
     * @return bool
     */
    protected function isUnavailable()
    {
        return false;
    }

    public function parse()
    {
        $this->captureAllKv($this->record->content, $this->recordKv);

        $this->record->incomplete = $this->isIncomplete();

        $this->record->throttled = $this->isThrottled();

        $this->record->unavailable = $this->isUnavailable();
    }

    public function findBlock($header, $ending = '\n\n', $returnRaw = false)
    {
        $this->stringScanner->setSource($this->record->content);
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

                if (!$k || !$v) {
                    continue;
                }

                if (array_key_exists($k, $target)) {
                    if (!is_array($target[$k])) {
                        if (is_string($target[$k]) && is_string($v) && strtolower($target[$k]) === strtolower($v)) {
                            continue;
                        } elseif ($target[$k] === $v) {
                            continue;
                        }

                        $oldValue = $target[$k];

                        $target[$k] = [
                            $oldValue
                        ];
                    } elseif (in_arrayi($v, $target[$k])) {
                        continue;
                    }

                    $target[$k][] = $v;
                } else {
                    $target[$k] = $v;
                }
            }
        }
    }

    protected function valForKey($key, $defaultValue = '')
    {
        return array_get($this->recordKv, $key, $defaultValue);
    }

    protected function firstValIfArray($value)
    {
        return is_array($value) ? reset($value) : $value;
    }

    protected function arrayValIfSingle($value)
    {
        return !is_array($value) ? [$value] : $value;
    }
}
