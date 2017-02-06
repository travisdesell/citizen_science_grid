<?php

class IDXException extends Exception
{
    public function __construct(string $msg = null, int $code = 0)
    {
        parent::__construct("IDXReader: $msg", $code);
    }
};

class IDXReader implements Iterator, ArrayAccess
{
    // the possible IDX formats
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

    private $position = 0;      /* current position */
    private $count = 0;         /* count of elements */
    private $dims = 0;          /* the number of dimensions */
    private $datasize = 0;      /* size of each element in bytes */
    private $vars = array();    /* size of each variable */
    private $elementsize = 1;   /* size of each total element in bytes */
    private $headersize = 0;    /* size of the header */
    private $pack = '';         /* pack character */
    private $fp = NULL;         /* file pointer */
    private $current = NULL;    /* current */
    private $format = 0;

    private $data = array();    /* read in the entire file now */

    public function __construct(string $filename)
    {
        echo "IDX File: $filename\n";

        $this->fp = fopen($filename, 'rb');
        $size = filesize($filename);
        if (!$this->fp || $size <= 0) {
            throw new IDXException("Unable to open file $filename");
        }

        // check the header
        $header = unpack('C4', fread($this->fp, 4));
        if ($header[1] != 0x00 && $header[2] != 0x00) {
            $this->destroy();
            throw new IDXException("Malformed header ${header[1]} ${header[2]}");
        }

        // get the data size
        $this->format = $header[3];
        if (!isset(self::FORMATS[$this->format])) {
            $this->destroy();
            throw new IDXException("Unknown format ${header[3]}");
        }

        // get the dimensions (min 1)
        if (($this->dims = $header[4]) < 1) {
            $this->destroy();
            throw new IDXException("No dimensions given ${header[4]}");
        }

        // remove 1 because the count is the first dimension
        --$this->dims;
        $this->count = unpack('N', fread($this->fp, 4))[1];

        // get the size, in total, of each element
        $this->datasize = self::FORMATS[$header[3]]["len"];
        $this->pack = self::FORMATS[$header[3]]["pack"];
        for ($i = 1; $i <= $this->dims; ++$i) {
            $var = unpack('N', fread($this->fp, 4))[1];
            $this->vars[] = $var;
            $this->elementsize *= $var;
        }
        $this->elementsize *= $this->datasize;

        $this->headersize = 4 + 4 * ($this->dims + 1);

        echo "\tCount:        ". $this->count ."\n";
        echo "\tHeader Size:  ". $this->headersize ."\n";
        echo "\tElement Size: ". $this->elementsize ."\n";

        // validate the file
        $expected_filesize = $this->headersize + $this->elementsize * $this->count;
        $filesize = filesize($filename);
        if ($filesize != $expected_filesize) {
            throw new IDXException("Mismatched files sizes: $expected_filesize vs $filesize");
        }

        /* read in the entire file */
        for ($i = 0; $i < $this->count; ++$i) {
            $current = array();
            $this->readElement($current);
            $this->data[] = $current;
        }

        if (count($this->data) != $this->count) {
            throw new IDXException("Read in the wrong amount of data!");
        }
    }

    public function __destruct()
    {
        $this->destroy();
    }

    /** Destroys the file pointer. */
    private function destroy()
    {
        if ($this->fp)
            fclose($this->fp);
    }

    /** Gets the header for this, given a specific size. */
    public function getHeader(int $count) {
        $header = pack('C4',
            0x00,
            0x00,
            $this->format,
            $this->dims + 1
        );

        $header .= pack('N', $count);
        foreach ($this->vars as $var) {
            $header .= pack('N', $var);
        }

        return $header;
    }

    public function getWidth() {
        return $this->count * $this->getElementWidth();
    }

    public function getHeight() {
        return $this->count * $this->getElementHeight();
    }

    public function getElementWidth() {
        if ($this->dims < 3) {
            return 0;
        }

        return $this->vars[0];
    }

    public function getElementHeight() {
        if ($this->dims < 3) {
            return 0;
        }

        return $this->vars[1];
    }

    public function pack() {
        return $this->pack;
    }

    /** Rewinds back to the beginning. */
    public function rewind() {
        fseek($this->fp, $this->headersize, SEEK_SET); 
        $this->position = 0;
        $this->current = null;
    }

    private function read(int $dim, array &$ret)
    {
        $read = 0;

        if ($dim == ($this->dims - 1)) {
            for ($i = 0; $i < $this->vars[$dim]; ++$i) {
                $ret[] = unpack($this->pack, fread($this->fp, $this->datasize))[1];
                $read += $this->datasize;
            }
        } else {
            for ($i = 0; $i < $this->vars[$dim]; ++$i) {
                $read += $this->read($dim + 1, $ret);
            }
        }

        return $read;
    }

    private function readElement(array &$ret)
    {
        return $this->read(0, $ret);
    }

    /** Gets the current element. */
    public function current()
    {
        // just return our cache if we have one
        if ($this->current)
            return $this->current;

        // read in the element
        $this->current = array();
        $read = $this->readElement($this->current);

        // make sure the size is expected
        if ($read != $this->elementsize) {
            $this->current = null;
            throw new IDXException("Mismatched sizes: $read vs ".$this->elementsize);
        }

        // return
        return $this->current;
    }

    /** Gets the current key. */
    public function key() {
        return $this->position;
    }

    /** Goes to the next element. */
    public function next() {
        ++$this->position;
        $this->current = null;
    }

    private function fileOffset($index) {
        if (!$this->offsetExists($index)) {
            throw new IDXException("Index out of bounds: $index.");
        }

        return $this->headersize + $this->elementsize * $index;
    }

    private function seek($index) {
        if (!$this->offsetExists($index)) {
            throw new IDXException("Index out of bounds: $index.");
        }

        fseek($this->fp, $this->fileOffset($index), SEEK_SET); 
    }

    /** Is this element valid? */
    public function valid() {
        return ($this->position < $this->count);
    }

    /** Array functions */
    public function offsetExists($offset) {
        if (!is_int($offset)) {
            return false;
        }

        $index = intval($offset);
        if ($index < 0 || $index >= $this->count) {
            return false;
        }

        return true;
    }

    public function get($index) {
        if (!$this->offsetExists($index)) {
            throw new IDXException("Index out of bounds: $index.");
        }
        
        $offset = intval($index);
        $this->seek($offset);
        $value = array();
        $this->readElement($value);
        if ($this->position == $this->count) {
            $this->seek($this->position - 1);
        } else {
            $this->seek($this->position);
        }

        return $value;
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        throw new IDXException("Array setting not available.");
    }

    public function offsetUnset($offset) {
        throw new IDXException("Array unsetting not available.");
    }

    public function count() {
        return $this->count;
    }

};

?>
