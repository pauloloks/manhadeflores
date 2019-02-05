<?php

class Address extends AddressCore {

    public $numend;

    public function setWsNumAddressData() {
        $addressArray = explode(',', $this->address1);
		if(count($addressArray) == 3) {
			if(is_numeric(trim($addressArray[1]))){
				return trim($addressArray[1]);
			} elseif(is_numeric(trim($addressArray[2]))){
				return trim($addressArray[2]);
			} else {
				return trim($addressArray[1]);
			}
		} elseif(count($addressArray) == 2) {
			return trim($addressArray[1]);
		}
		return null;
    }

    public function __construct($id = null) {
        parent::__construct($id);

        $this->webserviceParameters['fields']['numend'] = array();
        $this->numend = $this->setWsNumAddressData();
    }
}
