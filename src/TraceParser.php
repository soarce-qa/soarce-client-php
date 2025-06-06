<?php

namespace Soarce;

class TraceParser
{
    /** @var array[] temporary stack - will hold metadata of "open" functions */
    private array $parseStack    = [];

    /** @var array[] end result that will be delivered to the service - one entry per function, listing type, number of calls and sum of walltime */
    private array $parsedData    = [];

    /** @var int[]   internal function number index that assigns an ID for every function */
    private array $functionIndex = [];

    /** @var int[][] functionId based map of what function calls which function how often: [caller][callee] => calls */
    private array $functionMap   = [];

    /**
     * @param  resource $fp
     */
    public function analyze($fp): void
    {
        while (false !== ($line = fgets($fp))) {
            $split = explode("\t", $line);

            if (count($split) > 9) {
                if ('' === $split[5]) {  // functionName
                    continue;
                }

                if (!isset($this->functionIndex[$split[5]])) {
                    $this->functionIndex[$split[5]] = count($this->functionIndex);
                }

                $this->parseStack[$split[1]] = [
                    'start'        => $split[3],
                    'functionName' => $split[5],
                    'number'       => $this->functionIndex[$split[5]],
                    'type'         => $split[6],
                    'file'         => $split[8],
                ];

                if (count($this->parseStack) >= 2) {
                    $slice = array_slice($this->parseStack, count($this->parseStack)-2, 2);
                    $calleeId = array_pop($slice)['number'];
                    $callerId = array_pop($slice)['number'];

                    if (!isset($this->functionMap[$callerId])) {
                        $this->functionMap[$callerId] = [];
                    }
                    if (!isset($this->functionMap[$callerId][$calleeId])) {
                        $this->functionMap[$callerId][$calleeId] = 0;
                    }
                    $this->functionMap[$callerId][$calleeId]++;
                }

                continue;
            }

            if (count($split) >= 5) {
                if (! isset($this->parseStack[$split[1]])) {
                    continue;
                }

                $info = $this->parseStack[$split[1]];
                unset($this->parseStack[$split[1]]);

                if (!isset($this->parsedData[$info['file']])) {
                    $this->parsedData[$info['file']] = [];
                }

                if (!isset($this->parsedData[$info['file']][$info['functionName']])) {
                    $this->parsedData[$info['file']][$info['functionName']] = [
                        'type'     => $info['type'],
                        'count'    => 1,
                        'walltime' => (float)$split[3] - (float)$info['start'],
                        'number'   => $info['number'],
                    ];
                } else {
                    $this->parsedData[$info['file']][$info['functionName']]['count']++;
                    $this->parsedData[$info['file']][$info['functionName']]['walltime'] += ((float)$split[3] - (float)$info['start']);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getParsedData(): array
    {
        return $this->parsedData;
    }

    /**
     * @return array
     */
    public function getFunctionMap(): array
    {
        return $this->functionMap;
    }
}