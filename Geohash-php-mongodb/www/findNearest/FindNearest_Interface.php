<?php
	require_once "DB_GeoHash.php";
	require_once "HashForm.php";
	require_once "ToolBox.php";

	class FindNearest_Interface{
		private $Geohash;
		private $debug_mode;
		private $time_use;
		private $log_time_usage;

		//0: host , 1: db
		function __construct() {
			switch(func_num_args()) {
				case 2:
					$this->Geohash = new DB_GeoHash(
						func_get_arg(0), func_get_arg(1));
				case 1:
					$this->Geohash = new DB_GeoHash(
						func_get_arg(0), DB_GeoHash::DEF_DB);
					break;
				default:
					break;
			}//end condi.
			$this->debug_mode = false;
			$this->time_use = null;
			$this->log_time_usage = false;
		}//end __construct

		function getLastTimeLog() {
			switch(func_num_args()) {
				case 0:
					return ($this->time_use == null) ? null : ($this->time_use."(sec)");
				case 1:
					if(func_get_arg(0)) {
						return ($this->time_use == null) ? null : $this->time_use;
					} else {
						return ($this->time_use == null) ? null : ($this->time_use."(sec)");
					}//end condi.
				default:
					return null;
			}//end condi.
		}//end getLastTimeLog

		function getCourierByShopLoc() {
			/*
			With the shop as the center, search the nearest courier.
			*/
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			//inputs
			$x;
			$y;
			$filter = [];
			$limit;
			switch(func_num_args()) {
				case 3:
					$x = func_get_arg(0);
					$y = func_get_arg(1);
					$limit = func_get_arg(2);
					break;

				case 4:
					$x = func_get_arg(0);
					$y = func_get_arg(1);
					$filter = func_get_arg(2);
					$limit = func_get_arg(3);
					break;

				default:
					return null;
			}//end condi.
			
			$candi_points = $this->Geohash->getCandiPoints($x, $y);
			$result = $this->getNearestPointFromCandi(
						$candi_points, $x, $y, $filter, $limit);

			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.

			return $result;
		}//end getCouriersByShop

		function updateCourierLoc($courier_id, $x, $y) {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			//delete if exist
			$this->Geohash->DelCourierById($courier_id);
			//insert new one
			$r = $this->Geohash->insert($courier_id, $x, $y);
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $r;
		}//end updateCourierLoc

		function deleteCourier($courier_id) {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			//delete if exist
			$r = $this->Geohash->DelCourierById($courier_id);
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $r;
		}//end deleteCourier

		function getCourierLoc($id) {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			$tar_node = $this->Geohash->QueryByCourierId($id);
			if($tar_node != null) {
				$points = $tar_node->points;
				$length = count($points);
				for($i = 0 ; $i < $length ; $i++) {
					if($points[$i]->courier_id == $id) {
						$r = ["courier_id" => $points[$i]->courier_id,
								"x" => $points[$i]->x,
								"y" => $points[$i]->y];
						if($this->log_time_usage) {
							$t2 = microtime(true);
							$this->time_use = $t2 - $t1;
						}//end condi.
						return $r;
					}//end loop
				}//end loop
			}//end condi.
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return null;
		}//end getCourierLoc

		// (void)
		// (limit)
		// (skip , limit)
		function dumpCourier() {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			$cursor;
			switch(func_num_args()) {
				case 0:
					$cursor = $this->Geohash->m_db->query([]);
					break;
				case 1:
					$cursor = $this->Geohash->m_db->query(
						[], ["limit"=> func_get_arg(0)] );
					break;
				case 2:
					$cursor = $this->Geohash->m_db->query(
						[],
						["skip"=> func_get_arg(0), "limit"=> func_get_arg(1)] );
					break;

				default:
					return;
			}//end switch
			$length = count($cursor);
			$points = [];
			for($i = 0 ; $i < $length ; $i++) {
				$points = array_merge($points, $cursor[$i]->points);
			}//end for
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $points;
		}//end getAllCourier

		//only needed when use db first time
		//or you want to format the meta
		function initDB() {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			$r = $this->Geohash->initDB();
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $r;
		}//end initDB

		//start load in meta to start service
		function launch() {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			$r = $this->Geohash->launch();
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $r;
		}//end launch

		//remove girds that have no any courier node
		//this func. is unnecessary in routine
		//just make db easy to view when needed
		function clean() {
			if($this->log_time_usage) {
				$t1 = microtime(true);
			}//end condi.
			$r = $this->Geohash->clean();
			if($this->log_time_usage) {
				$t2 = microtime(true);
				$this->time_use = $t2 - $t1;
			}//end condi.
			return $r;
		}//end clean

		function debug_mode($boolean) {
			$this->debug_mode = $boolean;
			$this->Geohash->debug_mode($boolean);
		}//end debug_mode

		function log_time_usage($bool) {
			$this->log_time_usage = $bool;
		}//end log_time_usage

		function showHashTable() {
			echo "odd:<br>";
			HashForm::drawTable($this->Geohash->getHashform()->form->odd->table_single);
			echo "<br>=================<br><br>";
			echo "even:<br>";
			HashForm::drawTable($this->Geohash->getHashform()->form->even->table_single);
		}//end showHashTable

		function getScale() {
			return $this->Geohash->getScale();
		}//end getSideLength

		function toString() {
			return "Geohash engine: <br>" . $this->Geohash->toString();
		}//end toString

		private function getNearestPointFromCandi($points, $x, $y, $filter, $limit) {
			//vars
			$length = count($points);
			$length_filter = count($filter);
			//do filter
			for($i = 0 ; $i < $length ; $i++) {
				for($j = 0 ; $j < $length_filter ; $j++) {
					if($points[$i]->courier_id == $filter[$j]) {
						array_splice($points, $i, 1);
						array_splice($filter, $j, 1);
					}//end condi.
				}//end loop j
			}//end loop i
			//calculate
			ToolBox::calDistance($points, $x, $y);
			ToolBox::sortPoints($points, 0, count($points)-1);
			//return result with limit amg
			if($limit == -1) {
				return $points;
			} else {
				$output = array_slice($points, 0, $limit);
				switch(count($output)) {
					case 0:
						return null;

					default:
						return $output;
				}//end condi.
			}//end condi.
		}//end getNearestPointFromCandi

	}//end FindNearest_Interface
?>
