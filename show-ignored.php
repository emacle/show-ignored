<?php
require 'vendor/autoload.php';

$api_url = 'http://localhost:8384';
$APIKey = 'xfsRu4txHuydQUambxxxxxxxxxxxxx';
$folderid = 'folderid-name';

$parent_str = '';
$client = new \GuzzleHttp\Client();
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Query;

function checkArrayRecursively($arr, $folderid, $APIKey, $api_url, $parent_str)
{
    if ($arr) {
        foreach ($arr as $value) {
            if($parent_str != '') {
                $filename = $parent_str . '/' . $value->name;
            } else {
                $filename = $value->name;
            }

            //  echo $filename . "\n";
            //     echo $api_url . '/rest/db/file?folder=' . $folderid . '&file=' . $filename . "\n";

            $filename = str_replace("+","%2B",$filename);
            $filename = str_replace("&","%26",$filename);
            $filename = str_replace("=","%3D",$filename);
            // echo $filename . "\n";

            $cli = new \GuzzleHttp\Client();
            try {

                 $res = $cli->request('GET', $api_url . '/rest/db/file', [
                    'query' => [
                        'folder' => $folderid,
                        'file' => $filename
                    ], 
                    'headers' => [
                        'X-API-Key' => $APIKey
                    ]
                ]);

                $obj = json_decode($res->getBody());
                // var_dump($obj->local->ignored);
                if($obj->local->ignored){
                    if($obj->local->type == 'FILE_INFO_TYPE_DIRECTORY'){
                        echo $filename . " (dir) local ignored.\n" ;
                    } else {
                        echo $filename . " (file) local ignored.\n" ;
                    }
                } else {
                    echo "local not ignored: " . $filename . "\n";
                }
            } catch (ClientException $e) {
                echo "path exception: " . $filename . "\n";
                // echo Psr7\Message::toString($e->getRequest());
                // echo Psr7\Message::toString($e->getResponse());
            }

            // sleep(1);
            if(isset($value->children)) {
                checkArrayRecursively($value->children, $folderid, $APIKey, $api_url, $filename);
            }

        }
    }
}

$response = $client->request('GET', $api_url . '/rest/db/browse?folder=' . $folderid , [
    'headers' => [
        'X-API-Key' => $APIKey
    ]
]);

// echo $response->getBody();
// echo $response->getStatusCode(); // 200
// echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
$object = json_decode($response->getBody());

checkArrayRecursively($object, $folderid, $APIKey, $api_url, $parent_str);
