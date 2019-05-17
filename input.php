<?php

include 'db_con.php';

global $koneksi;
$koneksi->set_charset('utf8mb4');
$TOKEN = "821904545:AAHQDMbWf46pc1e77g_0hqQOJgj4eiR3TYA";
$usernamebot= "@cpthook_bot";

$debug = false;

function request_url($method)
{
	global $TOKEN;
	return "https://api.telegram.org/bot" . $TOKEN . "/". $method;
}

function process_message($message)
{
	global $koneksi;
	$updateid = $message["update_id"];
	$message_data = $message["message"];
	if (isset($message_data["text"])) {
		$chatid = $message_data["chat"]["id"];
		$text = $message_data["text"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$text','msg','1',NOW())");

	}

	elseif (isset($message_data["photo"])) {
		$chatid = $message_data["chat"]["id"];
		$photo_id = $message_data["photo"]["0"]["file_id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$photo_id','img','1',NOW())");

	}
	elseif (isset($message_data["location"])) {
		$chatid = $message_data["chat"]["id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','location','loc','1',NOW())");
		
	}
	elseif (isset($message_data["document"])) {
		$chatid = $message_data["chat"]["id"];
		$file_name = $message_data["document"]["file_name"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$file_name','file','1',NOW())");
		
	}

	elseif (isset($message_data["sticker"])) {
		$chatid = $message_data["chat"]["id"];
		$file_id = $message_data["sticker"]["file_id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$file_id','stc','1',NOW())");
		
	}

	elseif (isset($message_data["contact"])) {
		$chatid = $message_data["chat"]["id"];
		$first_name = $message_data["contact"]["first_name"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$first_name','ctc','1',NOW())");
		
	}

	elseif (isset($message_data["video"])) {
		$chatid = $message_data["chat"]["id"];
		$file_id = $message_data["video"]["file_id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$file_id','vdo','1',NOW())");
		
	}

	elseif (isset($message_data["voice"])) {
		$chatid = $message_data["chat"]["id"];
		$file_id = $message_data["voice"]["file_id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','$file_id','vic','1',NOW())");
		
	}

	else{
		$chatid = $message_data["chat"]["id"];
		mysqli_query($koneksi, "INSERT INTO tb_inbox VALUES (null,'$chatid','format belum diketahui','unk','1',NOW())");
		
	}

	return $updateid;
}

$entityBody = file_get_contents('php://input');
$pesanditerima = json_decode($entityBody, true);
process_message($pesanditerima);

?>
