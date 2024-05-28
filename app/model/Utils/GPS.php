<?php

namespace App\Model\Utils;

use App\Model\ApiConnections;
use App\Model\App as App;
use App\Model\Database\Entity\Url;
use GuzzleHttp\Client;
use MongoDB\BSON\Regex;
use Nette\Utils\Arrays as NetteArrays;

final class GPS
{

	public function gpsFromAdress($ulice, $cp, $mesto, $psc)
	{
		$adressa = $ulice .' '. $cp .' '.$mesto.' '.$psc;
		$adrs = str_replace(' ', '+',$adressa);

		return $this->getLatLng($adrs);

	}
	public function getLatLng($adresa)
	{
		$url = 'https://nominatim.openstreetmap.org/search?q='.$adresa.'&format=json&polygon=1&addressdetails=1';
		$guzzle = new Client();
		$output = $guzzle->get($url);

		$dotazy = (array)json_decode($output->getBody()->getContents(), true);

//		bdump($dotazy);

		$policko = [];
		foreach ($dotazy as $item){

			if(!empty($item['address']['house_number'])){
//				bdump($item['address']['house_number']);
				$pole = [];
				$pole['place_id'] = $item['place_id'];
				$pole['lat'] = $item['lat'];
				$pole['lon'] = $item['lon'];
				$pole['class'] = $item['class'];
				$pole['type'] = $item['type'];
				$pole['display_name'] = $item['display_name'];
				$pole['house_number'] = $item['address']['house_number'];
//			$pole['road'] = $item['address']['road'];
				$pole['suburb'] = $item['address']['suburb'];
				$pole['town'] = $item['address']['town'];
				$pole['municipality'] = $item['address']['municipality'];
				$pole['county'] = $item['address']['county'];
				$pole['state'] = $item['address']['state'];
				$pole['postcode'] = $item['address']['postcode'];
				$pole['iso17'] = $item['address']['ISO3166-2-lvl7'];
				$pole['iso16'] = $item['address']['ISO3166-2-lvl6'];
				$pole['country'] = $item['address']['country'];

				$policko[] = $pole;
			}



		}
		bdump($policko);


		return $pole;

	}
}