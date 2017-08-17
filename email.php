<?php

include('../connect.php');

$book = $_GET['book'];

function getBookDetails() {
  global $pdo;
  $book = $_POST['book'];
  $sql = "SELECT * FROM books WHERE system_number='".$book."'";


  $statement = $pdo->prepare($sql);
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  return $result[0];
}



if($_POST['book']){
  if(!$_POST['sendTo']) {
    echo 'Please enter a valid email';
  } else {
    $result = getBookDetails();
    // the message
    $msg = '<html><body>';
    $msg .= "<p>Dear Reader,<br/><br/>This message from the Elastic System contains the details of the following item:<br/>";
    $msg .= "<ul>";

    $msg .= "<li><b>Title: </b>";

    $msg .= "<a href='http://explore.bl.uk/primo_library/libweb/action/dlDisplay.do?vid=BLVU1&afterPDS=true&institution=BL&docId=BLL01".$result['system_number']."'>".$result['title']."</a>";

    $msg .= "</li>";


    if($result['author']) {
      $msg .= "<li><b>Author: </b>".$result['author']."</li>";
    } else {
      $msg .= "<li><b>Corporate Name: </b>".$result['corporate_name']."</li>";
    }

    if($result['publication_date']){
      $msg .= "<li><b>Publication Details: </b>".$result['publication_date']."</li>";
    }

    if($result['notes']){
      $msg .= "<li><b>Notes: </b>".$result['notes']."</li>";
    }

    if($result['transcribed_shelfmark']){
      $msg .= "<li><b>Shelfmark: </b>".$result['transcribed_shelfmark']."</li>";
    }

    $msg .= "</ul><br/>";
    if($_POST['note']){
      $msg .= "<b>Note: </b>".$_POST['note'];
    }
    $msg .= "<br/><br/>Please do not reply to this message";
    $msg .= "</body></html>";

    //$msg = wordwrap($msg,90);

    $headers = "From: noreply@elasticsystem.net\r\n";
    $headers .= "Reply-To: noreply@http://elasticsystem.net\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    // send email
    mail($_POST['sendTo'],"Search Results from the Elastic System",$msg, $headers);

   //echo $msg;
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
              <span class="results_corner_with_border">Email to me</span>
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

