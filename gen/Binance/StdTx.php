<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: dex.proto

namespace Binance;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * please note the field name is the JSON name.
 *
 * Generated from protobuf message <code>Binance.StdTx</code>
 */
class StdTx extends \Google\Protobuf\Internal\Message
{
    /**
     *    uint64 SIZE-OF-ENCODED // varint encoded length of the structure after encoding
     *    0xF0625DEE   // hardcoded, object type prefix in 4 bytes
     *
     * Generated from protobuf field <code>repeated bytes msgs = 1;</code>
     */
    private $msgs;
    /**
     * array of size 1, containing the standard signature structure of the transaction sender
     *
     * Generated from protobuf field <code>repeated bytes signatures = 2;</code>
     */
    private $signatures;
    /**
     * a short sentence of remark for the transaction. Please only `Transfer` transaction allows 'memo' input, and other transactions with non-empty `Memo` would be rejected.
     *
     * Generated from protobuf field <code>string memo = 3;</code>
     */
    protected $memo = '';
    /**
     * an identifier for tools triggerring this transaction, set to zero if unwilling to disclose.
     *
     * Generated from protobuf field <code>int64 source = 4;</code>
     */
    protected $source = 0;
    /**
     *byte array, reserved for future use
     *
     * Generated from protobuf field <code>bytes data = 5;</code>
     */
    protected $data = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $msgs
     *              uint64 SIZE-OF-ENCODED // varint encoded length of the structure after encoding
     *              0xF0625DEE   // hardcoded, object type prefix in 4 bytes
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $signatures
     *           array of size 1, containing the standard signature structure of the transaction sender
     *     @type string $memo
     *           a short sentence of remark for the transaction. Please only `Transfer` transaction allows 'memo' input, and other transactions with non-empty `Memo` would be rejected.
     *     @type int|string $source
     *           an identifier for tools triggerring this transaction, set to zero if unwilling to disclose.
     *     @type string $data
     *          byte array, reserved for future use
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Dex::initOnce();
        parent::__construct($data);
    }

    /**
     *    uint64 SIZE-OF-ENCODED // varint encoded length of the structure after encoding
     *    0xF0625DEE   // hardcoded, object type prefix in 4 bytes
     *
     * Generated from protobuf field <code>repeated bytes msgs = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getMsgs()
    {
        return $this->msgs;
    }

    /**
     *    uint64 SIZE-OF-ENCODED // varint encoded length of the structure after encoding
     *    0xF0625DEE   // hardcoded, object type prefix in 4 bytes
     *
     * Generated from protobuf field <code>repeated bytes msgs = 1;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setMsgs($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->msgs = $arr;

        return $this;
    }

    /**
     * array of size 1, containing the standard signature structure of the transaction sender
     *
     * Generated from protobuf field <code>repeated bytes signatures = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSignatures()
    {
        return $this->signatures;
    }

    /**
     * array of size 1, containing the standard signature structure of the transaction sender
     *
     * Generated from protobuf field <code>repeated bytes signatures = 2;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSignatures($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->signatures = $arr;

        return $this;
    }

    /**
     * a short sentence of remark for the transaction. Please only `Transfer` transaction allows 'memo' input, and other transactions with non-empty `Memo` would be rejected.
     *
     * Generated from protobuf field <code>string memo = 3;</code>
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * a short sentence of remark for the transaction. Please only `Transfer` transaction allows 'memo' input, and other transactions with non-empty `Memo` would be rejected.
     *
     * Generated from protobuf field <code>string memo = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setMemo($var)
    {
        GPBUtil::checkString($var, True);
        $this->memo = $var;

        return $this;
    }

    /**
     * an identifier for tools triggerring this transaction, set to zero if unwilling to disclose.
     *
     * Generated from protobuf field <code>int64 source = 4;</code>
     * @return int|string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * an identifier for tools triggerring this transaction, set to zero if unwilling to disclose.
     *
     * Generated from protobuf field <code>int64 source = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setSource($var)
    {
        GPBUtil::checkInt64($var);
        $this->source = $var;

        return $this;
    }

    /**
     *byte array, reserved for future use
     *
     * Generated from protobuf field <code>bytes data = 5;</code>
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *byte array, reserved for future use
     *
     * Generated from protobuf field <code>bytes data = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setData($var)
    {
        GPBUtil::checkString($var, False);
        $this->data = $var;

        return $this;
    }

}

