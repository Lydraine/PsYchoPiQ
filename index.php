<?php
/************************************************************************
 ************************************************************************
 *Program Name : PsYchoPiQ                                              *
 ************************************************************************
 *Organisation : Lydraine                                               *
 ************************************************************************
 *Version      : Data Doctor <1.4.0>                                    *
 ************************************************************************
 *Contributors : Tobalase Akinyemi <stagnova@gmail.com>                 *
 *               <add your name and email here on improvement>          *
 ************************************************************************
 *Summary      : A guessing game that picks a random number between 0   *
 *               and 101 and asks you to guess the number inputing      *
 *               guides to making the right guess with colors to signify*
 *               different alerts.                                      *
 ************************************************************************
 *Improvements : Quit button added during game play.                    *
 *               Email and name request before game starts.             *
 *               Collected data stored in Databases.                    *
 ************************************************************************
 *License      : GNU GPLv3 (view below)                                 *
 ************************************************************************
 ************************************************************************/
?>


<?php
// ***PRE-DISPLAY***

include_once('config.php');
session_start();
if(isset($_POST['welcome'])) {$_SESSION['welcome'] = true;}else {$color = "purple";}
// Check if email has been entered just previously.
if(isset($_POST['email'])) {
  // Check for empty email field before initializing variables.
  if(($_POST['email'] != "" AND $_POST['name'] != "")) {
    $book_open = mysql_connect("{$SERVER_NAME}", "{$SQL_USERNAME}", "$SQL_PASSWORD");
    $book_chapter = mysql_select_db("{$DATABASE_NAME}", $book_open);
    $book_note = mysql_query(
			     "INSERT INTO {$DATABASE_TABLE} (name, email) VALUES('{$_POST['name']}', '{$_POST['email']}')",
			     $book_open);
    $book_close = mysql_close($book_open);
    // Initialize variables for number, number of guesses
    // and email address respectively.
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['number'] = rand(1, 100);
    $_SESSION['tries'] = 0;
    $_SESSION['email'] = $_POST['email'];

    // Update number of game attempts in database.
    $book_open = mysql_connect("{$SERVER_NAME}", "{$SQL_USERNAME}", "$SQL_PASSWORD");
    $book_chapter = mysql_select_db("{$DATABASE_NAME}", $book_open);
    $skim = mysql_query(
			"SELECT attempts FROM {$DATABASE_TABLE} WHERE email = '{$_SESSION['email']}';",
			$book_open);
    while($scan = mysql_fetch_array($skim)) {
      $atempts = $scan[0];
      $attempts++;
    }
    $book_note = mysql_query(
			     "UPDATE {$DATABASE_TABLE} SET attempts = {$attempts} WHERE email = '{$_SESSION['email']}'",
			     $book_open);
    $book_close = mysql_close($book_open);

  }    
}

// Check for affirmative replay.
if(isset($_POST['replay'])) {

  // Update number of game attempts in database.
  $book_open = mysql_connect("{$SERVER_NAME}", "{$SQL_USERNAME}", "$SQL_PASSWORD");
  $book_chapter = mysql_select_db("{$DATABASE_NAME}", $book_open);
  $skim = mysql_query(
		      "SELECT attempts FROM {$DATABASE_TABLE} WHERE email = '{$_SESSION['email']}';",
		      $book_open);
  while($scan = mysql_fetch_array($skim)) {
    $atempts = $scan[0];
    $attempts++;
  }
  $book_note = mysql_query(
			   "UPDATE {$DATABASE_TABLE} SET attempts = {$attempts} WHERE email = '{$_SESSION['email']}'",
			   $book_open);
  $book_close = mysql_close($book_open);

  // Re-initialize variables for number and number of guesses.
  $_SESSION['number'] = rand(1, 100);
  $_SESSION['tries'] = 0;
}

// Otherwise in the case of game exit.
elseif(isset($_POST['exit']) OR isset($_POST['quit'])) {
  //End User Session.
  $_SESSION = array();
  setcookie(session_name(), NULL, time()-(60*60*24*7*52*2000));
  $color = "purple";
}
?>

<?php
// Set initial page background color to purple.
$color = "purple";

// Check if guess has been submitted and
// set for appropriate error messages for invalid guesses.
if (isset($_POST['guess'])) {

  // Check if guess is empty.
  if($_POST['guess'] == "") {$errmsg ="Please Enter Your Guess.";}

  // Check if guess is numeric.
  elseif(!is_numeric($_POST['guess'])) {$errmsg = "Your guess should be a number!!!";}

  // Check if guess is within the range of 1-100
  elseif(($_POST['guess'] < 1) OR ($_POST['guess'] > 100)) {$errmsg = "Your Guess should not be less than 1 or more than 100." ;} 


  if(!isset($_SESSION['welcome'])) {unset($errmsg);} 

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

<img src = "img/logo1/<?php echo $color;?>.png" height = "100">
<form action = "./index.php" method = "POST">
  <h2>
  <?php
  // ***HEAD***
  if(isset($_SESSION['welcome'])) {
    // Print greeting message if background color is purple and
    // email has been supplied.
    if((isset($_SESSION['email']) AND isset($_SESSION['name'])) AND $color == "purple") {
      echo "<h3>I have chosen a number </em>randomly</em> from 1 to 100</h3>";
      echo "<h2>What Do You Think It Is?</h2>";
    }
    // Print error message if there is any.
    elseif(isset($errmsg)) {echo "Invalid Input". "<br/>" . $errmsg ; }

    // Check if email has been set.
    elseif((isset($_SESSION['email']) AND isset($_SESSION['name']))) {

      // Print "RiGHt" if guess is correct.
      if($number == $guess) {echo "RiGHT";}

      // Print "wRUNG" if guess is wrong.
      else{echo "wRUNG";}
    }
  }else{$color = "purple";}
?>
</h2>


<?php 
  // ***INPUT FIELDS***

if(isset($_SESSION['welcome'])) {
  // Display input form if guess is incorrect after
  // email has been supplied and not before. 
  if ((isset($_SESSION['email']) AND isset($_SESSION['name'])) AND $color != "green") {
    echo "<input name = \"guess\" type = \"text\"><br/>";
    echo "<input name = \"submit\" type =\"submit\" value = \"Enter\">";
    if($color != "purple" AND $color != "orange") {
      echo "&nbsp&nbsp&nbsp" . "<input name = \"exit\" type =\"submit\" value = \"Quit\">";}
  }

  // Display Replay question if guess is correct.
  elseif ($color == "green"){
    echo "<h3>Wanna Play Again?</h3>" . "<br/>";
    echo "<input name = \"replay\" type = \"submit\" value = \"yes\">";
    echo "<input name = \"exit\" type = \"submit\" value = \"no\">";
  }

  // Request email if it hasn't been entered.
  else{
    echo "<h3>Please Enter Your Name and Email Address</h3>" . "<br/>";
    echo "<strong>Name</strong>" . "<br/>";
    echo "<input name = \"name\" type = \"text\" value = \"";
    if(isset($_POST['name'])){echo $_POST['name'];}
    echo "\">" . "<br/>";
    echo "<strong>E-mail</strong>" . "<br/>";
    echo "<input name = \"email\" type = \"text\" value = \"";
    if(isset($_POST['name'])){echo $_POST['email'];}
    echo "\">" . "<br/>";
    echo "<input name = \"start\" type = \"submit\" value = \"Start!!!\">";
  }
}
else{
  echo "<h3>Click The Button Below To See How Fast You Are Against A Computer</h3>";
  echo "<input name = \"welcome\" type = \"submit\" value = \"ENTER\">";
}
?>
<h2>
<?php
 // ***SUB-MESSAGES***
 // Unset number session variable when guess is correct.
 // Print number of guesses and unset the related session variable when guess is correct.  
if(($number == $guess) AND ($color == "green")) {
 
  // Update number of successful game completions in database.
  $book_open = mysql_connect("{$SERVER_NAME}", "{$SQL_USERNAME}", "$SQL_PASSWORD");
  $book_chapter = mysql_select_db("{$DATABASE_NAME}", $book_open);
  $skim = mysql_query(
		      "SELECT plays FROM {$DATABASE_TABLE} WHERE email = '{$_SESSION['email']}';",
		      $book_open);
  while($scan = mysql_fetch_array($skim)) {
    $plays = $scan[0];
    $plays++;
  }
  $book_note = mysql_query(
			   "UPDATE {$DATABASE_TABLE} SET plays = {$plays} WHERE email = '{$_SESSION['email']}'",
			   $book_open);
  $book_close = mysql_close($book_open);

  echo "It took you " . $_SESSION['tries'] . " guesses.";

  // Update least number of tries in database.
  $book_open = mysql_connect("{$SERVER_NAME}", "{$SQL_USERNAME}", "$SQL_PASSWORD");
  $book_chapter = mysql_select_db("{$DATABASE_NAME}", $book_open);
  $skim = mysql_query(
		      "SELECT tries FROM {$DATABASE_TABLE} WHERE email = '{$_SESSION['email']}';",
		      $book_open);
  while($scan = mysql_fetch_array($skim)) {
    $tries = $scan[0];
    $cur_tries = $_SESSION['tries'];
  }
  if($cur_tries < $tries OR !is_numeric($tries)){
    $book_note = mysql_query(
			     "UPDATE {$DATABASE_TABLE} SET tries = {$_SESSION['tries']} WHERE email = '{$_SESSION['email']}'",
			     $book_open);
  }

  $book_close = mysql_close($book_open);
  unset($_SESSION['tries']);
  unset($_SESSION['number']);
}

// If guess is incorrect, print guide.
elseif($number > $guess) {echo "Go Higher";}
elseif($number < $guess) {echo "Go Lower";}
?>
</h2>
</form>
<?php if($COUNTER == true) {include_once('counter.php');}?>
<?php if($GANALYTICS == true) {include_once('ganalytics.php');}?>
</body>
</html>	
