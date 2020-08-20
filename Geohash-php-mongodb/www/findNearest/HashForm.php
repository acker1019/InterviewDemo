<?php
	require_once "Border.php";

	class HashForm {
		public $form;
		public $form_use;

		function __construct($form_in, $type) {
			$this->form = $form_in;
			$this->set($type);
		}//end __construct

		function set($type) {
			switch($type) {
				case 'odd':
					$this->form_use = $this->form->odd;
					break;
				case 'even':
					$this->form_use = $this->form->even;
					break;
			}//end switch
		}//end set

		function left($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]][$pos[0]-1];
			$idx = strpos($this->form_use->border->left, $word);
			if($idx === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end left

		function right($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]][$pos[0]+1];
			$idx = strpos($this->form_use->border->right, $word);
			if($idx === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end right

		function top($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]-1][$pos[0]];
			$idx = strpos($this->form_use->border->top, $word);
			if($idx === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end top

		function bottom($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]+1][$pos[0]];
			$idx = strpos($this->form_use->border->bottom, $word);
			if($idx === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end bottom

		function lefttop($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]-1][$pos[0]-1];
			$idx1 = strpos($this->form_use->border->left, $word);
			$idx2 = strpos($this->form_use->border->top, $word);
			if($idx1 === false && $idx2 === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end lefttop

		function leftbottom($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]+1][$pos[0]-1];
			$idx1 = strpos($this->form_use->border->left, $word);
			$idx2 = strpos($this->form_use->border->bottom, $word);
			if($idx1 === false && $idx2 === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end leftbottom

		function righttop($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]-1][$pos[0]+1];
			$idx1 = strpos($this->form_use->border->right, $word);
			$idx2 = strpos($this->form_use->border->top, $word);
			if($idx1 === false && $idx2 === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end righttop

		function rightbottom($word, &$nei) {
			$pos = $this->getPos($word);
			$nei = $this->form_use->table[$pos[1]+1][$pos[0]+1];
			$idx1 = strpos($this->form_use->border->right, $word);
			$idx2 = strpos($this->form_use->border->bottom, $word);
			if($idx1 === false && $idx2 === false) {
				return false;
			} else {
				return true;
			}//end condi.
		}//end rightbottom

		private function getPos($word) {
			$length_y = count($this->form_use->table_single);
			$length_x = count($this->form_use->table_single[0]);
			for($i = 0 ; $i < $length_y ; $i++) {
				$idx = array_search($word, $this->form_use->table_single[$i]);
				if($idx !== false) {
					$pos[0] = $length_x + $idx;
					$pos[1] = $length_y + $i;
					return $pos;
				}//end condi.
			}//end loop
			return null;
		}//end function

		public static function drawTable($table) {
			$length_y = count($table);
			$length_x = count($table[0]);
			echo "<table>";
			for($j = 0 ; $j < $length_y ; $j++) {
				echo "<tr>";
				for($i = 0 ; $i < $length_x ; $i++) {
					echo "<td width='30'>" . $table[$j][$i] . "</td>";
				}//end loop i
				echo "</tr>";
			}//end loop j
			echo "</table>";
		}//end drawTable

		public static function createHashForm($width, $height) {
			$form = new stdClass();
			$form->odd = new stdClass();
			$form->odd->table_single = self::createSingleHashtable($width, $height);
			$form->odd->table = self::ExtTable($form->odd->table_single);
			$form->odd->border = self::getBorder($form->odd->table_single);
			$form->even = new stdClass();
			$form->even->table_single = self::ReverseTable($form->odd->table_single);
			$form->even->table = self::ExtTable($form->even->table_single);
			$form->even->border = HashForm::getBorder($form->even->table_single);
			$form->bitX = count($form->odd->table_single[0]);
			$form->bitY = count($form->odd->table_single);
			$form->hash_group_length = log10($form->bitX*$form->bitY) / log10(2);
			return $form;
		}//end createHashForm

		public static function createSingleHashtable($width, $height) {
			$alph = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
			$code_length = $width * $height;
			$depth = log($code_length) / log(2);
			$form;
			for($i = 0 ; $i < $code_length ; $i++) {
				$code = base_convert($i, 10, 2);
				$code = str_pad($code, $depth , "0", STR_PAD_LEFT);
				$start_x = 0;
				$end_x = $width - 1;
				$start_y = 0;
				$end_y = $height - 1;
				$switch = true;//even->true, odd->false
				for($j = 0 ; $j < $depth ; $j++) {
					$digit = $code[$j];
					if($switch) {
						//even
						$center_x = intdiv($start_x+$end_x, 2);
						if($digit == 0) {
							$end_x = $center_x;
						} else {
							$start_x = $center_x + 1;
						}//end condi.
					} else {
						//odd
						$center_y = intdiv($start_y+$end_y, 2);
						if($digit == 0) {
							$end_y = $center_y;
						} else {
							$start_y = $center_y + 1;
						}//end condi.
					}//end condi.
					$switch = !$switch;
				}//end loop j
				if($start_x==$end_x && $start_y==$end_y) {
					$form[$start_y][$start_x] = $alph{$i};
				} else {
					return null;
				}//end condi.
			}//end loop i
			return $form;
		}//end createSingleHashtable

		public static function ExtTable($Table) {
			$length_y = count($Table);
			for($i = 0 ; $i < $length_y ; $i++) {
				$ele = $Table[$i];
				$Table[$i] = array_merge($Table[$i], $ele);
				$Table[$i] = array_merge($Table[$i], $ele);
			}//end loop
			$ele = $Table;
			$Table = array_merge($Table, $ele);
			$Table = array_merge($Table, $ele);
			return $Table;
		}//end createHashtable

		public static function ReverseTable($Table) {
			$RTable;
			$length_y = count($Table);
			$length_x = count($Table[0]);
			for($j = 0 ; $j < $length_y ; $j++) {
				for($i = 0 ; $i < $length_x ; $i++) {
					$RTable[$length_x-$i-1][$length_y-$j-1] = $Table[$j][$i];
				}//end loop i
			}//end loop j
			for($j = 0 ; $j < $length_x ; $j++) {
				ksort($RTable[$j]);
			}//end loop j
			ksort($RTable);
			//ksort($RTable);
			return $RTable;
		}//end ReverseHashtable

		public static function getBorder($Table) {
			$length_y = count($Table);
			$length_x = count($Table[0]);
			$border = new Border();
			//top
			for($i = 0 ; $i < $length_x ; $i++) {
				$border->top .= $Table[0][$i];
			}//end loop
			// bottom
			for($i = 0 ; $i < $length_x ; $i++) {
				$border->bottom .= $Table[$length_y-1][$i];
			}//end loop
			//left
			for($i = 0 ; $i < $length_y ; $i++) {
				$border->left .= $Table[$i][0];
			}//end loop
			//right
			for($i = 0 ; $i < $length_y ; $i++) {
				$border->right .= $Table[$i][$length_x-1];
			}//end loop
			return $border;
		}//end getBorder
	}//end HashForm
?>