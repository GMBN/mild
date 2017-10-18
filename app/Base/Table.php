<?php

namespace Base;

class Table extends DAO {

    function showAll() {
        return $this->query('SHOW TABLES');
    }
    
        function desc($table) {
        return $this->query('DESC '.$table);
    }

}
