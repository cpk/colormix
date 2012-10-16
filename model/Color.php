<?php

class Color{
    
    private $id;
    private $code;
    private $name;
    private $price;
    private $package;
    
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPackage() {
        return $this->package;
    }

    public function setPackage($package) {
        $this->package = $package;
    }


    
}
?>
