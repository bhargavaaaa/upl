<?php

use Bhargavaaaa\Excel\Files\ExportHandler;

class TestNewFileHandler implements ExportHandler {

    /**
     * Handle
     * @param $file
     * @return mixed|void
     */
    public function handle($file)
    {
        return 'exported';
    }

} 