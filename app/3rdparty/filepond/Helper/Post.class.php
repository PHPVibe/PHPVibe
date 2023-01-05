<?php

namespace FilePond;

function is_associative_array($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function to_array($value) {
    if (is_array($value) && !is_associative_array($value)) {
        return $value;
    }
    return isset($value) ? array($value) : array();
}

function to_array_of_files($value) {
    if (is_array($value['tmp_name'])) {
        $results = [];
        foreach($value['tmp_name'] as $index => $tmpName ) {
            $file = array(
                'tmp_name' => $value['tmp_name'][$index],
                'name' => $value['name'][$index],
                'size' => $value['size'][$index],
                'error' => $value['error'][$index],
                'type' => $value['type'][$index]
            );
            array_push( $results, $file );
        }
        return $results;
    }
    return to_array($value);
}

function is_encoded_file($value) {
    $data = @json_decode($value);
    return is_object($data);
}

class Post {

    private $format;
    private $values;

    public function __construct($entry) {
        
        if (isset($_FILES[$entry])) {
            $this->values = to_array_of_files($_FILES[$entry]);
            $this->format = 'FILE_OBJECTS';
        }

        if (isset($_POST[$entry])) {
            $this->values = to_array($_POST[$entry]);
            if (is_encoded_file($this->values[0])) {
                $this->format = 'BASE64_ENCODED_FILE_OBJECTS';
            }
            else {
                $this->format = 'TRANSFER_IDS';
            }
        }
        
    }

    public function getFormat() {
        return $this->format;
    }

    public function getValues() {
        return $this->values;
    }
}