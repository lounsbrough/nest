<?PHP
class Location
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function getPresence()
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/location/get-presence.php';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET')
        );
        $postJSON = json_encode($postData);
        
        return json_decode($this->utilities->postJSONRequest($url, $postJSON), true);
    }
}
?>