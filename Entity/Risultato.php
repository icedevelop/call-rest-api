<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 11/2/16
 * Time: 9:38 AM
 */

namespace Services\Bundle\Rest\Entity;

/**
 * This class contains the format for the json response
 *
 * Class Risultato
 * @package AppBundle\Entity
 */
class Risultato
{

    /**
     * contain information if there is been an error
     *
     * @var boolean
     */
    private $success;

    /**
     * contain the message
     *
     * @var string
     */
    private $message;

    /**
     * contain metadata catalog
     *
     * @var string
     */
    private $catalog;

    /**
     * this contains the information, the core
     *
     * @var
     */
    private $items;

    /**
     * this contains the possible error code
     *
     * @var
     */
    private $errorCode;

    /**
     * get success value
     *
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * set success value
     *
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * get messagge
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * set message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * get metadata catalog
     *
     * @return string
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * set metadata catalog
     *
     * @param string $catalog
     */
    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * get items
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * set items
     *
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * get error code
     *
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * set error code
     *
     * @param mixed $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }


}