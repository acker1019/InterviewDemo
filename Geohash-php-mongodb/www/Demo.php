<?php
	/*
	GeoHash v2.0
	新function於下方Demo皆有更新

	說明:
	1. 建議把舊的DB整個drop掉，重新init
		a. 啟動mongo shell
			(安裝路徑)/mongo.exe
		b. 指令
			use CourierGeoHash
			db.dropDatabase()

	feature:
	1. 九宮格範圍已改成約4.5km的那個版本
	2. 取得一個格子經度和緯度的尺度：getScale()
	3. 列出DB中的送貨員資訊：dumpCourier()、dumpCourier($limit)、dumpCourier($skip, $limit)
	4. 需要 log_time_usage(true) 才會記錄時間
	5. 已對courier_id進行indexing，優化使用courier_id進行查詢時的耗費時間
	*/

	//請載入 FindNearest_Interface.php
	//其他檔案請放在與之相同目錄之下
	require_once "findNearest/FindNearest_Interface.php";

	//建立interface物件
	//需給予用作中mongodb的監聽位址
	mongo_link = "mongodb://root:123456@127.0.0.1:27017"
	$FN_interface = new FindNearest_Interface(mongo_link);

	//1. html格式 追蹤以自己為中心的9宮格內，送貨員節點的分布狀況
	//2. 紀錄function運算時間(usec)，可透過getLastTimeLog()取得，false則不紀錄
	//   getLastTimeLog(), getLastTimeLog(false) 回傳字串
	//   getLastTimeLog(true) 回傳float，單位皆為sec
	//debug_mode()預設為false
	//需設置為true才有作用
	$FN_interface->debug_mode(true);
	//是否紀錄時間
	$FN_interface->log_time_usage(true);

	//第一次執行此模組，需初始化資料庫裡的meta data
	//資料庫建置完成後不需再度執行
	//再次使用可初始化meta data
	echo $FN_interface->initDB();
	echo $FN_interface->getLastTimeLog();

	//將資料庫中的meta data載入FN_interface
	//每次實體化FindNearest_Interface後都需要執行
	echo $FN_interface->launch();
	echo $FN_interface->getLastTimeLog();

	//宣告等等用於範例的變數
	//設定店家為courier_9的位置開始位移
	$shop_lon;
	$shop_lat;
	//範例：新增/更新送貨員courier_0~courier_9的經緯度資訊
	//無論送貨員是否已存在資料庫中皆可使用
	//longitude應介於 120.06667 E ~ 121.98333 E 之間
	//latitude應介於 21.9 N ~ 25.3 N 之間
	//演算法內精度為小數後5位，多於位數四捨五入
	for($i = 0 ; $i < 10 ; $i++) {
		$longitude = rand(12006667, 12198333);
		$longitude /= 100000;
		$shop_lon = $longitude;
		$latitude = rand(2190000, 2530000);
		$latitude /= 100000;
		$shop_lat = $latitude;
		//pars: 送貨員ID, 經度, 緯度
		$r = $FN_interface->updateCourierLoc("courier_".$i, $longitude, $latitude);
		var_dump($r);
		echo $FN_interface->getLastTimeLog();
	}
	//設定店家位置與courier_9的距離
	$shop_lon += 0.01;
	$shop_lat += 0.0005;

	//html格式 顯示資料庫中目前使用的hash table
	$FN_interface->showHashTable();

	//取得一格的長寬，所換算的經緯度位移量，以[dlon, dlat]的格式
	//九宮格的邊長，所換算的經緯度，就會是回傳值的三倍
	var_dump($FN_interface->getScale());

	//輸入店家座標，查詢以店家為中心的九宮格
	//(約4.5平方公里)
	//內的送貨員節點
	//回傳資料為一陣列(即使只有一個送貨員)
	//最大清單長度自訂義，為-1時，回傳所有結果
	//pars: 經度, 緯度, limit
	$r = $FN_interface->getCourierByShopLoc($shop_lon, $shop_lat, -1);
	var_dump($r);
	echo $FN_interface->getLastTimeLog();

	//以送貨員ID查找並刪除該送貨員節點
	$r = $FN_interface->deleteCourier("courier_3");
	var_dump($r);
	echo $FN_interface->getLastTimeLog();

	//以送貨員ID取得送貨員所在位置
	$r = $FN_interface->getCourierLoc("courier_1");
	var_dump($r);
	echo $FN_interface->getLastTimeLog();
	$r = $FN_interface->getCourierLoc("courier_3");
	var_dump($r);
	echo $FN_interface->getLastTimeLog();

	//清除資料庫中沒有任何送貨員節點的geohash nodes
	//此動作為非必要
	//不影響程式運作
	//只為了需要肉眼確認資料庫時有個乾淨的環境
	var_dump($FN_interface->clean());
	echo $FN_interface->getLastTimeLog();

	//取得運行中的Geohash引擎成員變數
	$r = $FN_interface->toString();
	var_dump($r);

	//dump送貨員:
	//由於是迭代的進行遍歷
	//clean之後有助提升此function的搜尋速度(資料量大時)
	//輸出所有送貨員
	var_dump($FN_interface->dumpCourier());

	//只輸出3個
	//var_dump($FN_interface->dumpCourier(3));

	//從第三個(2號元素)開始，只顯示3個
	//var_dump($FN_interface->dumpCourier(2, 3));

	echo $FN_interface->getLastTimeLog();
?>
