<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

abstract class Model {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $db;
    protected $attributes = [];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function __get($property) {
        if (array_key_exists($property, $this->attributes)) {
            return $this->attributes[$property];
        }
        return null;
    }

    public function __set($property, $value) {
        $this->attributes[$property] = $value;
    }

    public function fill(array $data) {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function save() {
        if (empty($this->attributes)) {
            throw new \Exception("No data to save");
        }

        $data = array_intersect_key($this->attributes, array_flip($this->fillable));
        
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update($data);
        } else {
            return $this->create($data);
        }
    }

    protected function create(array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            
            $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
            return $this->find($this->attributes[$this->primaryKey]);
        } catch (PDOException $e) {
            throw new \Exception("Error creating record: " . $e->getMessage());
        }
    }

    protected function update(array $data) {
        if (!isset($this->attributes[$this->primaryKey])) {
            throw new \Exception("Primary key not set for update");
        }

        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "$column = :$column";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id";
        $data['id'] = $this->attributes[$this->primaryKey];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            return $this->find($this->attributes[$this->primaryKey]);
        } catch (PDOException $e) {
            throw new \Exception("Error updating record: " . $e->getMessage());
        }
    }

    public function delete() {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $this->attributes[$this->primaryKey]]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error deleting record: " . $e->getMessage());
        }
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $model = new static();
                $model->attributes = $result;
                return $model;
            }
            return null;
        } catch (PDOException $e) {
            throw new \Exception("Error finding record: " . $e->getMessage());
        }
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        
        try {
            $stmt = $this->db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $models = [];
            foreach ($results as $result) {
                $model = new static();
                $model->attributes = $result;
                $models[] = $model;
            }
            
            return $models;
        } catch (PDOException $e) {
            throw new \Exception("Error fetching records: " . $e->getMessage());
        }
    }

    public function where($column, $operator, $value = null) {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['value' => $value]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $models = [];
            foreach ($results as $result) {
                $model = new static();
                $model->attributes = $result;
                $models[] = $model;
            }
            
            return $models;
        } catch (PDOException $e) {
            throw new \Exception("Error in where query: " . $e->getMessage());
        }
    }

    public function first() {
        $sql = "SELECT * FROM {$this->table} LIMIT 1";
        
        try {
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $model = new static();
                $model->attributes = $result;
                return $model;
            }
            return null;
        } catch (PDOException $e) {
            throw new \Exception("Error fetching first record: " . $e->getMessage());
        }
    }

    public function toArray() {
        return array_diff_key(
            $this->attributes,
            array_flip($this->hidden)
        );
    }

    public function toJson() {
        return json_encode($this->toArray());
    }

    public function __toString() {
        return $this->toJson();
    }

    public function addImage($data) {
    $image = new PropertyImage();
    $image->fill($data);
    return $image->save();
    }
}
