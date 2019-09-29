<?php

/**
 * Machine:  i9 9900K, 64GB RAM, Xubuntu 18.04, PHP 7.2
 * Sample trace file: 5MB, 71810 lines
 *
 * Runs in: 24.608 seconds
 */

class TraceParser3
{
    /** @var array[] temporary stack - will hold metadata of "open" functions */
    private $parseStack    = array();

    /** @var array[] end result that will be delivered to the service - one entry per function, listing type, number of calls and sum of walltime */
    private $parsedData    = array();

    /** @var int[]   internal function number index that assigns an ID for every function */
    private $functionIndex = array();

    /** @var int[][] functionId based map of what function calls which function how often: [caller][callee] => calls */
    private $functionMap   = array();

    /**
     * @param  resource $fp
     */
    public function analyze($fp)
    {
        while (false !== ($split = fgetcsv($fp, 0, "\t"))) {

            if (count($split) > 9) {
                if ('' === $split[5]) {  // functionName
                    continue;
                }

                if (!isset($this->functionIndex[$split[5]])) {
                    $this->functionIndex[$split[5]] = count($this->functionIndex);
                }

                $this->parseStack[$split[1]] = array(
                    'start'        => $split[3],
                    'functionName' => $split[5],
                    'number'       => $this->functionIndex[$split[5]],
                    'type'         => $split[6],
                    'file'         => $split[7],
                );

                if (count($this->parseStack) >= 2) {
                    $slice = array_slice($this->parseStack, count($this->parseStack)-2, 2);
                    $calleeLine = array_pop($slice);
                    $calleeId = $calleeLine['number'];

                    $callerLine = array_pop($slice);
                    $callerId = $callerLine['number'];

                    if (!isset($this->functionMap[$callerId])) {
                        $this->functionMap[$callerId] = array();
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
                    $this->parsedData[$info['file']] = array();
                }

                if (!isset($this->parsedData[$info['file']][$info['functionName']])) {
                    $this->parsedData[$info['file']][$info['functionName']] = array(
                        'type'     => $info['type'],
                        'count'    => 1,
                        'walltime' => (float)$split[3] - (float)$info['start'],
                        'number'   => $info['number'],
                    );
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
    public function getParsedData()
    {
        return $this->parsedData;
    }

    /**
     * @return array
     */
    public function getFunctionMap()
    {
        return $this->functionMap;
    }
}


$start = microtime(true);

for ($i = 0; $i < 100; $i++) {
    $fp = fopen('../PhpUnit_UnitTests/Fixtures/long-trace.xt', 'rb');
    $traceParser = new TraceParser3();
    $traceParser->analyze($fp);
}

echo microtime(true) - $start;
echo "\n";
