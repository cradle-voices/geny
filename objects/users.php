<?php
class User
{
	private $id;
	private $name;	
	private $email;	
	private $phone;
	private $password;	
	private $status;
	private $role;
	private $table;
	private $connection;
	public  function __construct($connection)
	{
		$this->id         = "";
		$this->name       = "";
		$this->email      = "";	
		$this->phone      = "";
		$this->password   = "";
		$this->status     = "";
		$this->role       = "";
		$this->connection = $connection;
		$this->table      = "users";
	}
	public function create($name, $email, $phone, $password, $status,  $role)
	{
		//filte/sanitze user data 
		$this->name      = filter_var($name,      FILTER_UNSAFE_RAW);
		$this->phone     = filter_var($phone,     FILTER_UNSAFE_RAW);
		$this->email     = filter_var($email ,    FILTER_UNSAFE_RAW);
		$this->role      = 2;		
		$this->password  = password_hash(filter_var($password, FILTER_UNSAFE_RAW), PASSWORD_DEFAULT);
		$this->status    = 1;
		
		//sql statement 
		$sql = "INSERT INTO  `$this->table` SET 
		`$this->table`.name     = :name, 
		`$this->table`.email    = :email,
		`$this->table`.phone    = :phone, 
		`$this->table`.role     = :role,
		`$this->table`.password = :password,		
		`$this->table`.status   = :status;";
		//prepare the sql statement  
		$psql = $this->connection->prepare($sql);
		//bind parameters
		$psql->bindParam(":name",     $this->name);
		$psql->bindParam(":email",    $this->email);
		$psql->bindParam(":phone",    $this->phone);
		$psql->bindParam(":role",     $this->role);
		$psql->bindParam(":password", $this->password);
		$psql->bindParam(":status",   $this->status);		
		try 
		{
			$psql->execute();
			if(!$psql->errorinfo()[2])
			{
				return array("error"=>false, 'info'=>"account created successful ...");
			}else 
			{
				return array("error"=>true, 'info'=>"unable to create the student  ...");				
			}
		}catch(PDOException $ex)
		{
			return $ex;
		}
	}

	public function read($filter = array())
    {
        $filter = array_map('trim', $filter); // Trim whitespace from all elements in the filter array

        $sql = "SELECT * FROM `$this->table`";
        $conditions = array();
        $params = array();

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                if (!empty($value)) {
                    $conditions[] = "`$key` = :$key";
                    $params[":$key"] = filter_var($value, FILTER_UNSAFE_RAW);
                }
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= ";";
        $stmt = $this->connection->prepare($sql);

        try {
            $stmt->execute($params);
            if (!$stmt->errorinfo()[2]) {
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array('error' => false, 'data' => $data);
            } else {
                return array('error' => true, 'info' => "Error executing the query.");
            }
        } catch (PDOException $e) {
            return array('error' => true, 'info' => $e->getMessage());
        }
    }



	public function delete($id)
	{
		$this->id = filter_var($id, FILTER_UNSAFE_RAW);
		$sql = "DELETE FROM `$this->table` WHERE `$this->table`.id = :id;";
		$stmt = $this->connection->prepare($sql);
		$stmt->bindParam(":id", $this->id);
		try
		{
			$stmt->execute();
			if(!$stmt->errorinfo()[2])
			{
				return array('error'=>false, 'info'=>"delete was successful..");
			}

		}catch(PDOException $error)
		{
			return array('error'=>true, 'info'=>$error);
		}
	}
	public function update($filter = array('id'=>"",  'phone'=> "", 'email'=> "", 'role'=> "",  'status'=>""))
	{
		$this->phone = filter_var($filter['phone'], FILTER_UNSAFE_RAW);
		$this->email = filter_var($filter['email'], FILTER_UNSAFE_RAW);
		$this->status = filter_var($filter['status'], FILTER_UNSAFE_RAW);
		$this->id = filter_var($filter['id'], FILTER_UNSAFE_RAW);
		$this->role = filter_var($filter['role'], FILTER_UNSAFE_RAW);		

		//prepare the sql query
		$sql = "UPDATE   `$this->table` SET"; 		 

		//update based on the data given
		
	
		// serch using phone number .....
		if(!empty($filter['phone']))
		{
			if(stripos($sql, "AND"))
			{
				$sql .= " AND  `$this->table`.phone = :phone ";
			}else 
			{
				$sql .= " `$this->table`.phone = :phone ";
			}

		}

		 //search using email  		
		if(!empty($filter['email']))
		{
			if(stripos($sql, "AND"))
			{
				$sql .= "AND  `$this->table`.email = :email ";
			}else 
			{
				$sql .= " `$this->table`.email = :email ";
			}

		} 
		// SEARCH USING THE role
		if(!empty($filter['role']))
		{
			if(stripos($sql, "AND"))
			{
				$sql .= "AND  `$this->table`.role = :role ";
				
			}else 
			{
				$sql .= " `$this->table`.role = :role ";

			}

		} 



		
		//the user status never changes thus is  most suitable to be used as the finall serch key	
		
		// $sql .= " `$this->table`.status = :status ";s
		$sql .=  " WHERE `$this->table`.id = :id;";

		

		

		//prepare the sql statement    dfjdf
		$psql = $this->connection->prepare($sql);
		// bind the parameters 
		if (!empty($this->id)){$psql->bindParam(":id", $this->id);}
		if (!empty($this->email)){$psql->bindParam(":email", $this->email);}
		if (!empty($this->phone)){$psql->bindParam(":phone", $this->phone);}
		if (!empty($this->role)){$psql->bindParam(":role", $this->role);}
		if (!empty($this->status)){$psql->bindParam(":status", $this->status);}
		try
		{
			$psql->execute();
			if(!$psql->errorinfo()[2])
			{
				return array('error'=>0, 'info'=>"User updated succesfully");

			}else
			{
				print $psql->errorinfo()[2];
			}
		}catch(PDOException $error)
		{
			print "sorry unable to update the data" . $error->getmessage();
		}

	}
	public function login($email, $password)
	{
		//clean the daata 
		$this->email = filter_var($email, FILTER_UNSAFE_RAW);
		$this->password = filter_var($password, FILTER_UNSAFE_RAW);
		// make an sql statement 
		$sql = "SELECT  
		`$this->table`.id,
		`$this->table`.name ,
		`$this->table`.email,
		`$this->table`.phone,
		`$this->table`.role,
		`$this->table`.password,		
		`$this->table`.status FROM `$this->table` WHERE `$this->table`.email = :email;";
		//prepare the sql statemnt 
		$stmt = $this->connection->prepare($sql);
		// bind the parameter 
		$stmt->bindParam(":email", $this->email);
		// execute the sql query
		
		try
		{
			$stmt->execute();
			if(!$stmt->errorinfo()[2])
			{

				$rows = $stmt->rowCount();
				if($rows == 1)
				{
					// fetch the data  as an associative arry
					$data = $stmt->fetch(PDO::FETCH_ASSOC);
					// extract the password from the db
					$passwordFromDb = $data['password'];
					//veryfy the password 

					if(password_verify($this->password, $passwordFromDb))
					{
						return array('error'=>false, 'data'=>$data);


					}else
					{
						return array('error'=>true, 'info'=>"incorrect username of password please try again ... ");					
						
					}


				}
				else 
				{
					return array('error'=>true, 'info'=>"account not found ");					
				}
			}else 
			{
				return array('error'=>true, 'info'=>$stmt->errorinfo()[2]);
			}
		} 
		catch (PDOException $e)
		{
			return array('error'=>true, 'info'=>$e);
			
		}

	}
	public function StartSession()
	{
		// session name 
		$name = "geni";
		// ensure the session secure is false
		$secure = false;
		// ensure the session uses only the http only
		$http  = true;
		//ensure that our session uses only the cookie
		if(ini_set('session_use_only_cookie', 1) === false)
		{
			$_SESSION['sessionError'] = "session_not secure";
		}
		// get  the initial cookie parameters and store them in a variable 
		$cookie_param = session_get_cookie_params();
		//seee what these cookie parma look like 
		// print_r($cookie_param)=>Array ( [lifetime] => 0 [path] => / [domain] => [secure] => [httponly] => [samesite] => )
		// set the cookie paaramete
		session_set_cookie_params($cookie_param['lifetime'], $cookie_param['path'], $cookie_param['domain'], $secure, $http);
		// set the session name 
		session_name($name);
		// session start
		session_start();
		// regenerate the id
		session_regenerate_id();
	}
	public function stopSessionLogout()
	{

		if(!empty($_SESSION))
		{
			// [userId] => 2 [status] => 2 [userName] => colls [location] => 2056467 [loginString]
			unset($_SESSION['userId']);
			unset($_SESSION['status']);	
			unset($_SESSION['userName']);						
			unset($_SESSION['location']);
			unset($_SESSION['loginString']);
			session_destroy();						
			return array('error'=>0, 'info'=>"session stoped succesfully");
			

		}else 
		{
			return array('error'=>1, 'info'=>"trying to stop a non existence session ");
		}
	}
	public function loginCheck()
	{
		// just checks that the uer using our systen belongs to us 
		// strt by making sure the required details are all set b4 we continue
		if(isset($_SESSION['userId'], $_SESSION['location'], $_SESSION['loginString']))
		{
			// check if  the logged in user is identified by our sustem
			//make a query to extract  the pswd from the db
			$sql = "SELECT  `$this->table`.password FROM  `$this->table` WHERE `$this->table`.location = :location;";
			// prepare the sql statement 
			$stmt = $this->connection->prepare($sql);
			// bind the parameter
			$stmt->bindParam(":location", $_SESSION['location']);
			// try and execute the sql querry 
			try
			{
				$stmt->execute();
				if(!$stmt->errorinfo()[2])
				{
					$rows = $stmt->rowCount();
					if($rows == 1)
					{
						// fetch the data 
						$data = $stmt->fetch(PDO::FETCH_ASSOC);
						// EXTRACT THE pswd
						$passwordFromDb = $data['password'];
						// extract the user agent 
						$user_agent = $_SERVER['HTTP_USER_AGENT'];
						// hash the tha useragent and the password from the db
						$login_check = hash("sha512", $passwordFromDb.$user_agent);
						//check if the login string and the hashed user agent are the same
						if(hash_equals( $_SESSION['loginString'], $login_check))
						{
							return array('error'=>0, 'info'=>"user veryfied"); 
							
						}else 
						{
							return array('error'=>1, 'info'=>"unable to veryfy the user ..."); 						

						}
						

					}else //when the user does not belong to us
					{
						return array('error'=>1, 'info'=>"the use does not belong  to us"); 

					}

				}else 
				{
					return array('error'=>1, 'info'=>$stmt->errorinfo()[2]); 

				}

			}catch(PDOException $E)
			{
				return array('error'=>1, 'info'=>$E);				
			}
			
			return array('error'=>1, 'info'=>"some  one is logged inn");
		}
		else 
		{
			
			return array('error'=>1, 'info'=>"no user currently logged in");
		}
	}

}

?>
