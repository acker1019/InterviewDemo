<?php
	require_once "mongoDB.php";
	require_once "HashForm.php";
	require_once "ToolBox.php";

	class DB_GeoHash {
		//db.collection
		const DEF_DB = "CourierGeoHash";
		const DEF_META_COLL = "Meta";
		const DEF_DATA_COLL = "Data";

		public $m_db;
		private $debug_mode;

		public $META_COLL;
		public $DATA_COLL;

		private $border_top;
		private $border_bottom;
		private $border_left;
		private $border_right;

		private $fix_val;

		private $accuracy;
		private $hashform;

		function __construct($host, $db) {
			$this->META_COLL = self::DEF_META_COLL;
			$this->DATA_COLL = self::DEF_DATA_COLL;
			$this->m_db = new mDB($host, $db, $this->META_COLL);
			$this->debug_mode = false;
		}//end __construct

		function initDB() {
			$version = ["_id" => "version", "value" => "v2.0"];
			$algorithm = ["_id" => "algorithm",
							"value" => "Geohash Peano - longitude divide first"];
			$accuracy = ["_id" => "accuracy", "value" => "16"];
			$border = ["_id" => "border",
						"top" => "25.3",
						"bottom" => "21.9",
						"left" => "120.06667",
						"right" => "121.98333"];

			$check_str = "";

			$this->m_db->switchColl($this->META_COLL);

			$this->m_db->s_delete(["_id" => $version["_id"]]);
			$this->m_db->s_delete(["_id" => $algorithm["_id"]]);
			$this->m_db->s_delete(["_id" => $accuracy["_id"]]);
			$this->m_db->s_delete(["_id" => $border["_id"]]);
			$this->m_db->s_delete(["_id" => "hashform"]);

			$r = $this->m_db->s_insert($version);
			$check_str .= " -- " . $version["_id"] . ": " . $version["value"]
					. " - insert " . ($r ? "succeeded" : "failed") . "<br>";
			$r = $this->m_db->s_insert($algorithm);
			$check_str .= " -- " . $algorithm["_id"] . ": " . $algorithm["value"]
					. " - insert " . ($r ? "succeeded" : "failed") . "<br>";
			$r = $this->m_db->s_insert($accuracy);
			$check_str .= " -- " . $accuracy["_id"] . ": " . $accuracy["value"]
					. " - insert " . ($r ? "succeeded" : "failed") . "<br>";
			$r = $this->m_db->s_insert($border);
			$check_str .= " -- " . $border["_id"] . ": " . "top=" . $border["top"]
										. " / bottom=" . $border["bottom"]
										. " / left=" . $border["left"]
										. " / right=" . $border["right"]
					. " - insert " . ($r ? "succeeded" : "failed") . "<br>";

			$new_hashform = HashForm::createHashForm(4, 4);
			$new_hashform->_id = "hashform";
			$r = $this->m_db->s_insert($new_hashform);
			$check_str .= " -- hashform" . " - created " . ($r ? "succeeded" : "failed") . "<br>";
			$r = $this->m_db->ensureIndex(self::DEF_DATA_COLL, "points.courier_id");
			$r = print_r($r, true);
			$check_str .= " -- Build Index:<br>" . $r;
			return $check_str;
		}//end initDB

		function launch() {
			$this->m_db->switchColl($this->META_COLL);
			$cursor = $this->m_db->query(["_id" => "accuracy"]);
			$this->accuracy = $cursor->value;
			$cursor = $this->m_db->query(["_id" => "border"]);
			$this->border_top = $cursor->top;
			$this->border_bottom = $cursor->bottom;
			$this->border_left = $cursor->left;
			$this->border_right = $cursor->right;
			$cursor = $this->m_db->query(["_id" => "hashform"]);
			$this->hashform = new HashForm($cursor, 'even');

			return "<br> -- meta loaded<br>";
		}//end launch

		function clean() {
			$this->m_db->switchColl($this->DATA_COLL);
			$cursor = $this->m_db->s_delete(["points" => []]);
			return $cursor;
		}//end clean

		function debug_mode($boolean) {
			$this->debug_mode = $boolean;
		}//end debug_mode

		function getHashform() {
			return $this->hashform;
		}//end getHashform

		function toString() {
			$info = var_export(get_object_vars($this), true);
			return "DEF_DB = " . self::DEF_DB
					. "<br>DEF_META_COLL = " . self::DEF_META_COLL
					. "<br>DEF_DATA_COLL = " . self::DEF_DATA_COLL
					. "<br>Object Info: "
					. $info;
		}//end toString

		function getCandiPoints($tar_x, $tar_y) {

			$points = [];
			$tar_node = $this->searchNode($tar_x, $tar_y);

			if($tar_node != null && $tar_node->points != null) {
				$points = array_merge($points, $tar_node->points);
			}//end condi.

			$addr = $this->getAddr($tar_x, $tar_y);
			$nei_nodes = $this->searchNeighbour($addr);
			$length = count($nei_nodes);

			for($i = 0 ; $i < $length ; $i++) {
				$points = array_merge($points, $nei_nodes[$i]->points);
			}//end loop

			return $points;
		}//end getPoints

		private function getAddr($tar_x, $tar_y) {
			$hashcode = "";
			$length = ($this->border_top - $this->border_bottom) / 2;
			$length = round($length, 5);
			$centerY = ($this->border_top + $this->border_bottom) / 2;
			$centerY = round($centerY, 5);
			$centerX = ($this->border_left + $this->border_right) / 2;
			$centerX = round($centerX, 5);
			//hash to accuracy
			$times = $this->accuracy / 2;
			for($i = 0 ; $i < $times ; $i++) {
				$length /= 2;
				$length = round($length, 5);
				if($tar_x < $centerX) {
					$hashcode .= 0;
					$centerX -= $length;
				} else {
					$hashcode .= 1;
					$centerX += $length;
				}//end condi.
				if($tar_y < $centerY) {
					$hashcode .= 0;
					$centerY -= $length;
				} else {
					$hashcode .= 1;
					$centerY += $length;
				}//end condi.
			}//end for
			return ToolBox::base2to16($hashcode);
		}//end getAddr

		private function getNeiNodeCode($center_code) {
			//check
			if($center_code != null) {
				$nei_codes = [];
				$pointer = strlen($center_code) - 1;
				$new_code = $this->getFixedNei($center_code, $pointer, 'tl');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'lb');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'tr');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'br');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 't');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'l');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'b');
				array_push($nei_codes, $new_code);
				$new_code = $this->getFixedNei($center_code, $pointer, 'r');
				array_push($nei_codes, $new_code);
				return $nei_codes;
			} else {
				return null;
			}//end condi.
		}//end getNeiNodeCode

		private function getFixedNei($code, $pointer, $direction) {
			if($pointer >= 0) {
				if($pointer % 2 == 1) {
					$this->hashform->set('even');
				} else {
					$this->hashform->set('odd');
				}//end condi. even/odd
				$isCross;
				$nei;
				switch($direction) {
					case 'tl':
						$isCross = $this->hashform->lefttop($code{$pointer}, $nei);
						break;
					case 'lb':
						$isCross = $this->hashform->leftbottom($code{$pointer}, $nei);
						break;
					case 'tr':
						$isCross = $this->hashform->righttop($code{$pointer}, $nei);
						break;
					case 'br':
						$isCross = $this->hashform->rightbottom($code{$pointer}, $nei);
						break;
					case 't':
						$isCross = $this->hashform->top($code{$pointer}, $nei);
						break;
					case 'l':
						$isCross = $this->hashform->left($code{$pointer}, $nei);
						break;
					case 'b':
						$isCross = $this->hashform->bottom($code{$pointer}, $nei);
						break;
					case 'r':
						$isCross = $this->hashform->right($code{$pointer}, $nei);
						break;

					default:
						return null;
				}//end condi.
				$code{$pointer} = $nei;
				if($isCross) {
					return $this->getFixedNei($code, --$pointer, $direction);
				} else {
					return $code;
				}//end condi.
			} else {
				return $code;
			}//end condi.
		}//end getFixedNei

		function searchNode($tar_x, $tar_y) {
			$this->m_db->switchColl($this->DATA_COLL);
			$tar_hash = $this->getAddr($tar_x, $tar_y);
			$cursor = $this->m_db->query(["_id" => $tar_hash]);
			if($this->debug_mode) {
				var_dump($tar_hash);
				var_dump($cursor);
			}//end condi.
			return $cursor;
		}//end searchNode

		function searchNeighbour($center_code) {
			$nei_nodes = [];
			$nei_codes = $this->getNeiNodeCode($center_code);
			$this->m_db->switchColl($this->DATA_COLL);
			$l = count($nei_codes);

			for($i = 0 ; $i < $l ; $i++) {
				$cursor = $this->m_db->query(["_id" => $nei_codes[$i]]);

				if($this->debug_mode) {
					var_dump($nei_codes[$i]);
					var_dump($cursor);
				}//end condi.

				if($cursor != null) {
					array_push($nei_nodes, $cursor);
				}//end condi.
			}//end loop

			return $nei_nodes;
		}//end searchNeighbour

		function insert($courier_id, $x, $y) {
			if($x < $this->border_left || $x > $this->border_right
				|| $y < $this->border_bottom || $y > $this->border_top) {
				return false;
			} else {
				$this->m_db->switchColl($this->DATA_COLL);
				$hashcode = $this->getAddr($x, $y);
				$cursor = $this->m_db->query(["_id" => $hashcode]);
				$new_point = ["courier_id" => $courier_id, "x" => $x, "y" => $y];
				if($cursor == null) {
					$this->m_db->s_insert([
						"_id" => $hashcode,
						"points" => [ $new_point ]
					]);
				} else {
					array_push($cursor->points, $new_point);
					$this->m_db->s_update(
						["_id" => $hashcode],
						$cursor
					);
				}//end condi.
				return true;
			}//end condi.
		}//end insert

		function DelCourierById($courier_id) {
			$this->m_db->switchColl($this->DATA_COLL);
			$bulk = $this->m_db->getBulk();
			$bulk->update(
				[],
				['$pull'=> ["points"=> ["courier_id"=> $courier_id]]],
				["multi"=> true] );
			return $this->m_db->submit();
		}//end DelCourierById

		function QueryByCourierId($id) {
			$this->m_db->switchColl($this->DATA_COLL);
			$cursor = $this->m_db->query([
				"points" => ['$elemMatch' => ["courier_id" => $id]] ]);
			return $cursor;
		}//end QueryByCourierId

		function getScale() {
			$width = ($this->border_right - $this->border_left);
			$height = ($this->border_top - $this->border_bottom);

			$times = $this->accuracy / 2;
			$times = pow(2, $times);

			$width = $width/$times;
			$width = round($width*100000.0)/100000.0;

			$height = $height/$times;
			$height = round($height*100000.0)/100000.0;

			return ["dlon"=> $width, "dlat"=> $height];
		}//end getScale
	}//end class
?>
