<?php

    $dbserver = "localhost";
    $dbuser = "users_admin";
    $dbpass = "ASY@amguwZ4yo4TR";
    $dbname = "users_admin";
    $dbtablename = "users";

    $pathToArchiveFolder = "archivedUsers/";

    class Userdata {
        public string $id;
        public string $firstName;
        public string $lastName;
        public string $email;

        function __construct($id, $firstName, $lastName, $email){
            $this->id = $id;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->email = $email;
        }
    }

    //echo "Establishing connection!<br>";
    $conn = mysqli_connect($dbserver, $dbuser, $dbpass, $dbname);
    if (!$conn){
        die("Connection failed!<br>Error: " . mysqli_connect_error() . "<br>");
    }

    if (isset($_GET['ids'])){

        // Making sure the string doesn't contain malicious characters
        $capIDS = strtoupper($_GET['ids']);
        $preggedIDS = preg_replace("/[^A-Z0-9\-\,]/", "", $capIDS);
        //echo $preggedIDS . "<br>"; 
        $ids = explode(",", $preggedIDS); // splits string of ids into an array

        foreach ($ids as $id){
            //echo "Making query for " . $id . "<br>";
            $query = "SELECT * FROM " . $dbtablename . " WHERE id='" . $id . "'";

            //echo "Sending query!<br>";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1){
                //echo "Handling data!<br>";
                $row = mysqli_fetch_assoc($result);
                $data = new Userdata($row['id'], $row['firstName'], $row['lastName'], $row['email']);

                //echo "Encoding data to JSON!<br>";
                $dataJSON = json_encode($data);
                //echo $dataJSON . "<br>";

                // Write data to JSON file
                file_put_contents($pathToArchiveFolder . $data->id . ".json", $dataJSON);

                // Confirm data Integrity
                $fileContent = file_get_contents($pathToArchiveFolder . $data->id . ".json");
                if ($dataJSON === $fileContent){
                    //echo "Success!<br>";
                    echo "Archiving " . json_decode($fileContent)->id . "<br>";
                    $query = "DELETE FROM " . $dbtablename . " WHERE id='" . json_decode($fileContent)->id . "'";
                    mysqli_query($conn, $query);
                } else {
                    echo "Data integrity check failed!";
                }
            } else {
                echo "Error: The query returned " . mysqli_num_rows($result) . " results!<br>";
            }
        }
    }
    mysqli_close($conn);
?>