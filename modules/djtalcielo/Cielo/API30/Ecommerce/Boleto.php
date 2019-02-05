<?php
namespace Cielo\API30\Ecommerce;

class Boleto implements \JsonSerializable
{

    private $provider;
    
    private $address;
    
    private $expirationDate;
    
    private $boletoNumber;

    private $assignor;

    private $demonstrative;

    private $identification;
    
    private $instructions;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function populate(\stdClass $data)
    {
        $this->provider = isset($data->Provider)? $data->Provider: null;
        $this->address = isset($data->Address)? $data->Address: null;
        $this->expirationDate = isset($data->ExpirationDate)? $data->ExpirationDate: null;
        $this->boletoNumber = isset($data->BoletoNumber)? $data->BoletoNumber: null;
        $this->assignor = isset($data->Assignor)? !!$data->Assignor: false;
        $this->demonstrative = isset($data->Demonstrative)? $data->Demonstrative: null;
        $this->identification = isset($data->Identification)? $data->Identification: null;
        $this->instructions = isset($data->Instructions)? $data->Instructions: null;
    }
    
    
    function getProvider() {
        return $this->provider;
    }

    function getBoletoNumber() {
        return $this->boletoNumber;
    }

    function getAssignor() {
        return $this->assignor;
    }

    function getDemonstrative() {
        return $this->demonstrative;
    }

    function getExpirationDate() {
        return $this->expirationDate;
    }

    function getIdentification() {
        return $this->identification;
    }

    function getInstructions() {
        return $this->instructions;
    }

    function setProvider($provider) {
        $this->provider = $provider;
        return $this;
    }

    function setBoletoNumber($boletoNumber) {
        $this->boletoNumber = $boletoNumber;
        return $this;
    }

    function setAssignor($assignor) {
        $this->assignor = $assignor;
        return $this;
    }

    function setDemonstrative($demonstrative) {
        $this->demonstrative = $demonstrative;
        return $this;
    }

    function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    function setIdentification($identification) {
        $this->identification = $identification;
        return $this;
    }

    function setInstructions($instructions) {
        $this->instructions = $instructions;
        return $this;
    }
    function getAddress() {
        return $this->address;
    }

    function setAddress($address) {
        $this->address = $address;
        return $this;
    }


}