<?php
class httpRequest {
  private $curl = false;
  public $userAgentsFile = 'userAgents';
  
  public function __construct($url) {
    if(!$url) {
      throw new Exception('Expected URL argument');
    }
    
    if(!filter_var($url, FILTER_VALIDATE_URL)) {
      throw new Exception('Invalid URL provided');
    }
    
    $this->curl = curl_init();
    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
  }
  
  public function setPOST($postData = '') {
    curl_setopt($this->curl, CURLOPT_POST, true);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postData);
  }
  
  public function setReferrerUrl($refURL) {
    if(!$refURL) {
      throw new Exception('Expected referrer URL argument');
    }
    
    if(!filter_var($refURL, FILTER_VALIDATE_URL)) {
      throw new Exception('Invalid referrer URL provided');
    }
    
    curl_setopt($this->curl, CURLOPT_REFERER, $refURL);
  }
  
  public function setUserAgent($userAgentString) {
    if(!$userAgentString) {
      throw new Exception('Expected user agent string argument');
    }
    
    curl_setopt($this->curl, CURLOPT_USERAGENT, $userAgentString);
  }
  
  public function setRandUserAgent() {
    if(!file_exists($this->userAgentsFile)) {
      throw new Exception('User agents file does not exist');
    }
    
    $userAgents = file($this->userAgentsFile);
    
    if(count($userAgents) == 0) {
      throw new Exception('Empty user agents file');
    }
    
    shuffle($userAgents);
    curl_setopt($this->curl, CURLOPT_USERAGENT, trim($userAgents[0]));
  }
  
  public function setTimeout($timeout) {
    if(!$timeout) {
      throw new Exception('Expected timeout argument');
    }
    
    if(!is_numeric($timeout)) {
      throw new Exception('Invalid numeric value for timeout');
    }
    
    curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
  }
  
  public function setCookieFile($cookieFile) {
    if(!$cookieFile) {
      throw new Exception('Expected cookie file argument');
    }
    
    curl_setopt($this->curl, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($this->curl, CURLOPT_COOKIEFILE, $cookieFile);
  }
  
  public function setCookieContents($cookieContent) {
    if(!$cookieContent) {
      throw new Exception('Expected cookie content argument');
    }
    
    curl_setopt($this->curl, CURLOPT_COOKIE, $cookieContent);
  }
  
  public function followLocation($bool) {
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $bool);
  }
  
  public function setProxy($proxy) {
    if(!$proxy) {
      throw new Exception('Expected proxy argument');
    }
    
    preg_match(
      '/^(HTTP|HTTPS|SOCKS4|SOCKS5):\/\/([a-z0-9.-]+):(\d+)$/i',
      $proxy, $match
    );
    
    if(!$match) {
      throw new Exception('Invalid proxy argument');
    }
    
    $pType = strtolower($match[1]);
    $pHost = $match[2];
    $pPort = $match[3];
    
    switch($pType) {
      case 'socks4':
        curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        break;
      case 'socks5':
        curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        break;
      default:
        curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    curl_setopt($this->curl, CURLOPT_PROXY, $pHost);
    curl_setopt($this->curl, CURLOPT_PROXYPORT, $pPort);
  }
  
  public function setHeaders($headerArr) {
    if(!$headerArr) {
      throw new Exception('Expected header array argument');
    }
    
    if(!is_array($headerArr)) {
      throw new Exception('Provided argument is not an array');
    }
    
    if(count($headerArr) == 0) {
      throw new Exception('Empty header array provided');
    }
    
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headerArr);
  }
  
  public function exec() {
    $reqResp = curl_exec($this->curl);
    
    if(curl_errno($this->curl)) {
      $curlErr = curl_error($this->curl);
      curl_close($this->curl);
      
      return $this->response(
        'ERROR',
        'cURL returned errors',
        $curlErr
      );
    }
    
    curl_close($this->curl);
    
    return $this->response(
      'OK',
      'Successfully returned response',
      $reqResp
    );
  }
  
  private function response($type, $msg, $returnData = false) {
    return [
      'status' => $type,
      'message' => $msg,
      'data' => $returnData
    ];
  }
}
?>
