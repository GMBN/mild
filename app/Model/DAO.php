<?php

namespace Model;

abstract class DAO {

    private $db;
    private $lastInsertId;

    public function __construct() {
        //obtem a conexao com o db
        $this->db = \Base\DB::connect();
    }

    /**
     * @return \PDO
     */
    function getDb() {
        return $this->db;
    }

    function setDb(\PDO $db) {
        $this->db = $db;
    }

    function getLastInsertId() {
        return $this->lastInsertId;
    }

    function setLastInsertId($lastInsertId) {
        $this->lastInsertId = $lastInsertId;
        return $this;
    }

    function insert() {
        $db = $this->db;
        $dados = $this->dados();
        foreach ($dados as $col => $val) {
            if ($val == '__default__') {
                unset($dados[$col]);
            }
        }
        $colunas = array_keys($dados);
        $sql_colunas = implode(',', $colunas);
        $sql_param = ':' . implode(',:', $colunas);
        $sql = 'INSERT INTO ' . $this->_table . '(' . $sql_colunas . ') VALUES (' . $sql_param . ')';
        $prepare = $db->prepare($sql);
        foreach ($dados as $key => $val) {
            $prepare->bindValue(':' . $key, $val);
        }
        $this->exec($prepare);
        $id = $db->lastInsertId();
        $this->setLastInsertId($id);
        return $prepare->rowCount();
    }

    function update($where = null, $par = array()) {
        $db = $this->db;
        $dados = $this->dados();
        foreach ($dados as $col => $val) {
            if ($val == '__default__') {
                unset($dados[$col]);
            }
        }
        $upt_col = [];
        $sql = 'UPDATE ' . $this->_table . ' SET ';
        foreach ($dados as $col => $val) {
            $upt_col[] = $col . '=:' . $col;
        }
        $sql .= implode(',', $upt_col);
        $sql .= ' WHERE ' . $where;
        $prepare = $db->prepare($sql);
        $allDados = array_merge($dados, $par);
        foreach ($allDados as $key => $val) {
            $prepare->bindValue(':' . $key, $val);
        }
        $this->exec($prepare);
        return $prepare->rowCount();
    }

    function delete($where = null, $par = array()) {
        $db = $this->db;
        $sql = 'DELETE FROM ' . $this->_table . ' ';
        $sql .= ' WHERE ' . $where;
        $prepare = $db->prepare($sql);
        foreach ($par as $key => $val) {
            $prepare->bindValue(':' . $key, $val);
        }
        $this->exec($prepare);
        return $prepare->rowCount();
    }

    function dados() {
        $dados = get_object_vars($this);
        unset($dados['_table'], $dados['db'], $dados['id'], $dados['lastInsertId']);
        return $dados;
    }

    function exec($prepare, $par = null) {
        if (is_array($par)) {
            foreach ($par as $name => $val) {
                if (is_integer($val)) {
                    $type = \PDO::PARAM_INT;
                } else {
                    $type = \PDO::PARAM_STR;
                }
                $prepare->bindValue(':' . trim($name), $val, $type);
            }
        }
        if (!$prepare->execute()) {
            $sql = $prepare->queryString;
            $erro = print_r($prepare->errorInfo(), true);
            $msg = "SQL: $sql \n ERRO: " . $erro;
            \Base\Log::erro($msg);
        }
    }

    function query($sql, $par = null) {
        $db = $this->getDb();
        $prepare = $db->prepare($sql);
        $this->exec($prepare, $par);
        $rs = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $rs;
    }

    private function parDefault() {
        $d = $this->dados();
        foreach ($d as $key => $val) {
            $this->{$key} = '__default__';
        }
    }

    function hydrate($dados) {
        foreach ($dados as $name => $value) {
            $part = explode('_', $name);
            $uc = array_map(function($word) {
                return ucfirst($word);
            }, $part);
            $func = implode('', $uc);
            $method = 'set' . $func;
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            } else if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }

    function findAll($where = null, $order = false) {
        $sql = 'SELECT * FROM ' . $this->_table;
        if (is_array($where)) {
            $sql .= ' WHERE ';
            foreach ($where as $col => $val) {
                $sql_where[] = $col . '=:' . $col;
            }
            $sql .= ' ' . implode(' AND ', $sql_where);
        }
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        } 
        
        $rs = $this->query($sql, $where);
        return $rs;
    }

    function findId($id) {
        $sql = "select * from " . $this->_table . " where id=:id";
        $par = ['id' => $id];
        $rs = $this->query($sql, $par);
        if ($rs) {
            return $rs[0];
        }
        return false;
    }

    function antiInjection($str) {
        $str = addslashes($str);
        return $str;
    }

}
