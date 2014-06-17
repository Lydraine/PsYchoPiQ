<?php
/************************************************************************
 ************************************************************************
 *Program Name : PsYchoPiQ                                              *
 ************************************************************************
 *Organisation : Lydraine                                               *
 ************************************************************************
 *Version      : Cookie Cocaine <1.0.0>                                 *
 ************************************************************************
 *Contibutors  : Tobalase Akinyemi <stagnova@gmail.com>                 *
 ************************************************************************
 *Summary      : A guessing game that picks a random number between 0   *
 *               and 101 and asks you to guess the number inputing      *
 *               guides to making the right guess with colors to signify*
 *               different alerts.                                      *
 ************************************************************************
 *License      : GNU GPLv3 (view below)                                 *
 ************************************************************************
 ************************************************************************/
?>


<?php

// Set cookies to record random number and number of guesses at the
// detection of no ongoing game play which is identified by presence of
// cookie.
if (!isset($_COOKIE['number'])) {
    Setcookie('number', rand(1, 100), time()+(60*60*24));
    setcookie('tries', 1, time()+(60*60*24));
}
?>
<?php
// Initially set page background color to purple
$color = "purple";

// Check for invalid input and set error message
if (isset($_POST['guess'])) {

// Check there is an empty input.
if($_POST['guess'] == "") {$errmsg ="Please Enter Your Guess.";}

// Check if guess is a number
elseif(!is_numeric($_POST['guess'])) {$errmsg = "Your guess should be a number!!!"; }

// Check if guess is within the range of 1-100
elseif(($_POST['guess'] < 1) OR ($_POST['guess'] > 100)) {$errmsg = "Your Guess should not be less than 1 or more than 100." ;} 

// Check if there are errors.
// Unset the guess too if there are errors.
// Changing the background color if there are.
if(isset($errmsg)) {$_POST['guess'] = NULL;$color = "orange";}
else{$color = "purple";}
}

// Check if there are no errors by checking for presence of guess variable
// and absence of error message.
if ((isset($_POST['submit']) AND isset($_POST['guess'])) AND !isset($errmsg)) {

// Set number by getting number variable from cookie.
    $number = $_COOKIE['number'];
// Set guess from form input.
    $guess = $_POST['guess'];
// Set background color to green if guess is correct.
    if($number == $guess) {$color = "green";}
// Guess can only be wrong if it isn't right.
// So set background color to maroon.
    else{$color = "maroon";}

// Add one to number of guesses.
    setcookie('tries', $_COOKIE['tries']+1, time()+(60*60));
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>PsYchoPiQ</title>
<style>
<!-- 
body{background-color: <?php echo $color; /*echo set background color*/?>;
    text-align: center;
    font-family: Monaco;
}
-->
</style>
</head>
<body>
<form action = "./index.php" method = "POST">
<h1>
<?php
// Print greeting message if background color is purple.
if($color == "purple") {
echo "<h3>I have chosen a number </em>randomly</em> from 1 to 100</h3>";
echo "<h2>What Do You Think It Is?</h2>";
}
// Print error message if there is any.
elseif(isset($errmsg)) {echo "Invalid Input". "<br/>" . $errmsg ; }

// Print "RiGHt" if guess is correct.
elseif($number == $guess) {echo "RiGHT";}

// Print "wRUNG" if guess is wrong.
else{echo "wRUNG";}
?>
</h1>
<br/>

<?php 

// Display input form if guess is incorrect. 
if ($color != "green") {
echo "<input name = \"guess\" type = \"text\"><br/>";
echo "<input name = \"submit\" type =\"submit\" value = \"Enter\">";
}

// Display Replay button if guess is correct.
else{
echo "<h3>Can You Guess My Number Faster?</h3>";
echo "<input name = \"replay\" type = \"submit\" value = \"RePlay!!!\">";}
?>
<h2>
<?php
// Unset number cookie when guess is correct.
// Print number of guesses and unset the related cookie when guess is correct.  
if(($number == $guess) AND ($color == "green")) {
echo "It took you " . $_COOKIE['tries'] . " guesses.";
setcookie('tries', NULL, time()+(60*60*24));
setcookie('number', NULL, time()-(60*60*24));
}

// If guess is incorrect, print guide.
elseif($number > $guess) {echo "Go Higher";}
elseif($number < $guess) {echo "Go Lower";}
?>
</h2>
</form>

</body>
</html>	
