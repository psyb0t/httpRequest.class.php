# httpRequest.class.php
Easily perform general HTTP requests in PHP.

### Requirements
* php5
* php5-curl

### Usage

Initialization
```php
$req = new httpRequest('http://example.org/itemPage.html');
```
Make a POST request
```php
$req->setPOST($rawPostData);
```
Set the referrer URL
```php
$req->setReferrerUrl('http://example.org/?search=item');
```
Set the user agent string
```php
$req->setUserAgent('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.14) '.
  'Gecko/20080530 Firefox/2.0.0.14 Flock/1.2.1');
```
Set a random user agent from a file
```php
$req->userAgentsFile = 'fileName'; // By default 'userAgents' file is used
$req->setRandUserAgent();
```
Set the request timeout in seconds
```php
$req->setTimeout(15); // The default timeout is 30s
```
Set the file to be used to store and send cookies
```php
$req->setCookieFile('cookiesFile');
```
Set cookie contents in header
```php
$req->setCookieContents('fruit=apple; colour=red');
```
Specify wether or not to follow redirects
```php
$req->followLocation(false); // Default is true
```
Set connection proxy
```php
$req->setProxy('socks5://127.0.0.1:9050'); // Supported types are http, https, socks4 and socks5
```
Set connection headers
```php
$req->setHeaders([
  'HTTP_X_REQUESTED_WITH: XMLHttpRequest',
  'Accept-Language: da, en-gb;q=0.8, en;q=0.7'
]);
```
Execute the request
```php
$response = $req->exec();
```
The request response will contain an object with the following keys:
* status (__ERROR__ | __OK__)
* message
* data