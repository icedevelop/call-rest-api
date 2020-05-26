<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 11/2/16
 * Time: 12:41 PM
 */

namespace Services\Bundle\Rest\Entity;

/**
 * This class permit to call rest resources
 *
 * Class ChiamataRest
 * @package ServicesBundle\Entity
 */
class ChiamataRest
{

    /**
     * Url to call
     *
     * @string
     */
    private $url;

    /**
     * Userid for the header
     *
     * @string
     */
    private $login;

    /**
     * Password for the header
     *
     * @string
     */
    private $password;

    /**
     * Who is calling
     *
     * @string
     */
    private $chiamante;

    /**
     * Here you can set your json
     *
     * @string
     */
    private $json;

    /**
     * http verrb
     *
     * @string
     */
    private $tipoChiamata;

    /**
     * Contain the information if needs to control success field or not in case it doesn't exist
     *
     * @boolean
     */
    private $controlSuccess=true;

    /**
     * Contains the httpcode received from the rest call
     *
     * @integer
     */
    private $httpcode;

    /**
     * This variable contain the name of the field containing message information from request
     *
     * @var string
     */
    private $nomeCampoMessage="message";

    /**
     * This variable contain the name of the field containing the result of the request
     *
     * @var string
     */
    private $nomeCampoSuccess="success";

    /**
     * This variable contains the ssl version
     *
     * @var string
     */
    private $sslVersion;

    /**
     * This variable contain the information if needs to get cookie or not
     *
     * @var boolean
     */
    private $getCookie=false;

    /**
     * This variable contain the information if needs to verify peer or not
     *
     * @var boolean
     */
    private $verifyPeer=true;

    /**
     * This property contains the value of the Cookie
     *
     * @var string
     */
    private $cookieValue;

    /**
     * This property contains the maximum amount of time in seconds to which the execution of individual cURL extension function calls will be limited. Timeout must be greater than Connecttimeout
     *
     * @var integer
     */
    private $timeout=90;

    /**
     * This property contains the maximum amount of time in seconds that is allowed to make the connection to the server. ConnectTimeout must be lower than timeout
     *
     * @var integer
     */
    private $connectTimeout=10;

    /**
     * This variable specify if header must be included in output or not
     *
     * @var boolean
     */
    private $includeHeader=false;

    /**
     * This property contains the USER-AGENT to set
     *
     * @var string
     */
    private $userAgent;

    private function chiamataCommonPart(){

        //Dichiaro le variabili
        $url=$this->url;
        $login=$this->login;
        $password=$this->password;
        $chiamante=$this->chiamante;
        $json=$this->json;
        $tipoChiamata=$this->tipoChiamata;
        $verifyPeer=$this->verifyPeer;
        $ritorno="";
        $jsonDecodificato="";

        //Tolgo gli spazi
        $url = str_replace(" ","%20",$url);

        //Inizializzo la chiamata
        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        //Imposto i valori
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($this->sslVersion))
            curl_setopt($ch, CURLOPT_SSLVERSION, $this->sslVersion);

        if ($tipoChiamata!="FORM")
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $tipoChiamata);

        //Se è post o patch di default deve passargli un json
        if ($tipoChiamata=="POST" || $tipoChiamata=="PUT") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json))
            );
        }

        if ($tipoChiamata=="FORM") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        //Verifico se devo impostare uno user agent
        if (!empty($this->userAgent))
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);

        //Imposto i timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        //Verifico se devo leggere cookie
        if ($this->getCookie || $this->includeHeader)
            curl_setopt($ch, CURLOPT_HEADER,1);

        //Verifico se devo impostare cookie
        if (!empty($this->cookieValue))
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookieValue );

        //Controllo se è stato passato un json anche alla chiamata delete
        if ($tipoChiamata=="DELETE" && !empty($json)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json))
            );
        }

        //Se è stato passato un userid allora lo setto nell'header
        if (!empty($login)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        }

        //Gli passo il json se valorizzato
        if (!empty($json)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        //Verifico se devo controllare il peer o no, di default lo fa
        if (!$verifyPeer) {
            //Non deve essere controllato
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }

        //Effettuo la chiamata
        $ritorno=curl_exec($ch);

        // Check if an error occurred
        if(curl_errno($ch)) {
            curl_close($ch);
            throw new \Exception($ritorno);
            //throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il messaggio di ritorno è:".$ritorno);
        }

        // Get HTTP response code
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Salvo l'httpcode nel caso in cui il chiamante voglia leggerlo
        $this->httpcode=$code;

        //Chiudo la chiamata
        curl_close($ch);

        //Controllo se il codice è tra quelli ammessi (200,201,202)
        if ($code<200 || $code>300)
            throw new \Exception($ritorno);
        //throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il codice di ritorno è:".$code." e il messaggio:".$ritorno);

        return $ritorno;

    }

    /**
     *
     * Questo metodo effettua una chiamata rest con decodifica in output sotto autenticazione all'url passato, restituisce un array decodificato dal json di risposta, controlla anche se c'è stato un errore logico ed in caso solleva un'eccezione
     * Se viene passato un tipo chiamata questo può assumere i seguenti valori
     * GET
     * POST
     * PUT
     * DELETE
     *
     * This metod make a rest call and return an array, http verb permits are:
     * GET
     * POST
     * PUT
     * DELETE
     *
     * @return array
     * @throws \Exception
     */
    public function chiamataRestDecodificata() {

        //Settaggi di default
        $messaggio=$this->nomeCampoMessage;
        $success=$this->nomeCampoSuccess;

        //Chiamo la parte comune
        $ritorno=$this->chiamataCommonPart();

        //Decodifico in un array il json di ritorno
        $jsonDecodificato=json_decode($ritorno);

        //Controllo se è stato scelto di testare il success field
        if ($this->controlSuccess) {
            //Controllo se il campo success è true o false
            if (!$jsonDecodificato->$success) {
                throw new \Exception($jsonDecodificato->$messaggio);
                //throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante.". Le informazioni restituite dal Web Service sono le seguenti:".
                //    $jsonDecodificato->$messaggio);
            }
        }

        //Restituisco l'array relativo al json ricevuto
        return $jsonDecodificato;
    }

    /**
     *
     * Questo metodo effettua una chiamata rest sotto autenticazione all'url passato, restituisce un array decodificato dal json di risposta, controlla anche se c'è stato un errore logico ed in caso solleva un'eccezione
     * Se viene passato un tipo chiamata questo può assumere i seguenti valori
     * GET
     * POST
     * PUT
     *
     * This metod make a rest call and return a string containing the json received, http verb permits are:
     * GET
     * POST
     * PUT
     * DELETE
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function chiamataRest() {

        //Settaggi di default
        $messaggio=$this->nomeCampoMessage;
        $success=$this->nomeCampoSuccess;

        //Chiamo la parte comune
        $ritorno=$this->chiamataCommonPart();

        //Controllo se è stato scelto di testare il success field
        if ($this->controlSuccess) {

            //Decodifico il json in un array per semplicità per accedere meglio alle proprietà successivamente
            $jsonDecodificato=json_decode($ritorno);

            //Controllo il campo success
            if (!$jsonDecodificato->$success) {
                throw new \Exception($jsonDecodificato->$messaggio);
                //throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante.". Le informazioni restituite dal Web Service sono le seguenti:".
                //    $jsonDecodificato->$messaggio);
            }
        }

        //Restituisco il json
        return $ritorno;
    }

    /**
     *
     * get url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * set login
     *
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * get caller
     *
     * @return string
     */
    public function getChiamante()
    {
        return $this->chiamante;
    }

    /**
     * set caller
     *
     * @param string $chiamante
     */
    public function setChiamante($chiamante)
    {
        $this->chiamante = $chiamante;
    }

    /**
     * get json
     *
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * set json
     *
     * @param string $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * get httpverb
     *
     * @return string
     */
    public function getTipoChiamata()
    {
        return $this->tipoChiamata;
    }

    /**
     * set httpverb
     *
     * @param string $tipoChiamata
     */
    public function setTipoChiamata($tipoChiamata)
    {
        $this->tipoChiamata = $tipoChiamata;
    }

    /**
     * get if control succes field
     *
     * @return boolean
     */
    public function getControlSuccess()
    {
        return $this->controlSuccess;
    }

    /**
     * set if control succes field
     *
     * @param boolean $controlSuccess
     */
    public function setControlSuccess($controlSuccess)
    {
        $this->controlSuccess = $controlSuccess;
    }

    /**
     * return httpcode of last request
     *
     * @return integer
     */
    public function getHttpcode()
    {
        return $this->httpcode;
    }

    /**
     * get value of field nomeCampoMessage
     *
     * @return string
     */
    public function getNomeCampoMessage()
    {
        return $this->nomeCampoMessage;
    }

    /**
     * set value of field nomeCampoMessage
     *
     * @param string $nomeCampoMessage
     */
    public function setNomeCampoMessage($nomeCampoMessage)
    {
        $this->nomeCampoMessage = $nomeCampoMessage;
    }

    /**
     * get value of field nomeCampoSuccess
     *
     * @return string
     */
    public function getNomeCampoSuccess()
    {
        return $this->nomeCampoSuccess;
    }

    /**
     * set value of field nomeCampoSuccess
     *
     * @param string $nomeCampoSuccess
     */
    public function setNomeCampoSuccess($nomeCampoSuccess)
    {
        $this->nomeCampoSuccess = $nomeCampoSuccess;
    }

    /**
     * This method permit to get the ssl version
     *
     * @return string
     */
    public function getSslVersion()
    {
        return $this->sslVersion;
    }

    /**
     * This method permit to specify the ssl version
     *
     * @param string $sslVersion
     */
    public function setSslVersion($sslVersion)
    {
        $this->sslVersion = $sslVersion;
    }

    /**
     * Thi method set if the client want to read cookie from response
     *
     * @param bool $valore
     */
    function returnCookie($valore=true){

        //Se è true valorizzo, di default lo è
        if ($valore) {
            $this->includeHeader=true;
            $this->getCookie=true;
        }

    }

    /**
     * This method specify if the client want to set cookie. The value is the cookie. The cookie must be name:value
     *
     * @param $cookie
     */
    function setCookie($cookie){

        //Verifico se è stato passato un cookie
        if (!empty($cookie)) {
            $this->includeHeader=true;
            $this->cookieValue=$cookie;
        }

    }

    /**
     * This method specify if the client want get the header from response
     *
     * @param bool $valore
     */
    function getHeader($valore=true){

        //Se è true lo imposto, di default è comunque true
        if ($valore) {
            $this->includeHeader=true;
        }

    }

    /**
     * This method can be used to set the entire timeout to get the response, this must be grater than connectiontimeout
     *
     * @param int $valore
     * @throws \Exception
     */
    function setTimeout($valore){

        //Verifico che il timeout non abbia un valore inferiore al connection timeout
        if ($valore<$this->connectTimeout)
            throw new \Exception("Timeout cannot be lower than ConnectionTimeout");

        //Se non è vuoto lo valorizzo
        if (!empty($valore)) {
            $this->timeout=$valore;
        }

    }

    /**
     * This method can be used to set the connection timeout to the server, this must be lower than timeout
     *
     * @param int $valore
     * @throws \Exception
     */
    function setConnectionTimeout($valore){

        //Verifico che il valore di connectiontimeout non ecceda il valore di timeout
        if ($valore>$this->timeout)
            throw new \Exception("ConnectionTimeout cannot be greater than Timeout");

        //Se non è vuoto lo valorizzo
        if (!empty($valore)) {
            $this->connectTimeout=$valore;
        }

    }

    /**
     * This method can be used to specify the user agent to pass to server
     *
     * @param string $valore
     */
    function setUserAgent($valore) {

        // Se non è vuoto valorizzo lo user agent
        if (!empty($valore)) {
            $this->userAgent=$valore;
        }
    }

    /**
     * @return bool
     */
    public function isVerifyPeer()
    {
        return $this->verifyPeer;
    }

    /**
     * @param bool $verifyPeer
     */
    public function setVerifyPeer($verifyPeer)
    {
        $this->verifyPeer = $verifyPeer;
    }

}