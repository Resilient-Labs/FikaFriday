<?php
    include'config.php';

	$insertQuery = "";
    $editQuery = "";
    $removeQuery = "";
	//Test Connection
	if(mysqli_connect_errno()) {
		die("Database connection failed: " .
			mysqli_connect_error() .
			" (". mysqli_connect_errno() . ")"
		);
	}
    //addEntry Function
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];
        switch ($action){
            case 'addUser':
                //adding entry
                $newFirst = $_POST['newFirst'];
                $newLast = $_POST['newLast'];
                
                $insertQuery = "INSERT INTO {$dbname} (";
                $insertQuery .= " first, last";
                $insertQuery .= ") Values (";
        		$insertQuery .= "'{$newFirst}', '{$newLast}'";
        		$insertQuery .= ")";
                $insertResult = mysqli_query($connection, $insertQuery);

                 if (!$insertResult){
                    echo("query failed");
                 }

                break;

            case 'editUser':
                //editing entry
                $newFirst = $_POST['newFirst'];
                $newLast = $_POST['newLast'];
                $oldName = $_POST['oldName'];
                $editQuery = "UPDATE {$dbname} 
                                SET first='{$newFirst}', last='{$newLast}' 
                                WHERE last='{$oldName}'";
                $editResult = mysqli_query($connection, $editQuery);
                break;
            case 'removeUser':
                //editing entry
                $formerEmployee = $_POST['formerEmployee'];
                $removeQuery = "DELETE FROM {$dbname}  
                                WHERE last='{$formerEmployee}'";
        
                $removeResult = mysqli_query($connection, $removeQuery);
                break;
                
            default:
                echo"nah";
        }
            
    }

	$query = "SELECT * FROM team";
	$results = mysqli_query($connection, $query);
	if(!$results){
	   die("Database query messed up");
	}
?>