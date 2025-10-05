<?php
/** --------------------------------------------------------------------------------------
  Nom         : CheckLDAP.php
  Description : Outils d'identification sur le serveur LDAP de Centrale Nantes
  Auteurs     : Jean-Yves Martin
  Date        : 29/04/2021
  Historique  :
  	Jean-Yves Martin	29/04/2021	creation du fichier
-------------------------------------------------------------------------------------- */

/**
	Autenticate through LDAP connection
	@param login
	@param password
	@return array
*/
function checkLDAP($login, $password) {
	$data = array();
	$data["auth"] = false;
	
	if (($login != "") && ($password != "")) {
		// Create LDAP connection
		$server = "rldap.ec-nantes.fr";
		$connectionLDAP = @ldap_connect($server);
		if ($connectionLDAP != null) {
			@ldap_set_option($connectionLDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
			@ldap_set_option($connectionLDAP, LDAP_OPT_REFERRALS, 0);
			if (! @ldap_bind($connectionLDAP)) {
				@ldap_close($connectionLDAP);
				$connectionLDAP = null;
			}
		}
	
		if ($connectionLDAP) {
			if (@ldap_bind($connectionLDAP, "uid=$login, ou=people, dc=ec-nantes, dc=fr", $password)) {
				$data["auth"] = true;

				$Request = @ldap_search($connectionLDAP, "ou=people, dc=ec-nantes, dc=fr", "(uid=$login)");
				if (@ldap_count_entries($connectionLDAP,$Request) == 1) {
					$info = @ldap_get_entries($connectionLDAP, $Request);
					$user = $info[0];
					
					$data["dn"] = $user["dn"];
					$data["Nom"] = $user["sn"][0];
					$data["Prenom"] = $user["givenname"][0];
					$data["Email"] = $user["mail"][0];
					$data["uid"] = $user["uid"][0];
					if (isset($user["supannetuid"])) {
						$data["supannetuid"] = $user["supannetuid"][0];
					}
				}
			}
		}

		@ldap_close($connectionLDAP);
	}

	return $data;
}

/**
	authenticate user
	@param login
	@param password
	@return array
*/
function authenticate($login, $password) {
	$data = array();

	$data["auth"] = false;
	if (($login != "") && ($password != "")) {
		if (function_exists("ldap_connect")) {
			$data = checkLDAP($login, $password);
		} else {
			// Alternate dummy authentication
			$isIdentified = false;
			if (($login == "admin") && ($password == "admin")) {
				$isIdentified = true;
			}
		
			if ($isIdentified) {
				$data["auth"] = true;
				$data["dn"] = "";
				$data["Nom"] = $login;
				$data["Prenom"] = "";
				$data["Email"] = "";
				$data["uid"] = $login;
				$data["supannetuid"] = "";
			}
		}
	}

	return $data;
}

?>