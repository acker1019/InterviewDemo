<?php
	class mDB {
		//consts
		public $mDB_host;
		public $db;
		public $coll;

		//global vars
		private $manager;
		private $bulk;
		private $s_id;

		//$host, $db, $coll
		function __construct() {
			switch(func_num_args()) {
				case 3:
					$this->coll = func_get_arg(2);
				case 2:
					$this->db = func_get_arg(1);
				case 1:
					$this->mDB_host = func_get_arg(0);
					echo $this->mDB_host;
					echo "<br>";
					$this->bulk = new \MongoDB\Driver\BulkWrite;
					$this->manager = new \MongoDB\Driver\Manager($this->mDB_host);
					break;
				default:
					break;
			}//end condi.
		}//end __construct

		public function switchColl($coll) {
			$this->coll = $coll;
		}//end switchColl

		public function get_s_id() {
			return $this->s_id;
		}//end get_s_insert_id

		public function s_insert($doc) {
			$this->s_id = $this->bulk->insert($doc);
			return $this->submit();
		}

		public function s_update($target_doc, $new_doc) {
			$this->s_id = $this->bulk->update($target_doc, ['$set'=>$new_doc]);
			return $this->submit();
		}

		public function s_delete($doc) {
			$this->s_id = $this->bulk->delete($doc);
			return $this->submit();
		}

		public function getBulk() {
			return $this->bulk;
		}

		public function submit() {
			try {
				$r = $this->manager->executeBulkWrite(
			    	$this->getDataLoc(), $this->bulk);
				unset($this->bulk);
				$this->bulk = new \MongoDB\Driver\BulkWrite;
			    return $r;
			} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
			    $result = $e->getWriteResult();

			    // Check if the write concern could not be fulfilled
			    if ($writeConcernError = $result->getWriteConcernError()) {
			        printf("%s (%d): %s\n",
			            $writeConcernError->getMessage(),
			            $writeConcernError->getCode(),
			            var_export($writeConcernError->getInfo(), true)
			        );
			    }

			    // Check if any write operations did not complete at all
			    foreach ($result->getWriteErrors() as $writeError) {
			        printf("Operation#%d: %s (%d)\n",
			            $writeError->getIndex(),
			            $writeError->getMessage(),
			            $writeError->getCode()
			        );
			    }
			} catch (MongoDB\Driver\Exception\Exception $e) {
			    printf("Other error: %s\n", $e->getMessage());
			    exit;
			}//end try
		}//end submit

		//arg0:filter, arg1:opts
		public function query() {
			switch (func_num_args()) {
				case 0:
					$query = new \MongoDB\Driver\Query([]);
					break;
				case 1:
					$query = new \MongoDB\Driver\Query(func_get_arg(0));
					break;
				case 2:
					$query = new \MongoDB\Driver\Query(func_get_arg(0), func_get_arg(1));
					break;
				default:
					return null;
			}//end switch
			$cursor = $this->manager->executeQuery($this->getDataLoc(), $query);
			$cursor = iterator_to_array($cursor);
			if(count($cursor) == 0) {
				$cursor = null;
			}else if(count($cursor) == 1) {
				$cursor = $cursor[0];
			}//end condi.
			return $cursor;
		}//end query

		public function getDataLoc() {
			return $this->db . "." . $this->coll;
		}//end getDataLoc

		public function ensureIndex($coll, $key) {
			$command = new MongoDB\Driver\Command([
			    "createIndexes" => $coll,
			    "indexes"       => [[
			        "name" => $key . "_1",
			        "key"  => [ $key => 1],
			        "ns"   => $this->db . "." . $coll,
			    ]],
			]);
			$result = $this->manager->executeCommand($this->db, $command);
			return $result->toArray();
		}//end ensureIndex
	}//end class mDB
?>
