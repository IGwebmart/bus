<?php
	$company_array = ["MinskLine"];
	$get_city_array = ["Бобруйск","Минск",];


	class Trans {
		function __construct ($company, $date) {
			$this->company = $company;
			$this->date = $date;
		} 

		function getCountryId ($city) {
			$get_data = file_get_contents($this->getUrl()["url_city"]);
	 		$data_result = json_decode($get_data, true);
	 		foreach ($data_result as $item) {
	 			if($item["text"] == $city) {
	 				$result = $item["value"];
	 				return $result;
	 			}
	 		}
		}

		function getUrl(){
			switch ($this->company) {
				case 'MinskLine':
					$company_url = "http://minsk-bobruisk.by/cp/api/v1/ap/reis?citya=".$this->citya."&cityb=".$this->cityb."&date=".$this->date."";
					$company_url_city = "http://minsk-bobruisk.by/cp/api/v1/ap/citya";
					break;
				case 'AutoJet':
					$company_url = "https://routebysaas.saas.carbus.io/api/search?from_id=c625144&to_id=c630468&calendar_width=30&date=2021-11-27&passengers=1";
					$company_url_city = "http://minsk-bobruisk.by/cp/api/v1/ap/citya";
					break;
			}
			$company_urls = ["url"=>$company_url, "url_city"=>$company_url_city];
			return $company_urls;
		}

		function getData() {
			$get_data = file_get_contents($this->getUrl()["url"]);
 			$result = json_decode($get_data, true);
 			return $result;
		}
	}

	$get_date = $_GET["date"];

	$bus_race = [];
	$result_race = "";

	foreach ($company_array as $company){
		$trans = new Trans($company, $get_date);
		$get_citya = $trans->getCountryId($get_city_array[$_GET["start"]-1]);
		$get_cityb = $trans->getCountryId($get_city_array[$_GET["end"]-1]);
		$trans->citya = $get_citya;
		$trans->cityb = $get_cityb;

		foreach ($trans->getData() as $item) {
			if($item["FreePlace"] > 0) {
				$bus_race[] = [
					"company"=>$company,
					"date_reis"=>$item["date_reis"],
					"time_reis"=>$item["time_reis"],
					"FreePlace"=>$item["FreePlace"],
					"PlaceInReis"=>$item["PlaceInReis"],
				];
			}
		}
	}

	foreach ($bus_race as $item) {
		if($item["FreePlace"] > 0) {
			$result_race .= '
				<div class="results-item">
					<a href="#"></a>
					<div class="results-way">
						<span class="title">Маршрут:</span>
						<span class="text">Минск - Бобруйск</span>
					</div>
					<div class="results-company">
						<span class="title">Компания:</span>
						<span class="text">'.$item["company"].'</span>
					</div>
					<div class="results-date">
						<span class="title">Дата:</span>
						<span class="text">'.$item["date_reis"].'</span>
					</div>
					<div class="results-time">
						<span class="title">Время:</span>
						<span class="text">'.$item["time_reis"].'</span>
					</div>
					<div class="results-placecount">
						<span class="title">Места:</span>
						<span class="text">'.$item["FreePlace"].'/'.$item["PlaceInReis"].'</span>
					</div>
					<div class="results-price">
						<span class="title">Цена:</span>
						<span class="text">10 руб</span>
					</div>
				</div>
			';
		}
	}

	echo $result_race;
?>