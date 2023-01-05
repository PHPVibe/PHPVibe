<?php

namespace FilePond;

require_once('Post.class.php');

class UniqueIdDispenser {
    private static $counter = 0;
    public static function dispense() {
        return md5(uniqid(self::$counter++, true));
    }
}

class Transfer {

    private $id;
    private $file = null;
    private $chunks = [];
    private $variants = [];
    private $metadata = [];
    
    public function __construct($id = false) {
        $this->id = $id ? $id : UniqueIdDispenser::dispense();
    }

    public function getid() {
        return $this->id;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function getChunks() {
        return $this->chunks;
    }

    public function getFiles($mutator = null) {
        if ($this->file === null) return null;
        $files = array_merge(isset($this->file) ? [$this->file] : [], $this->variants);
        return $mutator === null ? $files : call_user_func($mutator, $files, $this->metadata);
    }
    
    public function restore($file, $variants = [], $chunks = [], $metadata = []) {
        $this->file = $file;
        $this->variants = $variants;
        $this->chunks = $chunks;
        $this->metadata = $metadata;
    }

    public function populate($entry) {

        $files = isset($_FILES[$entry]) ? to_array_of_files($_FILES[$entry]) : null;
        $metadata = isset($_POST[$entry]) ? to_array($_POST[$entry]) : [];

        // parse metadata
        if (count($metadata)) {
            $this->metadata = @json_decode($metadata[0]);
        }

        // no files
        if ($files === null) return;

        // files should always be available, first file is always the main file
        $this->file = $files[0];
        
        // if variants submitted, set to variants array
        $this->variants = array_slice($files, 1);
    }

}