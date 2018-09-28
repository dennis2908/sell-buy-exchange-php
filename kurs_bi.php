<?php
/*
Ambil Nilai Valuta Kurs Dari BI Versi 1.0
Tanggal: 2008-12-31 16:31
oleh: jinbatsu (http://www.nusansifor.com) 
*/
error_reporting (E_ALL);
//
// Ubah menjadi 3600 untuk cache 1 jam, ketika semuanya sudah berjalan normal.
// Menggunakan cache berarti tidak perlu membuka koneksi ke klikbca
// setiap kali halaman dibuka << ini PENTING! menghemat waktu, dan mengurangi proses server.
//
$nkurs['cachetime'] = 3600; /* ubah jadi 3600 atau lebih */
//
// menggunakan CURL, jika file_get_contents tidak bisa dihostingan Anda, baca manual PHP untuk selengkapnya
// Untuk Cara penggunaan file get content:
// file_get_contents($url, "r")
// Untuk Cara penggunaan CURL:
// curl_get_file_contents($url)
$url = "http://www.bi.go.id/web/id/Moneter/Kurs+Bank+Indonesia/Kurs+Transaksi";	
$handle_url_metod = file_get_contents($url, "r");
//$handle_url_metod = curl_get_file_contents($url);
$header = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Nilai Valuta Kurs Dari BI Versi 1.0</title>
			<script type="text/javascript">
				function win2() {
					window.open("http://www.bi.go.id/biweb/Templates/Moneter/kode2.aspx","Window1","menubar=no,scrollbars=no,status=no,width=200,height=325,toolbar=no");
				}
			</script>
			<style type="text/css">
			#kurs_bi {
				width: 350px;
				background: #FFFFFF;
				padding: 0;
				border: 0;
				font-size: 11px;
				font-family: Verdana, Arial, Helvetica, sans-serif;
			}
			#kurs_bi table {
				border: 0 solid #ccc;
				padding: 2px 2px;
			}
			#kurs_bi table tr td table {
				border: 1px solid #ccc;
				padding: 0;
			}
			#kurs_bi .style6 {
				font-size: 13px;
			}
			</style>
			</head>

			<body>

			<div id="kurs_bi">
';

$footer = '
			<br />
			Sumber: <a href="http://www.bi.go.id">Bank Indonesia (BI)</a>
			</div>
			</body>
			</html>
';	
	
	


// Dari sini kebawah, ubah kalau mengerti aja.
//$nkurs['curr'] = array ('USD', 'SGD', 'HKD', 'CHF', 'GBP', 'AUD', 'JPY', 'SEK', 'DKK', 'CAD', 'EUR', 'SAR', 'MYR', 'NZD', 'NOK', 'BUK', 'INR', 'KWD', 'PKR', 'PHP', 'LKR', 'THB', 'BND', 'CNY', 'KRW');


function curl_get_file_contents($URL) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $URL);
	$contents = curl_exec($c);
	curl_close($c);
	if ($contents) return $contents;
	else return FALSE;
}

$nkurs['scriptpath'] = dirname (__FILE__);
$nkurs['cachefile'] = $nkurs['scriptpath'] . '/cache.txt';
$output = "";
if (!file_exists ($nkurs['cachefile']) || !is_writable ($nkurs['cachefile'])){ die ('File cache.txt belum ada atau belum writable.<br />Buat file: <code>' . $nkurs['cachefile'] . '</code><br />Lalu CHMOD ke 666'); }
if (filemtime ($nkurs['cachefile']) <= ( time () - $nkurs['cachetime'] ) && $handle = $handle_url_metod)  {
	$output .= '<table cellspacing="1" cellpadding="0" border="0" bgcolor="#cccccc" align="center" width="100%">';
	function extract_unit($string, $start, $end) {
		$pos = stripos($string, $start);
		$str = substr($string, $pos);
		$str_two = substr($str, strlen($start));
		$second_pos = stripos($str_two, $end);
		$str_three = substr($str_two, 0, $second_pos);
		$unit = trim($str_three); // remove whitespaces
		return $unit;
	}
	$unit = extract_unit($handle, '<!---------------------- Kurs Transaksi Bank Indonesia ---------------------->', '<!----------------------End Kurs Transaksi Bank Indonesia ---------------------->');
	$hasil = explode("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\">", $unit);
	$hasil = $hasil[0];
	$hasil = str_replace('/Biweb/', 'http://www.bi.go.id/Biweb/', $hasil);
	$hasil = str_replace('/biweb/', 'http://www.bi.go.id/biweb/', $hasil);
	//$hasil = str_replace('KodeSingkatan', '', $hasil);
	$output .= $hasil;
	$i = 0;
	/*
	foreach($hasil1 as $hasil) {
		if ($i == 30) {continue;}
		$output .= "<tr>";
		$hasil = str_replace('untuk setiap', '', $hasil);
		$hasil = str_replace('1,00', '', $hasil);
		$hasil = str_replace('100,00', '', $hasil);
		$hasil = str_replace('border="1"', 'border="0"', $hasil);
		foreach($nkurs['curr'] as $ilangan) {
			$hasil = str_replace($ilangan, '', $hasil);
		}
		$output .= $hasil;
		//$output .= "<br />" . $i;
		//$output .= "</tr>";
		$i++;
	}
	*/
	$output .= '</table>';
	$tocache = $output;
	$handle = fopen ($nkurs['cachefile'], 'w');
	fwrite ($handle, $tocache);
	fclose ($handle);
	//$output .= $hasil2;
} else {
	$handle = file ($nkurs['cachefile']);
	foreach($handle as $out) {
		$output .= $out;
	}
}



echo $header;
echo $output;
echo $footer;
?>