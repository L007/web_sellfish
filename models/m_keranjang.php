<?php 
class Keranjang
{
	
	public $id_produk;
	public $jumlah;
	
	public $nama_produk;
	public $harga;
	public $jumlah_stok;
	public $cabang;
	public $foto_produk;
	public $deskripsi;
	//public $kodeUnik;
	function __construct($id_produk,$nama_produk,$harga,$jumlah_stok,$cabang,$foto_produk,$deskripsi,$jumlah)
	{
		
		$this->id_produk=$id_produk;
		$this->jumlah=$jumlah;
		$this->nama_produk=$nama_produk;
		$this->harga=$harga;
		$this->jumlah_stok=$jumlah_stok;
		$this->cabang=$cabang;
		$this->foto_produk=$foto_produk;
		$this->deskripsi=$deskripsi;


	}

	public static function showCart($id_produk){
		//$i=0;
		$id=implode(",", $id_produk);

		$list=[];

		$db = DB::getInstance();

		//$req = $db->query("SELECT * FROM produk where id_produk in ($id)");
		for ($i=0; $i < count($_SESSION["id_produk"]); $i++) { 
			$req = $db->query("SELECT * 
				FROM produk p JOIN users u
				on p.id_user=u.id_user
				where id_produk=".$_SESSION['id_produk'][$i]);

		/*	foreach ($req->fetchAll() as $post) {
				$list[$i] = new Keranjang($post['id_produk'],$post['nama_produk'],$post['harga'],$post['jumlah_stok'],
					$post['cabang'],$post['foto_produk'],$post['deskripsi'],$_SESSION["jumlah"][$i]
					);
				//$i++;
}*/

foreach ($req as $item) {
	$list[$i]=array(
		'id_produk'=>$item['id_produk'],
		'nama_produk'=>$item['nama_produk'],
		'harga'=>$item['harga'],
		'jumlah_stok'=>$item['jumlah_stok'],
		'foto_produk'=>$item['foto_produk'],
		'deskripsi'=>$item['deskripsi'],
		'jumlahBeli'=>$_SESSION["jumlah"][$i],
		'penjual'=>$item['nama']
		);
}

}




return $list;
}

public static function bayarCart($id_user,$id_produk,$jumlah){
	$db = DB::getInstance();
	$insert1 = $db->query("INSERT INTO orders (id_order, id_user,tanggal,status) 
		VALUES (NULL, '".$id_user."',curdate(),'belum bayar')");

	$id_order;
	$select1=$db->query("SELECT * from orders where id_user=$id_user order by id_order DESC LIMIT 0,1");
	foreach ($select1->fetchAll() as $post) {
		$id_order=$post["id_order"];
	}


	for ($i=0; $i < count($_SESSION["id_produk"]) ; $i++) { 
		$insert2 = $db->query("INSERT INTO 
			`detail_order`(`id_order`, `id_produk`, `jumlah`, `total_harga`, `tanggal`) 
			VALUES ($id_order,".$_SESSION['id_produk'][$i].",".$_SESSION['jumlah'][$i].",
				(SELECT harga from produk where id_produk=".$_SESSION['id_produk'][$i].")*".$_SESSION['jumlah'][$i].",curdate())");
	}

	unset($_SESSION['id_produk']);
	unset($_SESSION['jumlah']);
/*
		$kodeUnik=$db->query("SELECT * from orders where id_user=$id_user order by id_order DESC LIMIT 0,1");
		foreach ($kodeUnik->fetchAll() as $post) {
			//$id_order=$post["id_order"];
			$unik=array('unik'=>$post["id_order"]

				);
}*/
return $insert2;
}

public function detailTransaksiPembeli($id_order){
	$list=[];
	$db = DB::getInstance();

	$req = $db->query("SELECT p.nama_produk,dp.jumlah,dp.total_harga,dp.tanggal FROM detail_order dp
		JOIN produk p on dp.id_produk=p.id_produk
		WHERE id_order=$id_order");

/*	foreach ($req->fetchAll() as $post) {

		$list[] = new Laporan("",$post['nama_produk'],"",$post['total_harga'],"",$post['tanggal'],"","","","",$post['jumlah']
			);
}*/

foreach ($req as $item) {
	$list[]=array(
		'nama_produk'=>$item['nama_produk'],
		'total_harga'=>$item['total_harga'],
		'tanggal'=>$item['tanggal'],
		'jumlahBeli'=>$item['jumlah']


		);
}


return $list;
}

public function detailTransaksiAdmin($id_order){
	$list=[];
	$db = DB::getInstance();

	$req = $db->query("SELECT p.nama_produk,dp.jumlah,dp.total_harga,dp.tanggal,u.nama FROM detail_order dp
		JOIN produk p on dp.id_produk=p.id_produk JOIN users u on p.id_user=u.id_user

		WHERE id_order=$id_order");

/*	foreach ($req->fetchAll() as $post) {

		$list[] = new Laporan("",$post['nama_produk'],"",$post['total_harga'],"",$post['tanggal'],"","","","",$post['jumlah']
			);
}*/

foreach ($req as $item) {
	$list[]=array(
		'nama_produk'=>$item['nama_produk'],
		'total_harga'=>$item['total_harga'],
		'tanggal'=>$item['tanggal'],
		'jumlahBeli'=>$item['jumlah'],
		'penjual'=>$item['nama']


		);
}


return $list;
}

public function transaksiPenjual(){
	$list=[];
	$db = DB::getInstance();

	$req = $db->query("SELECT p.nama_produk,dp.jumlah,dp.total_harga,dp.tanggal,
		(SELECT status FROM orders WHERE id_order=dp.id_order) as status,
		(SELECT u.nama FROM orders o JOIN users u ON o.id_user=u.id_user WHERE id_order=dp.id_order) as pembeli
		
		FROM detail_order dp
		JOIN produk p on dp.id_produk=p.id_produk join users u ON p.id_user=u.id_user
		WHERE p.id_user=".$_SESSION['id_user']);

/*	foreach ($req->fetchAll() as $post) {

		$list[] = new Laporan("",$post['nama_produk'],"",$post['total_harga'],"",$post['tanggal'],"","","","",$post['jumlah']
			);
}*/

foreach ($req as $item) {
	$list[]=array(
		'nama_produk'=>$item['nama_produk'],
		'total_harga'=>$item['total_harga'],
		'tanggal'=>$item['tanggal'],
		'jumlahBeli'=>$item['jumlah'],
		'status'=>$item['status'],
		'pembeli'=>$item['pembeli']


		);
}


return $list;
}

public function transaksiAdmin(){
	$list=[];
	$db = DB::getInstance();

	$req = $db->query("SELECT id_order,u.nama,tanggal,status from orders o 
		JOIN users u ON o.id_user=u.id_user");

/*	foreach ($req->fetchAll() as $post) {

		$list[] = new Laporan("",$post['nama_produk'],"",$post['total_harga'],"",$post['tanggal'],"","","","",$post['jumlah']
			);
}*/

foreach ($req as $item) {
	$list[]=array(
		'id_order'=>$item['id_order'],
		'pembeli'=>$item['nama'],
		'tanggal'=>$item['tanggal'],
		'status'=>$item['status'],


		);
}


return $list;
}

public function editStatusTransaksi($id_order){
		$db = DB::getInstance();

		$id_produk=array();
		$jumlah=array();
		

		
		$req2 = $db->query("SELECT * FROM detail_order WHERE id_order='$id_order'
			");
		foreach ($req2->fetchAll() as $post) {

			$id_produk[] = $post['id_produk'];
			$jumlah[]=$post['jumlah'];
		}

		$req3 = $db->query("SELECT status FROM orders WHERE id_order='$id_order'
			");
		foreach ($req3->fetchAll() as $post) {

			$status=$post['status'];
		}


		if (strcasecmp($status,"belum bayar")==0) {
			for ($i=0; $i <count($jumlah); $i++) { 
				$req4 = $db->query("UPDATE produk set jumlah_stok=jumlah_stok-'$jumlah[$i]' where 
					id_produk='$id_produk[$i]'
					");
			}
			
		}
		$req = $db->query("UPDATE orders set status='lunas' where id_order='$id_order'
			");




		return $req4;
	}

}





?>