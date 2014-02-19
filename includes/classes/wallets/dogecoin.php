<?php

/*
 * @author Stoyvo
 */
class Class_Wallets_Dogecoin extends Class_Wallets_Abstract {

    protected $_apiURL;

    public function __construct($label, $address) {
        parent::__construct($label, $address);
        $this->_apiURL = 'http://dogechain.info/chain/Dogecoin/q/addressbalance/' . $address;
    }
    
    public function update($cached) {
        $fileHandler = new Class_FileHandler(
                'wallets/dogecoin/' . sha1($this->_address) . '.json'
        );

        if ($cached == false || $fileHandler->lastTimeModified() >= 3600) { // updates every 60 minutes. How much are you being paid out that this must change? We take donations :)
            $curl = curl_init($this->_apiURL);
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; RigWatch ' . CURRENT_VERSION . '; PHP/' . phpversion() . ')');
            $walletBalance = curl_exec($curl); // this comes back as a single value (total doge in the wallet)
            
            $data = array (
                'currency' => 'dogecoin',
                'currency_code' => 'DOGE',
                'label' => $this->_label,
                'address' => $this->_address,
                'balance' => (float) $walletBalance
            );
            
            $fileHandler->write(json_encode($data));
        }
        
        return json_decode($fileHandler->read());
    }

}