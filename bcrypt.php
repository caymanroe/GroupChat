<?php
/*This is used for testing purposes only*/
$options = array('cost' => 13);
echo "Bcrypt: ";
echo $hash = password_hash("pass", PASSWORD_BCRYPT, $options);
echo "<br>";
echo "Verify now:<br>";
if (password_verify('pass', $hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}


echo " Maximum allowed file size is : ".ini_get('upload_max_filesize');

?>