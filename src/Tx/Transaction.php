<?php

namespace Binance\Tx;

use Binance\Encoder\Encoder;
use Binance\Crypto\Keystore;
use Binance\StdTx;
use Binance\Send;
use Binance\Send_Token;
use Binance\Send\Input;
use Binance\Send\Output;
use Binance\StdSignature\PubKey;
use Binance\StdSignature;
use Google\Protobuf\Internal\CodedOutputStream;
use Binance\NewOrder;
/**
 * Creates a new transaction object.
 * @example
 * var rawTx = {
 *   account_number: 1,
 *   chain_id: 'bnbchain-1000',
 *   memo: '',
 *   msg: {},
 *   type: 'NewOrderMsg',
 *   sequence: 29,
 *   source: 0
 * };
 * var tx = new Transaction(rawTx);
 * @property {Buffer} raw The raw vstruct encoded transaction
 * @param {Number} data.account_number account number
 * @param {String} data.chain_id bnbChain Id
 * @param {String} data.memo transaction memo
 * @param {String} type transaction type
 * @param {Object} data.msg object data of tx type
 * @param {Number} data.sequence transaction counts
 * @param {Number} data.source where does this transaction come from
 */
class Transaction {

    private $typePrefixes;
    
    function __construct($data) {
        $this->type = $data->type;
        $this->sequence = $data->sequence ?? 0;
        $this->account_number = $data->account_number ?? 0;
        $this->chain_id = $data->chain_id;
        $this->msgs = $data->msg ? [$data->msg] : [];
        $this->memo = $data->memo;
        $this->source = $data->source ?? 0; // default value is 0
        $this->typePrefixes = array(
            'MsgSend' => "2A2C87FA",
            'NewOrderMsg' => "CE6DC043",
            'CancelOrderMsg' => "166E681B",
            'IssueMsg' => "17EFAB80",
            'BurnMsg' => "7ED2D2A0",
            'FreezeMsg' => "E774B32D",
            'UnfreezeMsg' => "6515FF0D",
            'MintMsg' => "467E0829",
            'ListMsg' => "B41DE13F",
            'StdTx' => "F0625DEE",
            'PubKeySecp256k1' => "EB5AE987",
            'SignatureSecp256k1' => "7FC4A495",
            'MsgSubmitProposal' => "B42D614E",
            'MsgDeposit' => "A18A56E5",
            'MsgVote' => "A1CADD36",
            'TimeLockMsg' => "07921531",
            'TimeUnlockMsg' => "C4050C6C",
            'TimeRelockMsg' => "504711DA",
            'HTLTMsg' => "B33F9A24",
            'DepositHTLTMsg' => "63986496",
            'ClaimHTLTMsg' => "C1665300",
            'RefundHTLTMsg' => "3454A27C",
            'SetAccountFlagsMsg' => "BEA6E301"
        );
    }

    /**
   * generate the sign bytes for a transaction, given a msg
   * @param {Object} concrete msg object
   * @return {Buffer}
   **/
    function getSignBytes($msg) {
        if (!$msg) {
            throw new Exception("msg should be an object");
        }
        // $signMsg = {
        // "account_number": this.account_number.toString(),
        // "chain_id": this.chain_id,
        // "data": null,
        // "memo": this.memo,
        // "msgs": [msg],
        // "sequence": this.sequence.toString(),
        // "source": this.source.toString()
        // }

        $signMsg = (object)(array('account_number' => strval($this->account_number), 'chain_id' => $this->chain_id, 'data' => null, 'memo' => $this->memo, 'msgs' => [$msg], 'sequence' => strval($this->sequence), 'source' => strval($this->source)));

        var_dump($signMsg);
        $encoder = new Encoder();
        return $encoder->convertObjectToSignBytes($signMsg);
    }

    /**
     * attaches a signature to the transaction
     * @param {Elliptic.PublicKey} pubKey
     * @param {Buffer} signature
     * @return {Transaction}
     **/
    function addSignature($pubKey, $signature) {
        $pubKey = $this->_serializePubKey($pubKey); // => Buffer
        echo "<br>pubkey<br/>";
        var_dump($pubKey);
        echo "signature";
        var_dump(bin2hex($signature));
        // this.signatures = [{
        //     pub_key: pubKey,
        //     signature: signature,
        //     account_number: this.account_number,
        //     sequence: this.sequence,
        // }]
        $this->signatures = array(array('pub_key' => $pubKey, 'signature' => $signature, 'account_number' => $this->account_number, 'sequence' => $this->sequence));
        return $this;
    }

    /**
     * encode signed transfer transaction to hex which is compatible with amino
     */
    function serializeTransfer() {
        if (!$this->signatures) {
            throw new Exception("need signature");
        }
        
        $token = new Send_Token();
        $token->setDenom($this->msgs[0]->inputs['coins'][0]->denom); 
        $token->setAmount($this->msgs[0]->inputs['coins'][0]->amount); 

        $input = new Input();
        $input->setAddress(hex2bin($this->msgs[0]->inputs['address']));
        $input->setCoins([$token]);

        $output = new Output();
        $output->setAddress(hex2bin($this->msgs[0]->outputs['address']));
        $output->setCoins([$token]);

        $msgSend = new Send();
        $msgSend->setInputs([$input]);
        $msgSend->setOutputs([$output]);  
        
        $msgToSet = $msgSend->serializeToString();
        $msgToSetPrefixed = hex2bin($this->typePrefixes['MsgSend'].bin2hex($msgToSet));
        $signatureToSet = $this->serializeSign();
        return ($this->serializeStdTx($msgToSetPrefixed, $signatureToSet));
    }

    /**
     * encode signed new order transaction to hex which is compatible with amino
     */
    function serializeNewOrder() {
        if (!$this->signatures) {
            throw new Exception("need signature");
        }

        $newOrder = new NewOrder();
        $newOrder->setSender(hex2bin($this->msgs[0]->sender));
        $newOrder->setId($this->msgs[0]->id);
        $newOrder->setSymbol($this->msgs[0]->symbol);
        $newOrder->setOrdertype($this->msgs[0]->ordertype);
        $newOrder->setSide($this->msgs[0]->side);
        $newOrder->setPrice($this->msgs[0]->price);
        $newOrder->setQuantity($this->msgs[0]->quantity);
        $newOrder->setTimeinforce($this->msgs[0]->timeinforce);

        $msgToSet = $newOrder->serializeToString();
        $msgToSetPrefixed = hex2bin($this->typePrefixes['NewOrderMsg'].bin2hex($msgToSet));
        $signatureToSet = $this->serializeSign();
        return ($this->serializeStdTx($msgToSetPrefixed, $signatureToSet));
    }

    /**
     * encode signatures in amino comaptible format
     */
    function serializeSign(){
        $stdSignature = new StdSignature();
        $stdSignature->setPubKey($this->signatures[0]['pub_key']);
        $stdSignature->setSignature($this->signatures[0]['signature']);
        $stdSignature->setAccountNumber($this->signatures[0]['account_number']);
        $stdSignature->setSequence($this->signatures[0]['sequence']);

        $signatureToSet = $stdSignature->serializeToString();
        return $signatureToSet;
    }

    /**
     * encode wrap message in StdTX amino comaptible format
     */
    function serializeStdTx($msgToSetPrefixed, $signatureToSet){
        $stdTx = new StdTx();
        $stdTx->setMsgs([$msgToSetPrefixed]);
        $stdTx->setSignatures([$signatureToSet]);
        $stdTx->setMemo($this->memo);
        $stdTx->setSource($this->source);
        $stdTx->setData("");
       
        $stdTxBytes = $stdTx->serializeToString();

        $txWithPrefix = $this->typePrefixes['StdTx'].bin2hex($stdTxBytes);
        $lengthPrefix = strlen(pack('H*', $txWithPrefix));
        $output = new CodedOutputStream(2);
        $output->writeVarint64($lengthPrefix);
        $codedVarInt = $output->getData();
        $txToPost = bin2hex($codedVarInt).$txWithPrefix;
        return $txToPost;
    }

    /**
     * sign transaction with a given private key and msg
     * @param {string} privateKey private key hex string
     * @param {Object} concrete msg object
     * @return {Transaction}
     **/
    function sign($privateKey, $msg) {
        if(!$privateKey){
            throw new Exception("private key should not be null");
        }

        if(!$msg){
            throw new Exception("signing message should not be null");
        }

        $signBytes = $this->getSignBytes($msg);
        echo "sign_bytes<br/>";
        var_dump($signBytes);

        $privateKeyHex = $privateKey->getHex();


        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        $msg32 = hash('sha256', $signBytes, true);
        $privateKeySt = pack("H*", $privateKeyHex);

        /** @var resource $signature */
        $signature = null;
        if (1 !== secp256k1_ecdsa_sign($context, $signature, $msg32, $privateKeySt)) {
            throw new \Exception("Failed to create signature");
        }
        
        $serialized = '';
        secp256k1_ecdsa_signature_serialize_compact($context, $serialized, $signature);
    
        $keystore = new Keystore();
        
        $this->addSignature($keystore->privateKeyToPublicKey($privateKey), $serialized);
        return $this;
    }

    function _serializePubKey($pubKey){
        $hex = $pubKey -> getHex();
        $lengthPrefix = strlen(pack('H*', $hex));
        // prefix - length of the public key - public key
        $encodedPubKey = hex2bin('eb5ae987'.dechex($lengthPrefix).$hex);
        return $encodedPubKey;
    }

}

?>