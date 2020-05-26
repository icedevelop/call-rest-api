<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 11/2/16
 * Time: 12:41 PM
 */

namespace Services\Bundle\Rest\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class of unit test
 *
 * Class DefaultControllerTest
 * @package Services\Bundle\Rest\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{

    /**
     * This function make the test
     */
    public function testIndex()
    {

        //Test is working progress
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}


        //Dichiaro le variabili
        $url=$this->url;
        $login=$this->login;
        $password=$this->password;
        $chiamante=$this->chiamante;
        $json=$this->json;
        $tipoChiamata=$this->tipoChiamata;
        $ritorno="";
        $jsonDecodificato="";
        $messaggio=$this->nomeCampoMessage;
        $success=$this->nomeCampoSuccess;

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