<?
namespace App\controllers;
if( !session_id() ) {
	session_start();
}

use app\QueryBuilder;
use PDO;
use Delight\Auth\Auth;
use League\Plates\Engine;

use Tamtamchik\SimpleFlash\Flash;
use Tamtamchik\SimpleFlash\Templates;

use function Tamtamchik\SimpleFlash\flash;

class HomeController
{
  private $templates;
	public $auth;
	private $qb;
	private $engine;
	public function __construct(QueryBuilder $qb, Engine $engine,Auth $auth)
	{
		$this->qb = $qb;
		$this->templates =$engine;
		$this->auth = $auth;
		
	}
	public function views_register(){

		echo $this->templates->render('page_register');
		

	}
	public function page_register()
	{
		try {
			$userId = $this->auth->register($_POST['email'], $_POST['password'] 
				//  echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
				//  echo '  For emails, consider using the mail(...) function, Symfony Mailer, Swiftmailer, PHPMailer, etc.';
				//  echo '  For SMS, consider using a third-party service and a compatible SDK';
			);
	  
			// echo 'We have signed up a new user with the ID ' . $userId;
	  }
	  catch (\Delight\Auth\InvalidEmailException $e) {
			die('Invalid email address');
	  }
	  catch (\Delight\Auth\InvalidPasswordException $e) {
			die('Invalid password');
	  }
	  catch (\Delight\Auth\UserAlreadyExistsException $e) {

			flash()->error("User exists");
			
			echo $this->templates->render('page_register');
	  }
	  
	  catch (\Delight\Auth\TooManyRequestsException $e) {
			die('Too many requests');
	  }
	  	HomeController::redirect_to("page_login");
     
	}

	public function views_login()
	{
		echo $this->templates->render('page_login');
	}

	public function page_login()
	{
		try {
			$user=$this->auth->login($_POST['email'], $_POST['password']);
	  
			// echo 'User is logged in';
	  }
	  catch (\Delight\Auth\InvalidEmailException $e) {
			die('Wrong email address');
	  }
	  catch (\Delight\Auth\InvalidPasswordException $e) {
			die('Wrong password');
	  }
	  catch (\Delight\Auth\EmailNotVerifiedException $e) {
			die('Email not verified');
	  }
	  catch (\Delight\Auth\TooManyRequestsException $e) {
			die('Too many requests');
	  }
	  HomeController::redirect_to("users");
	}

	public  function logout(){
		$this->auth->logout();
		HomeController::redirect_to("page_login");

	}
	public function views_users()
	{	
		
		if(!$this->auth->isLoggedIn()){
			HomeController::redirect_to("page_login");
		}
		
		
		$users=$this->qb->getAll("users");
		d($_SESSION);
		echo $this->templates->render('users',["users"=>$users]);

	}

	public function views_create()
	{
		if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
			HomeController::redirect_to("page_login");
	  }
	 $datas = 
	  [
		"1"=>"Online",
		"2"=>"Offline"

	  ];
	 
	  echo $this->templates->render("create_user",["data"=>$datas]);
	}

	public function create_user()
	{
		
		try {
			$userID = $this->auth->admin()->createUser($_POST['email'],$_POST['password']);
			
			 $$this->qb->update_personal("users",$userID,$_POST['position'],$_POST['username'],$_POST['phone_number'],$_POST['address']);
			 $db->update_status('users',$userID,$_POST['status']);
			 $db->social_update('users',$userID,$_POST['vk'],$_POST['telegram'],$_POST['instagram']);
			 $db->avatar_upload('users',$_FILES['images'],$userID);
			HomeController::redirect_to("users");
	  }
	  catch (\Delight\Auth\InvalidEmailException $e) {
			die('Invalid email address');
	  }
	  catch (\Delight\Auth\InvalidPasswordException $e) {
			die('Invalid password');
	  }
	  catch (\Delight\Auth\UserAlreadyExistsException $e) {
			die('User already exists');
	  }
	 
	}
	public function views_edit($id)
	{
	
		$edit_user_id= (int)$id;
		$current_user_id = $this->auth->getUserId();

	
		$updates= $this->qb->getOne('users',[
			'id',
			'username',
			'position',
			'phone_number',
			'address'
		],$id);
		if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id !==$current_user_id)
		{
			HomeController::redirect_to("\users");
		}
		echo $this->templates->render('edit',['values'=>$updates]);
	}
	public function update_information($id)
	{	
	
		$id = $_POST['id'];
		// d($edit_user_id);
		// d($current_user_id);

		$this->qb->update_personal("users",$id,$_POST['username'],$_POST['position'],$_POST['phone'],$_POST['address']);

		HomeController::redirect_to("/edit/".$id);

	}
	public function views_security($id)
	{

		$edit_user_id= (int)$id;
		$current_user_id = $this->auth->getUserId();

		
		$updates=$this->qb->getOne('users',
		[
			'id',
			'email'
		],$id);
		if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id !==$current_user_id)
		{
			HomeController::redirect_to("\users");
		}
		echo $this->templates->render('security',['values'=>$updates]);
	}

	public function update_security()
	{
		
		$user=$$this->qb->getemail('users',
		[
			'id',
			'email'
		],$_POST['email']);

		if(!empty($user) AND $_POST['id']!==$user['id'])
		{
			HomeController::redirect_to("/security/".$_POST['id']);
			$_SESSION['secruity_danger']  = "Пользователь существует в базе";
			exit;
 		}
		$this->qb->update_email("users",$_POST['email'],$_POST['id']);
	try {
			$this->auth->changePassword($_POST['old_password'], $_POST['new_password']);
			HomeController::redirect_to("/security/".$_POST['id']);
			$_SESSION['secruity_sucsess'] = "Успешно обновлен!";
	  }
	  catch (\Delight\Auth\NotLoggedInException $e) {
			die('Not logged in');
	  }
	  catch (\Delight\Auth\InvalidPasswordException $e) {
			die('Invalid password(s)');
	  }
	  catch (\Delight\Auth\TooManyRequestsException $e) {
			die('Too many requests');
	  }

	}

	public function views_status($id)
	{
		$edit_user_id= (int)$id;
		$current_user_id = $this->auth->getUserId();
	
		$status=$this->qb->getOne('users',['id','status'],$id);

		if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id !==$current_user_id)
		{
			HomeController::redirect_to("\users");
		}
		
		echo $this->templates->render('status',['status'=>$status]);
	}
	public function set_status()
	{


		$this->qb->update_status('users',$_POST['id'],$_POST['status']);
		HomeController::redirect_to("/status/".$_POST['id']);
		$_SESSION['status_update']='Статус обновлен!';

	}
	public function views_media($id)
	{
		$edit_user_id= (int)$id;
		$current_user_id = $this->auth->getUserId();
	
			$image=$this->qb->getOne('users',['id','image'],$id);
			
			if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id !==$current_user_id)
			{
				HomeController::redirect_to("\users");
			}
		echo $this->templates->render('media',['image'=>$image]);
	}
	public function avatar_upload()
	{
		$id=$_POST['id'];


		if(!empty($_FILES['images'])){
			$this->qb->avatar_upload('users',$_FILES['images'],$id);
		}
		HomeController::redirect_to("/media/".$id);
		$_SESSION['upload_image']= 'Картинка успешно загружен';
		
	
		
	}
	public function delete_user($id)
	{
	
		
		$edit_user_id= (int)$id;
		$current_user_id = $this->auth->getUserId();
		// d($current_user_id);
	
		$image=$this->qb->getOne('users',['image','id'],$id);
		
		if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id!==$current_user_id)
		{
			HomeController::redirect_to("/users");
			$_SESSION['no_author'] = "У вас нет прав доступа на удаление пользователя!";
		}
		elseif($this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id==$current_user_id)
		{
				
			if(!empty($image['image'])){
				unlink($image['image']);
				exit;
			}
			d($this->qb->delete_user('users',$image['id']));die;
				$this->qb->delete_user('users',$image['id']);
				$this->auth->logout();
				HomeController::redirect_to("/page_login");
	
		
		}
		elseif(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id==$current_user_id ){
			if(!empty($image['image'])){
				unlink($image['image']);
				exit;
			}
				$this->qb->delete_user('users',$image['id']);
				$this->auth->logout();
				HomeController::redirect_to("/page_login");

		}
		elseif($this->auth->hasRole(\Delight\Auth\Role::ADMIN) AND $edit_user_id!==$current_user_id ){
			if(!empty($image['image'])){
				unlink($image['image']);
				exit;
			}
				$this->qb->delete_user('users',$image['id']);

			HomeController::redirect_to('/users');
			$_SESSION['author_admin'] = "Вы успешно удалили пользователя!";

		}


	}
	public static function redirect_to($url)
	{
	return header("location:" . $url);
	}



		
}

?>