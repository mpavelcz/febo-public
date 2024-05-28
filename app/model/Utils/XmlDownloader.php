<?php declare(strict_types = 1);

namespace App\Model\Utils;

use App\Model\ApiConnections;
use App\Model\App;
use App\Model\Cesty;
use Aws\S3\S3Client;
use Matrix\Exception;
use MongoDB\BSON\Regex;
use Nette\Utils\Arrays as NetteArrays;
use Symfony\Component\Filesystem\Filesystem;

final class XmlDownloader
{

	public function ziskatXMLfeed($url, $soubor)
	{
		$fs = new Filesystem();
		$ch = curl_init();
		$source = $url;
		curl_setopt($ch, CURLOPT_URL, $source);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$data = curl_exec($ch);
		curl_close($ch);

		$destination = Cesty::FILES_LOCALHOST . '/' . $soubor;

		//ulozi feed
		$fs->dumpFile($destination, $data);

		return true;
	}

	public function vycteniDat($soubor, $next)
	{
		$vystup = [];
		$reader = new \XMLReader();
		$reader->open(Cesty::FILES_LOCALHOST . '/' . $soubor);
		if($reader->open(Cesty::FILES_LOCALHOST . '/' . $soubor) === TRUE){
			while ($reader->read() && $reader->name !== $next) ;
			while ($reader->name == $next) {
				$item = new \SimpleXMLElement($reader->readOuterXml(), LIBXML_NOCDATA);

//				bdump((int)$item['id'][0]);

				$xx = json_encode($item);
				$xx = (array) json_decode($xx);
				array_push($vystup, $xx);

				$reader->next($next);
			}
		}
		$reader->close();

		return $vystup;
	}


}