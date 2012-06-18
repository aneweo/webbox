<?php
function authenticate($user, $password) {
    // // Active Directory server
    // $ldap_host = "ldap.cs.ubc.ca";
    // // Active Directory DN
    // $ldap_dn = "uid=$user,ou=People,dc=cs,dc=ubc,dc=ca";
    // // connect to active directory
    // $ldap = ldap_connect($ldap_host) or die("Could not connect to LDAP server.");
    // // verify user and password
    // if($bind = @ldap_bind($ldap, $ldap_dn, $password)) {
    //     // valid
    //     ldap_unbind($ldap);
        $_SESSION['user'] = $user;
        return true;
    // } else {
    //     // invalid name or password
    //     return false;
    // }
} 
