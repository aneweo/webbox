<?php
require_once 'DB.class.php';

class UserTools {

    protected $tbl_folders = 'wbox_folders';
    protected $tbl_users = 'wbox_users';
    protected $db;

    function __construct() {
        $this->db = new DB();
    }

    // Make sure the path is relative
    // e.g: boxes/baopham/private/folder1 -> private/folder1
    public function toRelativePath($box, $path) {
        $pattern = '/^boxes\/' . $box . '\//';
        $relPath = preg_replace($pattern, '', $path);
        return $relPath;
    }

    // Create folder
    // $folder_path: path relative to the box owner's home page (private/folder1, public/folder2)
    public function createFolder($owner, $path) {
        $path = $this->toRelativePath($owner, $path);
        $data = array(
            "owner" => $owner,
            "path" => $path
        );
        $this->db->insert($data, $this->tbl_folders);
    }

    //Update the database when the user deletes a project folder
    public function deleteFolder($owner, $path) {
        $path = $this->toRelativePath($owner, $path);
        mysql_query("DELETE FROM $this->tbl_folders
            WHERE owner = '$owner' AND path = '$path'")
            or die(mysql_error());
    }

    //Return a list of the users with access to the specific folder
    public function getGrantedUsers($owner, $folder_path) {
        $folder_path = $this->toRelativePath($owner, $folder_path);
        $result = mysql_query("SELECT DISTINCT email FROM $this->tbl_folders f, $this->tbl_users u
            WHERE f.path = '$folder_path' AND f.owner = '$owner' AND f.id = u.folder_id") or die(mysql_error());
        $users = array();
        while($row = mysql_fetch_row($result)) {
            $users[] = $row[0];
        }
        return $users;
    }

    //Grant access to a user
    public function grantAccess($owner, $folder_path, $user_email, $key) {
        $folder_path = $this->toRelativePath($owner, $folder_path);
        mysql_query("INSERT INTO $this->tbl_folders (owner, path, private_key) VALUES ('$owner', '$folder_path', '$key')");
        mysql_query("INSERT INTO $this->tbl_users (email, private_key, folder_id) VALUES ('$user_email', '$key',
            (SELECT id FROM $this->tbl_folders WHERE private_key = '$key' AND owner = '$owner' AND path = '$folder_path'))")
            or die(mysql_error());
    }

    //Remove access from a list of emails
    public function removeAccess($owner, $folder_path, $emails) {
        $folder_path = $this->toRelativePath($owner, $folder_path);
        foreach ($emails as $email) {
            mysql_query("DELETE FROM $this->tbl_users
                WHERE  email = '$email'
                AND private_key = (SELECT x.private_key FROM (SELECT DISTINCT f.private_key FROM $this->tbl_folders f, $this->tbl_users u
                                        WHERE f.owner = '$owner' AND f.path = '$folder_path' AND f.private_key IS NOT NULL
                                        AND f.id = u.folder_id AND u.email = '$email') AS x)")
                or die(mysql_error());
        }
    }

    //Return a list of folder paths that the user has access to
    public function getGrantedFolders($user_email) {
        $result = mysql_query("SELECT DISTINCT private_key FROM $this->tbl_users
                WHERE email = '$user_email'");
        $folder_paths = array();
        while ($row = mysql_fetch_row($result)) {
            $result2 = mysql_query("SELECT DISTINCT owner, path FROM $this->tbl_folders WHERE private_key = '$row[0]'");
            while ($r = mysql_fetch_row($result2)) {
                $folder_paths[] = "boxes/$r[0]/$r[1]/";
            }
        }
        return $folder_paths;
    }

    //Check if the private key already exists
    public function keyExists($key) {
        $result = mysql_query("SELECT private_key FROM $this->tbl_folders WHERE private_key = '$key'") or die(mysql_error());
        if (mysql_num_rows($result) == 0) return False;
        return True;
    }

    //Check if the key is valid with the provided email
    public function validKey($email, $key) {
        $result = mysql_query("SELECT private_key FROM $this->tbl_users WHERE private_key = '$key' AND email = '$email'")
            or die(mysql_error());
        if (mysql_num_rows($result) == 0) return False;
        return True;
    }

    //Generate random key
    public function generateKey(){
        // Reference: http://911-need-code-help.blogspot.ca/2009/06/generate-random-strings-using-php.html
        $character_set_array = array();
        $character_set_array[ ] = array('count' => 10, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        $character_set_array[ ] = array('count' => 5, 'characters' => '0123456789');
        $temp_array = array( );
        foreach ( $character_set_array as $character_set ) {
            for ( $i = 0; $i < $character_set['count']; $i++ ) {
                $temp_array[ ] = $character_set['characters'][ rand(0, strlen($character_set['characters']) - 1)];
            }
        }
        shuffle($temp_array);
        $key = implode('', $temp_array);
        // Continue to shuffle if the key already exists
        while ($this->keyExists($key)) {
            shuffle($temp_array);
            $key = implode('', $temp_array);
        }
        return $key;
    }

    //Return the granted user's email from a key
    public function getGrantedUser($key) {
        $result = mysql_query("SELECT DISTINCT email FROM $this->tbl_users WHERE private_key = '$key'");
        if (mysql_num_rows($result) == 0) {
            return 'NA';
        }
        $row = mysql_fetch_row($result);
        return $row[0];
    }

    public function getFoldersFromKey($key) {
        $result = mysql_query("SELECT DISTINCT path FROM $this->tbl_folders WHERE private_key = '$key'");
        $folder_paths = array();
        while($row = mysql_fetch_row($result)) {
            $folder_paths[] = basename($row[0]) . '/';
        }
        return $folder_paths;
    }

    public function getOwnerFromKey($key) {
        $result = mysql_query("SELECT DISTINCT owner FROM $this->tbl_folders f, $this->tbl_users u
            WHERE f.private_key = '$key' AND f.id = u.folder_id");
        $row = mysql_fetch_row($result);
        return $row[0];
    }

}

?>
