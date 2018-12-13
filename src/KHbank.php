<?php
/**
 * @license MIT
 * @license http://opensource.org/licenses/MIT
 */
namespace Payment;

/**
 * Class KHbank
 * @package KHbank
 */
class KHbank {

    /** @var int  */
    protected $mid;
    /** @var string */
    protected $pemFile;
    /** @var string */
    protected $bankUrl;
    /** @var string  */
    protected $ccy;
    /** @var string  */
    protected $lang;

    /**
     * KHbank constructor.
     * @param int $mid Merchant ID. Given by the bank.
     * @param string $pemFile Path of the private key file.
     * @param string $bankUrl Url of the bank system.
     * @param string $ccy Currency code.
     * @param string $lang Languge code.
     */
    public function __construct($mid, $pemFile, $bankUrl, $ccy = 'HUF', $lang = 'HU')
    {
        $this->mid =        $mid;
        $this->pemFile =    $pemFile;
        $this->bankUrl =    $bankUrl;
        $this->ccy =        $ccy;
        $this->lang =       $lang;
    }

    /**
     * @param int $id Id of the transaction.
     * @param int $amount Amount of the payment.
     * @param int|bool $szepZseb Id of the used "Szepkartya zseb". False to use credit card.
     * @return string Signature string for the communication.
     */
    private function generateSign($id, $amount, $szepZseb)
    {
        $data  = "mid=" . $this->mid;
        $data .= "&txid=" . $id;
        $data .= "&type=PU";
        $data .= "&amount=" . $amount * 100;
        $data .= "&ccy=HUF";

        if ($szepZseb > 0) {
            $data .= "&szep_zseb=" . $szepZseb;
        }

        $fp = fopen($this->pemFile, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($data, $signature, $pkeyid);
        openssl_free_key($pkeyid);
        $sign = bin2hex($signature);

        return $sign;
    }

    /**
     * @param int $id Id of the transaction.
     * @param int $amount Amount of the payment.
     * @param int|bool $szepZseb Id of the used "Szepkartya zseb". False to use credit card.
     * @param string $type Type of the transaction.
     * @return string URL to call for paying.
     */
    private function generateUrl($id, $amount, $szepZseb, $type)
    {
        $url  = $this->bankUrl . '/PGPayment?txid=' . $id;
        $url .= '&type=' . $type;
        $url .= '&mid=' . $this->mid;
        $url .= '&amount=' . $amount * 100;
        $url .= '&ccy=HUF';
        $url .= '&sign=' . $this->generateSign($id, $amount, $szepZseb);
        $url .= '&lang=HU';

        if ($szepZseb > 0) {
            $url .= "&szep_zseb=" . $szepZseb;
        }

        return $url;
    }

    /**
     * @param int $id Id of the transaction.
     * @param int $amount Amount of the payment.
     * @param int|bool $szepZseb Id of the used "Szepkartya zseb". False to use credit card.
     * @return string Sign of the transaction communication.
     */
    public function getSign($id, $amount, $szepZseb = false)
    {
        return $this->generateSign($id, $amount, $szepZseb);
    }

    /**
     * @param int $id Id of the transaction.
     * @param int $amount Amount of the payment.
     * @param int|bool $szepZseb Id of the used "Szepkartya zseb". False to use credit card.
     * @return string URL to for the payment page of the transaction.
     */
    public function getPayUrl($id, $amount, $szepZseb = false)
    {
        return $this->generateUrl($id, $amount, $szepZseb, 'PU');
    }

    /**
     * @param int $id Id of the transaction.
     * @param int $amount Amount of the reservation.
     * @param int|bool $szepZseb Id of the used "Szepkartya zseb". False to use credit card.
     * @return string URL to for the reservation page of the transaction.
     */
    public function getReserveUrl($id, $amount, $szepZseb = false)
    {
        return $this->generateUrl($id, $amount, $szepZseb, 'RE');
    }

    /**
     * @param int $id Id of the transaction.
     * @return string To get the information of the transaction.
     */
    public function getResultUrl($id)
    {
        return $this->bankUrl . '/PGResult?mid=' . $this->mid . '&txid=' . $id;
    }

    /**
     * @param int $id Id of the transaction.
     * @return bool|string Information of the transaction.
     */
    public function getResult($id)
    {
        $url = $this->bankUrl . '/PGResult?mid=' . $this->mid . '&txid=' . $id;
        return file_get_contents($url);
    }
}