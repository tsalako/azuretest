<?php
$options = array('cost' => 12);
echo "Bcrypt: ";
echo $hash = password_hash("password", PASSWORD_BCRYPT, $options);
echo "<br>";
echo "Verify now:<br>";
if (password_verify('password', $hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}
?>