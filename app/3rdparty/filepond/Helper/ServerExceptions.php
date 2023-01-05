<?php

namespace FilePond;

function catch_server_exceptions() {
    set_exception_handler(__NAMESPACE__ . '\\' . 'handle_server_exceptions');
}

function handle_server_exceptions($ex) {
    error_log('Uncaught exception in class="' . get_class($ex) . '" message="' . $ex->getMessage() . '" line="' . $ex->getLine() . '"');
    ob_end_clean();
    http_response_code(500);
}
