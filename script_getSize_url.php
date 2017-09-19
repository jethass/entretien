<?php
 
 
$url = $argv[1]; //1er argument passé au fichier via la command line
 
$url_validation = filter_var($url, FILTER_VALIDATE_URL);
 
if(!$url_validation)
    throw new Exception("Your URL is not valid.\n");
 
$request_tot = 0;
$length_tot = 0;
 
$headers = get_headers($url, true); //le 2ème paramètre permet de rendre le tableau associatif.
 
if (!preg_match('#text/html#i', $headers['Content-Type'])) {
    $request_tot++;
    $length_tot += getLength($url, $headers);
} else {
    $c = curl_init($url); //initialisation cURL en passant l'URL
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true); //cette option permet de retourner le contenu de la requête dans une variable.
    $html = curl_exec($c); //lancé de la requête
    //var_dump($html);
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
     
    $xpath = new DOMXpath($doc);
     
    $css = gentLengthByElement('link', $xpath);
    $img = gentLengthByElement('img', $xpath);
    $script = gentLengthByElement('script', $xpath);
     
    $request_tot+= $css['request'] + $img['request'] + $script['request'];
    $length_tot += $css['length'] + $img['length'] + $script['length'];
}
 
 
echo "Total Requests : $request_tot \n";
echo "Total length : $length_tot octets \n";
echo "URL : $url \n";
 
 
 
/**
 * Get a resource length
 */
function getLength($url, $headers = null)
{
    if(isset($headers['Content-Length'])) {
        return (int)$headers['Content-Length'];
    }
     
    /* on envoie la requête cURL si le header ne renvoie pas le poids */
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_exec($c);
     
    return curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
}
 
/**
 * Get length and requests by xHtml element
 */
function gentLengthByElement($element, DOMXpath $xpath)
{
    $length_element = 0;
    $request_element = 0;
     
    $elements = $xpath->query('//'.$element);
 
    foreach($elements as $entity) {
        $src = $entity->getAttribute('src');
        if(empty($src))
            continue;
         
        $length_element += getLength($src);
        $request_element++;
    }
     
    return array('request' => $request_element, 'length' => $length_element);
}