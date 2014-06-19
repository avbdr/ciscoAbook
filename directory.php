<?php
$serverIp = "10.0.0.1";
$companyName = "My company";
require_once ('MysqliDb.php');

header("Content-type: text/xml");
header("Connection: close");
header("Expires: -1");


if (empty ($_GET['directory'])) {
	echo "<CiscoIPPhoneMenu>
		<MenuItem>
			<Name>{$companyName} phone book</Name>
			<URL>http://{$serverIp}/directory.php?directory=contacts</URL>
		</MenuItem>
		<MenuItem>
		    <Name>Search in {$companyName}</Name>
			<URL>http://{$serverIp}/directory.php?directory=search</URL>
			
		</MenuItem>
		</CiscoIPPhoneMenu>";
	exit;
} else if ($_GET['directory'] == 'search') {
	echo "<CiscoIPPhoneInput>
		  <Title>Directory search</Title>
		  <Prompt>Enter person name: </Prompt>
		  <URL>http://{$serverIp}/directory.php?directory=contacts</URL>
		  <InputItem>
			<DisplayName>First Name</DisplayName>
			<QueryStringParam>firstName</QueryStringParam>
			<InputFlags>U</InputFlags>
		  </InputItem>
		  <InputItem>
			<DisplayName>Last Name</DisplayName>
			<QueryStringParam>lastName</QueryStringParam>
			<InputFlags>U</InputFlags>
		  </InputItem>
		  <InputItem>
			<DisplayName>Extension</DisplayName>
			<QueryStringParam>extNum</QueryStringParam>
			<InputFlags>T</InputFlags>
		  </InputItem>

		</CiscoIPPhoneInput>";
	exit;
}


$db = new Mysqlidb ('localhost', 'asteriskuser', 'amp109', 'asterisk');

$name = $_GET['firstName'] . "% ". $_GET['lastName'] . "%";
$db->where ("description", Array ('LIKE'=> $name));
if (!empty ($_GET['extNum']))
	$db->where ("id", Array ('LIKE' => $_GET['extNum']."%"));

$users = $db->get("devices", 32);
foreach ($users as $u) {
	$list .= "<DirectoryEntry> 
          <Name>{$u['description']}</Name> 
          <Telephone>{$u['id']}</Telephone> 
	     </DirectoryEntry>";
}
echo "<CiscoIPPhoneDirectory> 
    <Title>{$companyName} Phone Directory</Title> 
     <Prompt>People reachable via VoIP</Prompt>";
echo $list;
echo "</CiscoIPPhoneDirectory>";
