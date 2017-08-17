<?php

include('../connect.php');

// sanitize that filename brah.
$filename = filter_var(
	htmlentities($_GET['filename'],ENT_QUOTES,'UTF-8'),
	FILTER_SANITIZE_STRING,
	FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
);

$sql = 'SELECT * FROM books WHERE image_filename = "'.$filename.'"';
$statement = $pdo->prepare($sql);
$statement->execute();
// result should be only one
$result    = $statement->fetch(PDO::FETCH_ASSOC);
$author    = ($result['author'] ? $result['author'] : $result['corporate_name']);;
$title     = $result['title'];
$shelfmark = $result['transcribed_shelfmark_with_punc'];
$pub       = $result['publication_date'];
$system    = $result['system_number'];
?>
<!-- if nothing in author use corporate name -->
<div style="margin-bottom: 8px">
<span class="inside-text-author"><?= $author ?></span>&nbsp;&nbsp;<span class="inside-text-title"> <?= $title ?></span> &nbsp;
<span class="inside-text-date"><?= $pub ?></span><br/>
</div>
<span class="inside-text-link">

<a href="#" onclick="openWindow('http://explore.bl.uk/primo_library/libweb/action/dlDisplay.do?vid=BLVU1&afterPDS=true&institution=BL&docId=BLL01<?=$result['system_number'];?>')">I want this</a>




<!--
http://explore.bl.uk/primo_library/libweb/action/display.do?frbrVersion=2&tabs=moreTab&ct=display&fn=search&doc=BLL01<?=$system;?>&indx=2&recIds=BLL01009535560&recIdxs=1&elementId=1&renderMode=poppedOut&displayMode=full&frbrVersion=2&dscnt=1&scp.scps=scope%3A%28BLCONTENT%29&frbg=&tab=local_tab&dstmp=1472217154611&srt=rank&mode=Basic&vl(488279563UI0)=any&dum=true&tb=t&vl(freeText0)=//urlencode($title);&vid=BLVU1')"> 
-->

<!-- <a href="#" onclick="openWindow('http://explore.bl.uk/primo_library/libweb/action/search.do?dscnt=0&frbg=&scp.scps=scope%3A%28BLCONTENT%29&tab=local_tab&dstmp=1473591174302&srt=rank&ct=search&mode=Basic&vl(488279563UI0)=any&dum=true&tb=t&indx=1&vl(freeText0)=<?=$shelfmark;?>&vid=BLVU1&fn=search')" >I want this</a> -->
<br />
<style>
.emailtome{
    margin-top: 8px;
    display: inline-block;
}
</style>
<a href="#" class="emailtome" onclick="openWindowE('email.php?book=<?=$system;?>')" >Email to me</a>

</span>



