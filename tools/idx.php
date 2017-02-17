<?php

/** Generic exception class that allows. */
class IDXException extends Exception
{
    public function __construct(string $msg = null, int $code = 0)
    {
        parent::__construct("$msg", $code);
    }
};

/** Allows for the creation, loading, and saving of IDX files. */
class IDX implements ArrayAccess, Iterator
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
    public function __construct(int $format, array $dims = array())
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
    public static function fromFile(string $filename, array &$meta)
    {
        // open the given file as binary
        $fp = fopen($filename, 'rb');
        $size = filesize($filename);
        if (!$fp || $size <= 0) {
            throw new IDXException("Unable to open file $filename");
        }

        // check the header
        $header = unpack('C4', fread($fp, 4));
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
        $count = unpack('N', fread($fp, 4))[1];

        // get the total number of variables per element
        $datasize = self::FORMATS[$header[3]]["len"];
        $pack = self::FORMATS[$header[3]]["pack"];
        $vars = array();
        $elementsize = 1;
        for ($i = 0; $i < $dims; ++$i) {
            $var = unpack('N', fread($fp, 4))[1];
            $vars[] = $var;
            $elementsize *= $var;
        }

        // get he actual size of each element and the header
        $elementsize *= $datasize;
        $headersize = 4 + 4 * ($dims + 1);

        // validate the length of the file
        $expected_filesize = $headersize + $elementsize * $count;
        $filesize = filesize($filename);
        if ($filesize != $expected_filesize) {
            throw new IDXException("Mismatched files sizes: $expected_filesize vs $filesize");
        }

        // create the instance and read in the data
        $instance = new self($format, $vars);
        $instance->getMetaInformation($meta);
        for ($i = 0; $i < $count; ++$i) {
            $instance[] = $instance->readElementFromFile($fp);
        }
        $instance->getMetaInformation($meta);

        // make sure the count matches up
        if ($instance->count() != $count) {
            throw new IDXException("Wrong number of elements read in.");
        }

        // we have a fully loaded IDX!
        return $instance;
    }

    public function getMetaInformation(array &$meta)
    {
        $structure = "" . $this->count();
        foreach ($this->dims as $dim) {
            $structure .= " x $dim";
        }

        $meta["Dimensions"] = count($this->dims) + 1;
        $meta["Structure"] = $structure;
        $meta["Data Size"] = $this->datasize;
        $meta["Element Size"] = $this->elementsize;
        $meta["Elements"] = count($this->data);
        $meta["Pack"] = $this->pack;
    }

    /** Reads in an element from the given file. The file *must* be at the correct location. */
    public function readElementFromFile($fp): array
    {
        $element = array();
        if (count($this->dims) > 0) {
            $read = $this->readDimFromFile($fp, 1, $element);
        } else {
            $element[] = unpack($this->pack, fread($fp, $this->datasize))[1];
            $read += $this->datasize;
        } 

        if ($read != $this->elementsize) {
            throw new IDXException("[" . count($this->data) . "] Wrong element size: $read vs ".$this->elementsize);
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
            for ($i = 0; $i < $this->dims[$dim-1]; ++$i) {
                $read += $this->readDimFromFile($fp, $dim + 1, $ret);
            }
        }

        return $read;
    }

    /** Save the IDX file to a given location */
    public function saveToFile(string $filename): boolean
    {
        $fp = fopen($filename, 'wb');
        if (!$fp) {
            return false;
        }

        // write the header and count
        fwrite($fp, pack('C4', 0x00, 0x00, $this->format, count($this->dims) + 1), 4);
        fwrite($fp, pack('N', count($this->data)), 4);

        // write the length for each dimension
        foreach ($this->dims as $dim) {
            fwrite($fp, pack('N', $dim), 4);
        }

        // close
        fclose($fp);
        return true;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function dimCount(int $dim): int
    {
        if ($dim < 0 || $dim >= count($this->dims)) {
            return 0;
        }

        return $this->dims[$dim];
    }

    public function offsetExists($offset)
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->count();
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        return null;
    }

    public function offsetSet($offset, $value)
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

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return $this->position < count($this->data);
    }
};

?>
