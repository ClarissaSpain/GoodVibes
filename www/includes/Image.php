<?php
//make an image posting class
class Image{
    public static function uploadImage(){
    
            $file = $_FILES['profileimg'];
            $fileName = $_FILES['profileimg']['name'];
            $fileTmpName = $_FILES['profileimg']['tmp_name'];
            $fileSize = $_FILES['profileimg']['size'];
            $fileError = $_FILES['profileimg']['error'];
            $fileType = $_FILES['profileimg']['type'];
                
            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));
        
            $allowed = array('jpg', 'jpeg', 'png');
        
            if (in_array($fileActualExt, $allowed)) {
                if ($fileError === 0) {
                    // I increased the size so that an image could get uploaded that was bigger than 50K
                    // See I don't know how to do math
                    if ($fileSize < 500000) {
                        $fileNameNew = uniqid('', true).".".$fileActualExt;
                        $fileDestination = './uploads/'.$fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        // header("Location: my_account.php?uploadsuccess");
                        
                        //inserts the file path of my image from my directory into the database.
                        $InsertImage = DB::query('UPDATE goodvibes.posts SET postimg=:postimg WHERE idposts-:idposts', array(':postimg'=>$postimg,':idposts'=>$postid));
        }
                    }else{
                        echo "Your file is too big!";
                    }
                }else{
                    echo "There was an error uploading your file!";
                }

    
        // DB::query($query, $params);
    }
} 

?>