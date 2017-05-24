<?php

class DB_Functions {
 
    private $conn;
 
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * add new user
     * returns user details
     */
    public function storeUser($name, $email, $password, $address, $city, $zip, $country, $latitude, $longitude) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
 
        $stmt = $this->conn->prepare("INSERT INTO users(UserName, Email, Password, Salt, Address, City, Zip, Country, Latitude, Longitude, JoinedDate, Status) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)");
        $stmt->bind_param("ssssssssss", $name, $email, $encrypted_password, $salt, $address, $city, $zip, $country, $latitude, $longitude);
        $result = $stmt->execute();
        $stmt->close();
 
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            return $user;
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
 
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE Email = ?");
 
        $stmt->bind_param("s", $email);
 
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            // verifying user password
            $salt = $user['Salt'];
            $encrypted_password = $user['Password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }
 
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT Email from users WHERE Email = ?");
 
        $stmt->bind_param("s", $email);
 
        $stmt->execute();
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    } 
	
	/**
     * Get products by search
     */
    public function getProducts($Search) {
		/////////////////////////////////////////////////////////////////////////////////////
		$Search = '%'.$Search.'%';
        $query = "select p.*, min(i.id), i.Image FROM `product` p left join `product_images` i on p.ProductID = i.ProductID where p.ProductName like '".$Search."' GROUP BY p.ProductID";	
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}

    /**
     * Get products by search
     */
    public function getProductsWithoutImages($Search) {
		/////////////////////////////////////////////////////////////////////////////////////
		$Search = '%'.$Search.'%';
        $query = "select p.* FROM `product` p where p.ProductName like '".$Search."' GROUP BY p.ProductID";	
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}
	
	/**
     * add new product
     * returns product details
     */
    public function addProduct($name, $desc, $price, $category, $state, $zipCode, $latitude, $longitude, $image1, $image2, $image3, $image4, $image5, $uid) {		
		$prodId = 0;
		
        $stmt = $this->conn->prepare("INSERT INTO product(ProductName, PostedDate, Description, SoldBy, Price, Status, Category, ProductState, ZipCode, Latitude, Longitude) VALUES(?, NOW(), ?, ?, ?, 1, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddsssss", $name, $desc, $uid, $price, $category, $state, $zipCode, $latitude, $longitude);
        $result = $stmt->execute();
        $stmt->close();
		
		//get the prod ID
		$stmt = $this->conn->prepare("SELECT * FROM product WHERE ProductID=(SELECT max(ProductID) FROM product)");
        if ($stmt->execute()){
            $condi = $stmt->get_result()->fetch_assoc();
			$prodId = $condi["ProductID"];
            $stmt->close();
		}
		
		if($image1 != null){
			$stmt = $this->conn->prepare("INSERT INTO product_images (ProductID, Image) VALUES(?, ?)");
			$stmt->bind_param("ds", $prodId, $image1);
			$result = $stmt->execute();
			$stmt->close();
		}
		
		if($image2 != null){
			$stmt = $this->conn->prepare("INSERT INTO product_images (ProductID, Image) VALUES(?, ?)");
			$stmt->bind_param("ds", $prodId, $image2);
			$result = $stmt->execute();
			$stmt->close();
		}
		
		if($image3 != null){
			$stmt = $this->conn->prepare("INSERT INTO product_images (ProductID, Image) VALUES(?, ?)");
			$stmt->bind_param("ds", $prodId, $image3);
			$result = $stmt->execute();
			$stmt->close();
		}
		
		if($image4 != null){
			$stmt = $this->conn->prepare("INSERT INTO product_images (ProductID, Image) VALUES(?, ?)");
			$stmt->bind_param("ds", $prodId, $image4);
			$result = $stmt->execute();
			$stmt->close();
		}
		
		if($image5 != null){
			$stmt = $this->conn->prepare("INSERT INTO product_images(ProductID, Image) VALUES(?, ?)");
			$stmt->bind_param("ds", $prodId, $image5);
			$result = $stmt->execute();
			$stmt->close();
		}
 
        // check for successful product
        if ($result) { 
            return true;
        } else {
            return false;
        }
    }
	/**
     * Get products by id
     */
    public function getProductByID($ProductID, $UserID) {
 
 
        $query = "SELECT 
            p.ProductID,
            p.ProductName,
            p.PostedDate,
            p.Description,
            p.ProductState,
            p.SoldBy,
            p.Price,
            p.Status,
            p.Category,
            p.ZipCode,
            p.Latitude,
            p.Longitude,
            u.UserName,
            u.JoinedDate,
            u.Email,
            w.id
		 FROM product p
         left join users u on p.SoldBy = u.UserID
         left join product_watching w on (p.ProductID = w.ProductID and w.UserID = ".$UserID.")
		 where p.ProductID = ".$ProductID."";

		$result = $this->conn->query($query);
 
		$row = $result->fetch_assoc();
		
		return json_encode($row);
    }
	
	/**
     * Get images by search
     */
    public function getImageByID($ProductID) {
		$query = "SELECT * FROM product_images where ProductID = ".$ProductID."";
 
		$result = $this->conn->query($query);
 
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		
		return json_encode($rows);
	}

    /**
     * Get selling products
     */
    public function getSellingProducts($UserID) {
		/////////////////////////////////////////////////////////////////////////////////////
        $query = "select p.*, min(i.id), i.Image FROM `product` p left join `product_images` i on p.ProductID = i.ProductID where p.SoldBy = ".$UserID." GROUP BY p.ProductID";	
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}

    /**
     * Get sellin gproducts
     */
    public function getSellingProductsWithoutImages($UserID) {
		/////////////////////////////////////////////////////////////////////////////////////
        $query = "select p.* FROM `product` p where p.SoldBy = ".$UserID."";	
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}

    /**
     * Get watching products
     */
    public function getWatchingProducts($UserID) {
		/////////////////////////////////////////////////////////////////////////////////////
        $query = "select p.*, min(i.id), i.Image FROM `product` p left join `product_images` i on p.ProductID = i.ProductID where p.ProductID in (select ProductID from product_watching where UserID = ".$UserID.") GROUP BY p.ProductID";	
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}

    /**
     * Get watching gproducts
     */
    public function getWatchingProductsWithoutImages($UserID) {
		/////////////////////////////////////////////////////////////////////////////////////
        $query = "select p.* FROM `product` p where p.ProductID in (select ProductID from product_watching where UserID = ".$UserID.")";		
		$result = $this->conn->query($query);
		$rows = [];
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return json_encode($rows);
	}

    /**
     * Get user by email and password
     */
    public function addWatching($UserID, $ProductID) {
		try {
			$ID = 0;
			$stmt = $this->conn->prepare("SELECT * from product_watching WHERE UserID = ? and ProductID = ? ");
			$stmt->bind_param("dd", $UserID, $ProductID);
			if ($stmt->execute()) {
				$watching = $stmt->get_result()->fetch_assoc();
				if($watching != null){
					$stmt->close();
					$ID = $watching['ID'];
				}
			} 
			if($ID == 0) {
				// user not existed
				$stmt->close();
				$stmt = $this->conn->prepare("INSERT INTO product_watching (UserID, ProductID) VALUES (?, ?)");
				$stmt->bind_param("dd", $UserID, $ProductID);
				$result = $stmt->execute();
				$stmt->close();
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		return true;
    }

    public function removeWatching($UserID, $ProductID) {
		try {
			$stmt = $this->conn->prepare("delete from product_watching WHERE UserID = ? and ProductID = ? ");
			$stmt->bind_param("dd", $UserID, $ProductID);
            $result = $stmt->execute();
			$stmt->close();
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		return true;
    }

    public function deleteProduct($ProductID) {
		try {
			$stmt = $this->conn->prepare("delete from product_watching WHERE ProductID = ? ");
			$stmt->bind_param("d", $ProductID);
            $result = $stmt->execute();
			$stmt->close();

            $stmt = $this->conn->prepare("delete from product_images WHERE ProductID = ? ");
			$stmt->bind_param("d", $ProductID);
            $result = $stmt->execute();
			$stmt->close();

            $stmt = $this->conn->prepare("delete from product WHERE ProductID = ? ");
			$stmt->bind_param("d", $ProductID);
            $result = $stmt->execute();
			$stmt->close();
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		return true;
    }
}
 
?>