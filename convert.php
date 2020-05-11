<?php


class LegoStlConverter {

    const STL_PATH = './legostl/';
    const DAT_PATH = './ldrawlib/parts/';
    const NUM_FILES_PER_CALL = 10000;

    // all interesting dat files (without extension)
    protected $datArr = [];

    // existing stl files (without extension)
    protected $stlArr = [];

    protected function buildDatArray() {
        $datArr = glob($this::DAT_PATH."*.dat");
        // only with digits ./ldrawlib/parts/81379.dat
        $regex = '%'.$this::DAT_PATH.'(?P<partid>\d+)\.dat%i';
        foreach ($datArr as $filename) {
            if (preg_match($regex, $filename, $regs)) {
                if (!in_array($regs['partid'],$this->stlArr)) {
                    $this->datArr[] = $regs['partid'];
                }
            }
        }
        echo 'found '.count($this->datArr).' dat files to convert'.PHP_EOL;
        return true;
    }

    protected function buildStlArray() {
        $stlFilesArr = glob($this::STL_PATH."*.stl");
        // all stl files
        $regex = '%'.$this::STL_PATH.'(?P<filename>.*?)\.stl%i';
        foreach ($stlFilesArr as $filename) {
            if (preg_match($regex, $filename, $regs) && filesize($filename) > 0) {
                $this->stlArr[] = $regs['filename'];
            }
        }
        echo 'found '.count($this->stlArr).' stl files'.PHP_EOL;
        return true;
    }

    protected function convert() {
        if (empty($this->datArr)) {
            echo 'no dat files found to convert'.PHP_EOL;
            return false;
        }
        $counter = 0;
        foreach ($this->datArr AS $filename) {
            $counter++;
            $fullpath = $this::DAT_PATH.$filename.'.dat';
            $execCommand = 'perl bin/dat2stl --file '.$fullpath.' --ldrawdir ./ldrawlib --scale 1 > '.$this::STL_PATH.$filename.'.stl';
            echo 'executing: '.$execCommand.PHP_EOL;
            $result =  exec($execCommand);
            echo 'result: '.$result.PHP_EOL;
            if ($counter >= $this::NUM_FILES_PER_CALL) {
                break;
            }
        }
        echo 'converted '.$counter.' dat files'.PHP_EOL;
    }

    public function start() {
        echo 'starting'.PHP_EOL;
        $this->buildStlArray();
        $this->buildDatArray();
        $this->convert();
    }
}

$lsc = new LegoStlConverter();
$lsc->start();

