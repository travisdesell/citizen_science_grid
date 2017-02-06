<?php

/** Generic exception class that allows. */
class IDXException extends Exception
{
    public function __construct(string $msg = null, int $code = 0)
    {
        parent::__construct("IDXReader: $msg", $code);
    }
};

/** Allows for the creation, loading, and saving of IDX files. */
class IDX implements ArrayAccess
{
    /** All the possible formats for IDX files */
    const FORMATS = array(
        0x08 => array(
            "len"   => 1,
            "pack"  => 'C'
        ),
        0x09 => array(
            "len"   => 1,
            "pack"  => 'c'
        ),
        0x0B => array(
            "len"   => 2,
            "pack"  => 's'
        ),
        0x0C => array(
            "len"   => 4,
            "pack"  => 'l'
        ),
        0x0D => array(
            "len"   => 4,
            "pack"  => 'f'
        ),
        0x0E => array(
            "len"   => 8,
            "pack"  => 'd'
        )
    );

    private $data = array();
    private $dims = array();
    private $format = 0;
    private $pack = '';
    private $datasize = 0;
    private $elementlen = 0;
    private $elementsize = 0;

    // iterator stuff
    private $position = 0;

    /** Create an empty IDX file with a given format (only required variable).
     * @param $format the format of the IDX data (see: self::FORMATS)
     * @param $dims the count for each dimension
     */
    public function __construct(int $format, array $dims)
    {
        if (!isset(self::FORMATS[$format])) {
            throw new IDXException("Unknown format: '$format'");
        }

        $this->elementlen = 1;
        foreach ($dims as $dim) {
            if (!is_int($dim)) {
                throw new IDXException("Unknown dimension: $dim");
            }

            $this->elementlen *= $dim;
        }

        $this->dims = $dims;
        $this->format = $format;
        $this->datasize = self::FORMATS[$format]["len"];
        $this->pack = self::FORMATS[$format]["pack"];
        $this->elementsize = $this->elementlen * $this->datasize;
        $this->position = 0;
    }

    /** Destroy all memory */
    public function __destruct()
    {

    }

    /** Load an IDX file and return the object */
    public static function fromFile(string $filename)
    {
        // open the given file as binary
        $fp = fopen($filename, 'rb');
        $size = filesize($filename);
        if (!$this->fp || $size <= 0) {
            throw new IDXException("Unable to open file $filename");
        }

        // check the header
        $header = unpack('C4', fread($this->fp, 4));
        if ($header[1] != 0x00 && $header[2] != 0x00) {
            throw new IDXException("Malformed header: '${header[1]} ${header[2]}'");
        }

        // get the format
        $format = $header[3];
        if (!isset(self::FORMATS[$format])) {
            throw new IDXException("Unknown format: '${header[3]}'");
        }

        // get the dimensions (min 1)
        $dims = $header[4];
        if ($dims < 1) {
            throw new IDXException("No dimensions given: ${header[4]}");
        }

        // count is always the first dimension
        --$dims;
        $count = unpack('N', fread($this->fp, 4))[1];

        // get the total number of variables per element
        $datasize = self::FORMATS[$header[3]]["len"];
        $pack = self::FORMATS[$header[3]]["pack"];
        $vars = array();
        for ($i = 0; $i < $dims; ++$i) {
            $var = unpack('N', fread($fp, 4))[1];
            $vars[] = $var;
            $elementsize *= $var;
        }

        // get he actual size of each element and the header
        $elementsize *= $datasize;
        $headersize = 4 + 4 * $dims;

        // validate the length of the file
        $expected_filesize = $headersize + $elementsize * $count;
        $filesize = filesize($filename);
        if ($filesize != $expected_filesize) {
            throw new IDXException("Mismatched files sizes: $expected_filesize vs $filesize");
        }

        // create the instance and read in the data
        $instance = new self($format, $vars);
        for ($i = 0; $i < $count; ++$i) {
            $this->data[] = $instance->readElementFromFile($fp);
        }

        // make sure the count matches up
        if (count($this->data) != $count) {
            throw new IDXException("Wrong number of elements read in.");
        }

        // we have a fully loaded IDX!
        return $instance;
    }

    /** Reads in an element from the given file. The file *must* be at the correct location. */
    public function readElementFromFile($fp): array
    {
        $element = array();
        if ($this->readDimFromFile($fp, 1, $element) != $this->elementsize) {
            throw new IDXException("Wrong element size!");
        }

        return $element;
    }

    /** Reads in the specified dimension (1-indexed and recursive) from the file. */
    private function readDimFromFile($fp, int $dim, array &$ret): int
    {
        $read = 0;

        if ($dim == count($this->dims)) {
            for ($i = 0; $i < $this->dims[$dim-1]; ++$i) {
                $ret[] = unpack($this->pack, fread($fp, $this->datasize))[1];
                $read += $this->datasize;
            }
        } else {
            for ($i = 0; $i < $this->dims[$dim]; ++$i) {
                $read += $this->read($dim + 1, $ret);
            }
        }

        return $read;
    }

    /** Save the IDX file to a given location */
    public function saveToFile(string $filename): boolean
    {
        return true;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function offsetExists(mixed $offset): boolean
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->count();
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // make sure the value is correctly formatted
        if (!is_array($value) || count($value) != $this->elementlen) {
            return;
        }

        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            if ($this->offsetExists($offset)) {
                $this->data[$offset] = $value;
            }
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    public function current(): mixed {
        return $this->data[$this->position];
    }

    public function key(): scalar {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): boolean {
        return $this->position < count($this->data);
    }
};

?>
