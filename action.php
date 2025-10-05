<?php

include_once("CheckLDAP.php");

$user = $_POST["login"];
$password = $_POST["password"];

$response_data = "404";

$response_data = authenticate($user, $password);

print "<html lang=\"fr-fr\">\n";
print " <head>\n";
print "  <title>Response</title>\n";
print "  <meta charset=\"UTF-8\"/>\n";
print " </head>\n";
print " <body>\n";
print "  <p>{$response_data["auth"]}</p>\n";
print "  <p>Hi " . $response_data["Nom"] . "</p>\n";
print " </body>\n";
print "</html>";

?>