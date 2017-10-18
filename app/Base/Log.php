<?php

namespace Base;

class Log {

    static function erro($msg) {
        if (DEBUG) {
            echo "ocorreu um erro: " . $msg;
        }
    }

}
