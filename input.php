<?php

include 'db_con.php';

$TOKEN = "821904545:AAHQDMbWf46pc1e77g_0hqQOJgj4eiR3TYA";
$usernamebot= "@cpthook_bot";

$debug = false;

function request_url($method)
{
	global $TOKEN;
	return "https://api.telegram.org/bot" . $TOKEN . "/". $method;
}

function get_updates($offset)
{
	$url = request_url("getUpdates")."?offset=".$offset;
	$resp = file_get_contents($url);
	$result = json_decode($resp, true);
	if ($result["ok"]==1)
	return $result["result"];
	return array();
}

function process_message($message)
{
	global $koneksi;
	$updateid = $message["update_id"];
	$message_data = $message["message"];
	// print_r($message_data);
	// if(isset($message_data["text"]))
	// 	echo "ini adalah text";
	// else{
	// 	print_r($message_data);
	// }
	// print_r($message_data);

	if (isset($message_data["text"])) {
		$chatid = $message_data["chat"]["id"];
		$text = $message_data["text"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$text','msg','1',NOW())");

		$fetch =  mysqli_query($koneksi, "SELECT MAX(id_inbox) as id_inbox FROM tb_inbox");
		$id = mysqli_fetch_assoc($fetch);
		$id_inbox =  $id["id_inbox"];
		$msg = mysqli_query($koneksi, "INSERT INTO tb_outbox VALUES (null,'$id_inbox','$chatid','$text','msg','1', NOW())");
		if ($msg) {
			mysqli_query($koneksi, "UPDATE tb_inbox set flag = '2' where id_inbox = $id_inbox ");	
		}
		
	}

	elseif (isset($message_data["photo"])) {
		$chatid = $message_data["chat"]["id"];
		$photo_id = $message_data["photo"]["0"]["file_id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$photo_id','img','1',NOW())");
		$fetch =  mysqli_query($koneksi, "SELECT MAX(id_inbox) as id_inbox FROM tb_inbox");
		$id = mysqli_fetch_assoc($fetch);
		$id_inbox =  $id["id_inbox"];
		mysqli_query($koneksi, "INSERT INTO tb_outbox VALUES (null,'$id_inbox','$chatid','$photo_id','img','1', NOW())");
		mysqli_query($koneksi, "UPDATE tb_inbox set flag = '2' where id_inbox = $id_inbox ");

	}
	elseif (isset($message_data["location"])) {
		$chatid = $message_data["chat"]["id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','location','loc','1',NOW())");
		$fetch =  mysqli_query($koneksi, "SELECT MAX(id_inbox) as id_inbox FROM tb_inbox");
		$id = mysqli_fetch_assoc($fetch);
		$id_inbox =  $id["id_inbox"];
		echo $chatid;
		mysqli_query($koneksi, "INSERT INTO tb_outbox VALUES (null,'$id_inbox','$chatid','https://goo.gl/maps/5Nu9WB2tbzvNRTEn9','loc','1', NOW())");
		mysqli_query($koneksi, "UPDATE tb_inbox set flag = '2' where id_inbox = $id_inbox ");
	}
	elseif (isset($message_data["document"])) {
		$chatid = $message_data["chat"]["id"];
		$file_name = $message_data["document"]["file_name"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$file_name','file','1',NOW())");
		$fetch =  mysqli_query($koneksi, "SELECT MAX(id_inbox) as id_inbox FROM tb_inbox");
		$id = mysqli_fetch_assoc($fetch);
		$id_inbox =  $id["id_inbox"];
		echo $chatid;
		mysqli_query($koneksi, "INSERT INTO tb_outbox VALUES (null,'$id_inbox','$chatid','http://ikd.ugm.ac.id/files/download/CV.doc','file','1', NOW())");
		mysqli_query($koneksi, "UPDATE tb_inbox set flag = '2' where id_inbox = $id_inbox ");	
	}


	
	return $updateid;
}

function process_one()
{
	global $debug;
	$update_id = 0;
	

	if (file_exists("last_update_id"))
		$update_id = (int)file_get_contents("last_update_id");

	$updates = get_updates($update_id);

	// jika debug=0 atau debug=false, pesan ini tidak akan dimunculkan
	if ((!empty($updates)) and ($debug) ) {
		echo "rn===== isi diterima rn";
		print_r($updates);
	}

	foreach ($updates as $message)
	{
		echo '-';
		$update_id = process_message($message);
	}
	file_put_contents("last_update_id", $update_id + 1);
}




// process_one();



$entityBody = file_get_contents('php://input');
$pesanditerima = json_decode($entityBody, true);
process_message($pesanditerima);


?>
