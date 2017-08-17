<?php

include('../connect.php');

$book = $_GET['book'];

function getBookDetails() {
  global $pdo;

  $sql = "SELECT * FROM books WHERE system_number='".$book."'";
  $statement = $pdo->prepare($sql);
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}



if($_POST['book']){
  if(!$_POST['sendTo']) {
    echo 'Please enter a valid email';
  } else {
    $result = getBookDetails();
    // the message
    $msg = "Dear Reader,\n".$_POST['note']."\n\nThis message from the Elastic System contains the details of the following item:\n\nPlease do not reply to this message\n\n";
    $msg .= $result['title'];
    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    // send email
    mail($_POST['sendTo'],"Search Results from the British Library",$msg);
    echo "<script>window.top.close();</script>";
    exit();
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="text/css" http-equiv="Content-Style-Type"/>
<meta content="text/javascript" http-equiv="Content-Script-Type"/>

<link rel="stylesheet" type="text/css" href="emailform.css"/>


<link rel="apple-touch-icon" href="../images/favicon.png" />

<link rel="stylesheet" type="text/css" href="../css/temp.css"/>

<script type="text/javascript" src="../javascript/common.js"></script>
<script type="text/javascript">
  function closeWindow(){
    self.close();
  }
</script>

<title>Explore the British Library</title>
</head>

<body class="tags EXLPopup EXLEmailPopup">
  <form name="emailForm" method="post" action="email.php" id="emailForm">

    <input type="hidden" name="book" value="<?php echo $book; ?>" />

    <table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <div class="tags_title EXLPopupTitleBar">
            <div class="cancel">
              <a href="#" title="Close this window (requires javascript enabled)" onclick="window.close(); return false;">Cancel</a>
            </div>
            <h2>
              <span class="results_corner_with_border">Send By e-mail</span>
            </h2>
          </div>
        </td>
      </tr>
      <tr>
        <td class="tags_body_pop EXLPopupBody">
          <h4 class="addSelect EXLEmailPopupDetailsTitle">Item's Delivery details:</h4>
          <table border="0" cellpadding="0" cellspacing="0" class="sendMail_table EXLEmailPopupDetailsTable">
            <tr>
              <td><label for="subject">Subject:</label></td>
              <td><input type="text" name="subject" size="50" value="Search Results from the Elastic System" id="subject" /></td>
            </tr>
            <tr>
              <td><label for="sendTo">To:</label></td>
              <td><input type="text" name="sendTo" size="50" value="" id="sendTo" /></td>
            </tr>
            <tr>
              <td></td>
              <td class="colspanTD EXLEmailDetailsWideCell">&nbsp;
              </td>
            </tr>
            <tr>
              <td class="txtareaTD EXLEmailDetailsNote"><label for="note">Note:</label></td>
              <td><textarea name="note" cols="40" rows="7" class="long" id="note"></textarea></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2" class="colspanTD" align="right">
                <label for="send" class="offstage EXLOffstage">send</label>
                <input id="send" type="submit" title="Send" class="send_button" value="Send" onclick="save();return false;"/>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>
</body>
</html>

