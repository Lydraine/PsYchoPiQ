<?php
/************************************************************************
 ************************************************************************
 *Program Name : PsYchoPiQ                                              *
 ************************************************************************
 *Organisation : Lydraine                                               *
 ************************************************************************
 *Version      : Secret Session <1.2.0>                                 *
 ************************************************************************
 *Contributors : Tobalase Akinyemi <stagnova@gmail.com>                 *
 ************************************************************************
 *Summary      : A guessing game that picks a random number between 0   *
 *               and 101 and asks you to guess the number inputing      *
 *               guides to making the right guess with colors to signify*
 *               different alerts.                                      *
 ************************************************************************
 *Improvements :Long term variables now stored in sessions.             *
 *              Email request before game starts.                       *
 ************************************************************************
 *License      : GNU GPLv3 (view below)                                 *
 ************************************************************************
 ************************************************************************/
?>


<?php
// ***PRE-DISPLAY***

//include('functions.php');
session_start();

if(isset($_POST['email'])) {
    // Check for empty email field before starting game play.
    if($_POST['email'] != "") {
        $_SESSION['number'] = rand(1, 100);
        $_SESSION['tries'] = 0;
        $_SESSION['email'] = $_POST['email'];
    }    
}

if(isset($_POST['replay'])) {
    $_SESSION['number'] = rand(1, 100);
    $_SESSION['tries'] = 0;
}

elseif(isset($_POST['exit'])) {
    $_SESSION = array();
    setcookie(session_name(), NULL, time()-(60*60*24*7*52*2000));
}
?>

<?php

$color = "purple";


if (isset($_POST['guess'])) {


if($_POST['guess'] == "") {$errmsg ="Please Enter Your Guess.";}


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
    $number = $_SESSION['number'];
// Set guess from form input.
    $guess = $_POST['guess'];
// Set background color to green if guess is correct.
    if($number == $guess) {$color = "green";}
// Guess can only be wrong if it isn't right.
// So set background color to maroon.
    else{$color = "maroon";}

// Add one to number of guesses.
    $_SESSION['tries']++;
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
// ***HEAD***
// Print greeting message if background color is purple.
    if(isset($_SESSION['email']) AND $color == "purple") {
    echo "<h3>I have chosen a number </em>randomly</em> from 1 to 100</h3>";
    echo "<h2>What Do You Think It Is?</h2>";
}
// Print error message if there is any.
elseif(isset($errmsg)) {echo "Invalid Input". "<br/>" . $errmsg ; }

elseif(isset($_SESSION['email'])) {

// Print "RiGHt" if guess is correct.
if($number == $guess) {echo "RiGHT";}

// Print "wRUNG" if guess is wrong.
else{echo "wRUNG";}
}
?>
</h1>
<br/>

<?php 
// ***INPUT FIELDS***
// Display input form if guess is incorrect. 
if (isset($_SESSION['email']) AND $color != "green") {
    echo "<input name = \"guess\" type = \"text\"><br/>";
    echo "<input name = \"submit\" type =\"submit\" value = \"Enter\">";
}

// Display Replay button if guess is correct.
elseif ($color == "green"){
    echo "<h3>Wanna Play Again?</h3>" . "<br/>";
    echo "<input name = \"replay\" type = \"submit\" value = \"yes\">";
    echo "<input name = \"exit\" type = \"submit\" value = \"no\">";
}

// Request email if it hasn't been entered.
else{
    echo "<h2>Please Enter Your Email Address</h2>";
    echo "<input name = \"email\" type = \"text\">" . "<br/>";
    echo "<input name = \"start\" type = \"submit\" value = \"Start!!!\">";
}
?>
<h2>
<?php
// ***SUB-MESSAGES***
// Unset number session variable when guess is correct.
// Print number of guesses and unset the related session variable when guess is correct.  
if(($number == $guess) AND ($color == "green")) {
echo "It took you " . $_SESSION['tries'] . " guesses.";
unset($_SESSION['tries']);
unset($_SESSION['number']);
}

// If guess is incorrect, print guide.
elseif($number > $guess) {echo "Go Higher";}
elseif($number < $guess) {echo "Go Lower";}
?>
</h2>
</form>

</body>
</html>	
