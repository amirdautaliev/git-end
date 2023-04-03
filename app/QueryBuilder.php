<?
namespace app;

use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder{
	private $pdo;
	private $queryFactory;
	public function __construct()
	{
		$this->queryFactory  = new QueryFactory("mysql");
		$this->pdo  = new PDO("mysql:host=localhost;dbname=graduation","mysql","mysql");
	}
	public function getAll($table){
		$select = $this->queryFactory->newSelect();
		$select->cols(["*"])
		->from($table);
		$sth = $this->pdo->prepare($select->getStatement());
		// bind the values and execute
		$sth->execute($select->getBindValues());
		// get the results back as an associative array
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function update_personal($table,$id,$username,$position,$phone,$address)
	{
		
		$update = $this->queryFactory->newUpdate();

		$update
			 ->table($table)                  // update this table
			 ->cols([
				'username',
				'position',
				'phone_number',
				'address'
				])
			         // raw value as "(ts) VALUES (NOW())"
  // bind one value to a placeholder
  ->where('id = :id')
  ->bindValue('id', $id)
  ->bindValues([           
	'username'=>$username,       // bind these values to the query
	'position'=>$position,
	'phone_number'=>$phone,
	'address'=>$address

]);

$sth = $this->pdo->prepare($update->getStatement());
// d($sth);die;
// execute with bound values
$sth->execute($update->getBindValues());
}



public function update_status($table,$id,$status)
{
$update = $this->queryFactory->newUpdate();

	$update
		 ->table($table)                  // update this table
		 ->cols([
			'status'
			])
					// raw value as "(ts) VALUES (NOW())"
// bind one value to a placeholder
->where('id = :id')
->bindValue('id', $id)
->bindValues([                  // bind these values to the query
'status'=>$status
]);

$sth = $this->pdo->prepare($update->getStatement());
// d($sth);die;
// execute with bound values
$sth->execute($update->getBindValues());
}
public function social_update($table,$id,$vk,$telegram,$instagram)
{
	$update = $this->queryFactory->newUpdate();

	$update
		 ->table($table)                  // update this table
		 ->cols([
			'vk',
			'telegram',
			'instagram'
			])
					// raw value as "(ts) VALUES (NOW())"
// bind one value to a placeholder
->where('id = :id')
->bindValue('id', $id)
->bindValues([                  // bind these values to the query
"vk"=>$vk,
"telegram"=>$telegram,
"instagram"=>$instagram
]);

$sth = $this->pdo->prepare($update->getStatement());
// d($sth);die;
// execute with bound values
$sth->execute($update->getBindValues());
}
public function avatar_upload($table,$images,$id)
{
$result = pathinfo($images['name']);
$filename = uniqid() . '.' . $result['extension'];
$update = $this->queryFactory->newUpdate();

	$update
		 ->table($table)                  // update this table
		 ->cols([
			'image'
			])
					// raw value as "(ts) VALUES (NOW())"
// bind one value to a placeholder
->where('id = :id')
->bindValue('id', $id)
->bindValues([                  // bind these values to the query
	'image'=>'avatar/'.$filename
]);

$sth = $this->pdo->prepare($update->getStatement());
// d($sth);die;
// execute with bound values
$sth->execute($update->getBindValues());
move_uploaded_file($_FILES['images']["tmp_name"], "avatar/".$filename);
}

public function getOne($table,$title,$id)
{
	$select = $this->queryFactory->newSelect();
	$select->cols($title)
	->from($table)
	->where('id = :id')
	->bindValue('id', $id);
$sth = $this->pdo->prepare($select->getStatement());

// bind the values and execute
$sth->execute($select->getBindValues());

// get the results back as an associative array
$result = $sth->fetch(PDO::FETCH_ASSOC);
return $result;

}
public function getemail($table,$title,$email)
{
	$select = $this->queryFactory->newSelect();
	$select->cols($title)
	->from($table)
	->where('email = :email')
	->bindValue('email', $email);
$sth = $this->pdo->prepare($select->getStatement());

// bind the values and execute
$sth->execute($select->getBindValues());

// get the results back as an associative array
$result = $sth->fetch(PDO::FETCH_ASSOC);
return $result;
}

// prepare the statement
public function update_email($table,$email,$id)
{
		
	$update = $this->queryFactory->newUpdate();

	$update
		 ->table($table)                  // update this table
		 ->cols([
				'email'
			])
					// raw value as "(ts) VALUES (NOW())"
// bind one value to a placeholder
->where('id = :id')
->bindValue('id', $id)
->bindValues([           
'email'=>$email]);
$sth = $this->pdo->prepare($update->getStatement());

// execute with bound values
$sth->execute($update->getBindValues());


}
public function delete_user($table,$id)
{
	$delete = $this->queryFactory->newDelete();

	$delete
		 ->from($table)                   // FROM this table
		 ->where('id = :id')           // AND WHERE these conditions       
		 ->bindValue('id', $id);   // bind one value to a placeholder

		 $sth = $this->pdo->prepare($delete->getStatement());

		 // execute with bound values
		 $sth->execute($delete->getBindValues());
}
}





?>