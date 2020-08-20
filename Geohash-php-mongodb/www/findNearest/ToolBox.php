<?php
	class ToolBox {
		public static function binary_search($x, $list, &$mid) {
		    $left = 0;
		    $right = count($list) - 1;

		    while ($left <= $right) {
		        $mid = floor(($left + $right)/2);
		        
		        if ($list[$mid] == $x) {
		            return $mid;
		        } elseif ($list[$mid] > $x) {
		            $right = $mid - 1;
		        } elseif ($list[$mid] < $x) {
		            $left = $mid + 1;
		        }
		    }

		    if(count($list) <= 0) {
		    	$mid = 0;
		    } else if($x > $list[$mid]) {
		    	$mid++;
		    }//end condi.
		    
		    return -1;
		}//end binary_search

		public static function sortPoints(&$points, $left, $right) {
			//check
			if($points == null) {
				return;
			} else {
				//pre-set
				$i = $left;
				$j = $right;
				$pivot = $points[($i + $j) / 2]->d;

				/* partition */
				while ($i <= $j) {
					while ($points[$i]->d < $pivot) {
						$i++;
					}//end loop i
					while ($points[$j]->d > $pivot) {
						$j--;
					}//end loop j
					if ($i <= $j) {
						$tmp = $points[$i];
						$points[$i] = $points[$j];
						$points[$j] = $tmp;
						$i++;
						$j--;
					}//end condi.
				}//end loop

				/* recursion */
				if ($left < $j) {
					self::sortPoints($points, $left, $j);
				}//end condi. left
				if ($i < $right) {
					self::sortPoints($points, $i, $right);
				}//end condi. right
			}//end condi.
		}//end sortPoints

		public static function calDistance(&$points, $tar_x, $tar_y) {
			$length = count($points);
			for($i = 0 ; $i < $length ; $i++) {
				$points[$i]->d = self::getDistance($points[$i], $tar_x, $tar_y);
			}//end loop
		}//end calDistance

		public static function getDistance($point, $x, $y) {
			return round( Sqrt( pow(($point->x - $x), 2) + pow(($point->y - $y), 2) ), 5);
		}//end getDistance

		public static function push_if_existed(&$array, &$var) {
			if(isset($var)) {
				array_push($array, $var);
				return true;
			}//end condi.
			return false;
		}//end push_if_existed

		public static function base2to16($bin) {
			$base16 = "";
			$alph = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
			$length = strlen($bin);
			$lost = 4 - ($length % 4);
			if($lost == 4) {
				$lost = 0;
			}//end condi.
			for($i = 0 ; $i < $lost ; $i++) {
				$bin = "0" . $bin;
			}//end loop
			$chunks = str_split($bin, 4);
			$length = count($chunks);
			for($i = 0 ; $i < $length ; $i++) {
				$index = base_convert($chunks[$i], 2, 10);
				$base16 .= $alph{$index};
			}//end loop
			return $base16;
		}//end base10to16

		public static function base2to32($bin) {
			$base32 = "";
			$alph = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
			$length = strlen($bin);
			$lost = 5 - ($length % 5);
			if($lost == 5) {
				$lost = 0;
			}//end condi.
			for($i = 0 ; $i < $lost ; $i++) {
				$bin = "0" . $bin;
			}//end loop
			$chunks = str_split($bin, 5);
			$length = count($chunks);
			for($i = 0 ; $i < $length ; $i++) {
				$index = base_convert($chunks[$i], 2, 10);
				$base32 .= $alph{$index};
			}//end loop
			return $base32;
		}//end base10to32
	}//end class ToolBox
?>