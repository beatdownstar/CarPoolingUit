<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 25.02.2019
 * Time: 16:49
 */

class Database_queries
{

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function isUserExist($user_id): bool
    {
        $userExist = true;

        try {
            $stmt = $this->db->prepare("SELECT * FROM `CPL_users` WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch() == null)
                $userExist = false;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $userExist;
    }

    public function isUserBanned($user_id)
    {

        try {
            $stmt = $this->db->prepare("SELECT banned_user FROM `CPL_users` WHERE user_id = :user_id");
            $stmt->bindParam('user_id', $user_id);
            $stmt->execute();

            $ban = $stmt->fetch();
            $banned = $ban[0];

            return $banned;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function createUser(User $user, $admin)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_users` (`user_id`, `name`, `surname`, `epost`, `pass_hash`, `tel_number`,`user_rating`,`type_id`,`photo`, `banned_user`, `campus_id`, `id_verification`, `accept`)
					VALUES (NULL, :name, :surname, :epost, :pass_hash, :tel_number, :user_rating, :type_id, :photo, :banned_user, 16, :id_verification, :accept) ");

            $name = $user->getName();
            $surname = $user->getSurname();
            $epost = $user->getEpost();
            $password = $user->getPassword();
            // $accept = $user->getAccept();
            if ($admin) {
                $accept = 1;
                $user_type_id = $user->getUserTypeId();
                $id_verification = 'adminCreated';
            } else {
                $id_verification = $user->getIdVerification();
                $user_type_id = 3;
                $accept = 0;
            }
            $tel_number = "";
            $user_rating = 0;
            $tmp_name = "img/no_avatar.jpg";
            $banned_user = 0;
          //  $campus_id = NULL;
            $photo = file_get_contents($tmp_name);

            // $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
            $stmt->bindParam(':pass_hash', $password, PDO::PARAM_STR);
            $stmt->bindParam(':tel_number', $tel_number, PDO::PARAM_INT);
            $stmt->bindParam(':user_rating', $user_rating, PDO::PARAM_INT);
            $stmt->bindParam(':type_id', $user_type_id, PDO::PARAM_INT);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->bindParam(':banned_user', $banned_user, PDO::PARAM_INT);
            $stmt->bindParam(':id_verification', $id_verification, PDO::PARAM_STR);
            $stmt->bindParam(':accept', $accept, PDO::PARAM_INT);
            $stmt->execute();

            $user_id = $this->db->lastInsertId();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $user_id;
    }

    public function showUserData(int $user_id): User
    {
        $user = new User();

        try {

            $stmt = $this->db->prepare("SELECT * FROM `CPL_users` WHERE user_id = :user_id  ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $user = $stmt->fetchObject('User');
            $user->setVehicles($this->showUsersCarData($user_id));

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $user;
    }

    public function updateUserData(User $user)
    {

        //  print_r($user);

        try {

            if ($user->getFeideUser())
                $stmt = $this->db->prepare("UPDATE `CPL_users` SET tel_number = :tel_number, campus_id = :campus_id WHERE user_id=:user_id");
            else
                $stmt = $this->db->prepare("UPDATE `CPL_users` SET surname = :surname, name = :name, tel_number = :tel_number, pass_hash = :pass_hash, campus_id = :campus_id WHERE user_id=:user_id");

            $user_id = $user->getUserId();
            $tel_number = $user->getTelNumber();
            $name = $user->getName();
            $surname = $user->getSurname();
            $pass_hash = $user->getPassword();
            $campus_id = $user->getCampusId();

            $stmt->bindParam(':tel_number', $tel_number, PDO::PARAM_INT);
            $stmt->bindParam(':campus_id', $campus_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($user->getFeideUser()!=true) {
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
                $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            }


            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function updateUserDataAdmin(User $user)
    {

        //  print_r($user);

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET epost = :email, surname = :surname, name = :name, type_id = :user_type WHERE user_id=:user_id");

            $user_id = $user->getUserId();
            $name = $user->getName();
            $surname = $user->getSurname();
            $email = $user->getEpost();
            $userType = $user->getUserTypeId();

            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':user_type', $userType, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function updateFeideUserAdmin($userType, $user_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET type_id = :user_type WHERE user_id=:user_id");

            $stmt->bindParam(':user_type', $userType, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }



    public function setPassword($user)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET pass_hash = :pass_hash WHERE user_id=:user_id");

            $pass_hash = $user->getPassword();
            $user_id = $user->getUserId();

            $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function updateUserPhoto(User $user)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET photo = :photo WHERE user_id=:user_id");

            $user_id = $user->getUserId();
            $photo = $user->getPhoto();

            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function isUserCarExist(int $user_id): bool
    {
        $userCarExist = true;

        try {
            $stmt = $this->db->prepare("SELECT * FROM `CPL_vehicle` WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch() == null)
                $userCarExist = false;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $userCarExist;
    }

    public function showUsersCarData(int $user_id): array
    {

        $cars = array();

        try {

            $stmt = $this->db->prepare("SELECT * FROM `CPL_vehicle` WHERE user_id = :user_id");

            // $user_id = $_SESSION['user']->getUserID();
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            while ($car = $stmt->fetchObject('Car')) {

                $cars[] = $car;
            }

            // $cars = $stmt->fetchObject('Car');

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;

        }

        return $cars;
    }

    public function updateUsersCarData(Car $car)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_vehicle` SET info = :info, number_of_seats = :number_of_seats WHERE vehicle_id=:vehicle_id");
            $vehicle_id = $car->getVehicleId();

            $info = $car->getInfo();
            $number_of_seats = $car->getNumberOfSeats();
            $stmt->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
            $stmt->bindParam(':number_of_seats', $number_of_seats, PDO::PARAM_INT);
            $stmt->bindParam(':info', $info, PDO::PARAM_STR);

            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function updateUsersCarPhoto(Car $car)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_vehicle` SET photo = :photo WHERE vehicle_id=:vehicle_id");

            $vehicle_id = $car->getVehicleId();

            $photo = $car->getPhoto();


            $stmt->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);


            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }


    public function createUsersCar(Car $car)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_vehicle` (`vehicle_id`, `user_id`, `info`, `number_of_seats`, `photo`,`hidden`)
					VALUES (NULL, :user_id, :info, :number_of_seats, :photo, 0)");

            $user_id = $car->getUserId();
            $info = $car->getInfo();
            $number_of_seats = $car->getNumberOfSeats();
            $photo = $car->getPhoto();

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':info', $info, PDO::PARAM_STR);
            $stmt->bindParam(':number_of_seats', $number_of_seats, PDO::PARAM_INT);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }


    public function hideUsersCarData(int $vehicle_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_vehicle` SET hidden = 1 WHERE vehicle_id=:vehicle_id");
            $stmt->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function createUserPreferenses($user_id, $pref_id)
    {

        try {

            $stmt = $this->db->prepare("INSERT INTO `CPL_users_preference` (`user_id`, `pref_id`, `is_active`)
                                        VALUES (:user_id, (SELECT pref_id FROM `CPL_preferences` WHERE pref_id=:pref_id), 0)");

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':pref_id', $pref_id, PDO::PARAM_INT);

            $stmt->execute();
            // $user_id = $this->db->lastInsertId();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function showUserPreferenses(int $userId)
    {
        $preferenceList = array();
        try {
            $stmt = $this->db->prepare('select users_pref.pref_id, pref_description, is_active 
                from `CPL_preferences` as pref 
                inner join `CPL_users_preference` as users_pref 
                on pref.pref_id = users_pref.pref_id
                where users_pref.user_id = :userId;');
            $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            while ($pref = $stmt->fetchObject('Preference')) {
                array_push($preferenceList, $pref);
            }

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $preferenceList;
    }

    public function updateUserPreferences(UserPreferences $newUserPreferences)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_user_preferences` SET smoking = :smoking, pets = :pets, music = :music, talk = :talk WHERE user_id=:user_id");
            $user_id = $newUserPreferences->getUser_id();
            $smoking = $newUserPreferences->getSmoking();
            $pets = $newUserPreferences->getPets();
            $music = $newUserPreferences->getMusic();
            $talk = $newUserPreferences->getTalk();

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':smoking', $smoking, PDO::PARAM_STR);
            $stmt->bindParam(':pets', $pets, PDO::PARAM_STR);
            $stmt->bindParam(':music', $music, PDO::PARAM_STR);
            $stmt->bindParam(':talk', $talk, PDO::PARAM_STR);

            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    /* public function setUserPreferences($pref_id, $user_id, $is_active)
     {

         try {

             $stmt = $this->db->prepare("UPDATE `CPL_users_preference` SET pref_id = :pref_id, is_active = :is_active WHERE user_id=:user_id");



             $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
             $stmt->bindParam(':pref_id', $pref_id, PDO::PARAM_INT);
             $stmt->bindParam(':is_active', $is_active, PDO::PARAM_STR);


             $stmt->execute();

         } catch (InvalidArgumentException $e) {
             print $e->getMessage() . PHP_EOL;
         }
         return true;
     }*/

    public function showCarPhoto(int $vehicle_id)
    {

        try {

            $stmt = $this->db->prepare("SELECT photo FROM `CPL_vehicle` WHERE vehicle_id =:vehicle_id");
            $stmt->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
            $stmt->execute();

            $photo = $stmt->fetch();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $photo;

    }

    public function showUserPhoto(int $user_id)
    {

        try {

            $stmt = $this->db->prepare("SELECT photo FROM `CPL_users` WHERE user_id =:user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $photo = $stmt->fetch();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $photo;

    }

    public function showPoints(): array
    {

        $points = array();

        try {

            $stmt = $this->db->prepare("SELECT * FROM `CPL_point` ");

            $stmt->execute();

            while ($point = $stmt->fetchObject('Point')) {

                $points[] = $point;
            }


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $points;

    }

    public function showPoint(int $point_id): Point
    {

        $point = new Point();

        try {

            $stmt = $this->db->prepare("SELECT address, point_name FROM `CPL_point` WHERE point_id =:point_id");
            $stmt->bindParam(':point_id', $point_id, PDO::PARAM_INT);
            $stmt->execute();

            $point = $stmt->fetchObject('Point');


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $point;

    }


    public function registerNewTrip(Trip $trip, $userId)
    {
        try {
            $destination = $trip->getDestinationPoint();
            $departure = $trip->getDeparturePoint();

            $this->insertIntoGooglePoint($destination);
            $this->insertIntoGooglePoint($departure);

            $destinationId = $destination->getPointId();
            $departureId = $departure->getPointId();
            $dateOfDeparture = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
            $latestArrivalDate = $trip->getLatestArrivalDate()->format('Y-m-d H:i:s');
            $traveltime = $trip->getTravelTime();



            $stmt = $this->db->prepare('insert into `CPL_trip` (departure_google_id, destination_google_id, traveltime, latest_arrival_date, date_of_departure) 
                values(:departure, :destination, :traveltime, :latestArrivalDate, :dateOfDeparture);');
            $stmt->bindParam('departure', $departureId);
            $stmt->bindParam('destination', $destinationId);
            $stmt->bindParam('traveltime', $traveltime, PDO::PARAM_INT);
            $stmt->bindParam('latestArrivalDate', $latestArrivalDate, PDO::PARAM_STR);
            $stmt->bindParam('dateOfDeparture', $dateOfDeparture, PDO::PARAM_STR);
            $stmt->execute();

            $lastId = $this->db->lastInsertId();

            //Legg til bruker som passasjer
            $this->assignUserToTrip($lastId, $userId);

            return $lastId;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function updateTrip($trip)
    {
        try {
            $destination = $trip->getDestinationPoint();
            $departure = $trip->getDeparturePoint();

            $this->insertIntoGooglePoint($destination);
            $this->insertIntoGooglePoint($departure);

            $destinationId = $destination->getPointId();
            $departureId = $departure->getPointId();
            $dateOfDeparture = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
            $latestArrivalDate = $trip->getLatestArrivalDate()->format('Y-m-d H:i:s');
            $traveltime = $trip->getTravelTime();

            $stmt = $this->db->prepare('UPDATE `CPL_trip` SET departure_google_id = :departure, destination_google_id = :destination, traveltime = :traveltime, 
                                                 latest_arrival_date = :latestArrivalDate, date_of_departure = :dateOfDeparture');

            $stmt->bindParam('departure', $departureId);
            $stmt->bindParam('destination', $destinationId);
            $stmt->bindParam('traveltime', $traveltime, PDO::PARAM_INT);
            $stmt->bindParam('latestArrivalDate', $latestArrivalDate, PDO::PARAM_STR);
            $stmt->bindParam('dateOfDeparture', $dateOfDeparture, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }


    public function showAlternativeTrips($trip, $offset, $interval, $preferences = null, $hasDriver = null)
    {
// midlertidig kode
        try {
            $departure = $trip->getDeparturePoint()->getPointId();
            $destination = $trip->getDestinationPoint()->getPointId();
            $departureDate = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
            $arrivalDate = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
            $offset = ($offset - 1) * 5;
            $travelTime = $trip->getTravelTime();
            $interval = -($interval);
           // $interval = -($interval - ($travelTime / (60 * 60)));

            $tripArray = array();
            $stmt = $this->db->prepare("SELECT CPL_trip.* FROM CPL_trip INNER JOIN CPL_google_point as dep on CPL_trip.departure_google_id = dep.google_id AND dep.google_id = :departure 
                                                INNER JOIN CPL_google_point as des on CPL_trip.destination_google_id = des.google_id AND des.google_id = :destination
                                                WHERE date_of_departure BETWEEN DATE_ADD(:departureDate, INTERVAL :interval MINUTE) AND :arrivalDate ORDER BY date_of_departure DESC LIMIT 5 OFFSET :offset");

            $stmt->bindParam(':departure', $departure, PDO::PARAM_INT);
            $stmt->bindParam(':destination', $destination, PDO::PARAM_INT);
            $stmt->bindParam(':departureDate', $departureDate, PDO::PARAM_STR);
            $stmt->bindParam(':arrivalDate', $arrivalDate, PDO::PARAM_STR);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':interval', $interval, PDO::PARAM_INT);
         //   $stmt->bindParam(':travelTime', $travelTime, PDO::PARAM_INT);
            $stmt->execute();

            for ($count = $stmt->rowCount(); $count > 0; $count--) {
                $rowData = $stmt->fetch();
                $departurePoint = $this->getPoint($rowData['departure_google_id']);
                $destinationPoint = $this->getPoint($rowData['destination_google_id']);

                //$departurePoint = new Point();
                //$destinationPoint = new Point();
                //$departurePoint->setPointId($rowData['departure_google_id']);
                //$destinationPoint->setPointId($rowData['destination_google_id']);
                $trip = Trip::fromArray($rowData);

                if ($preferences !== null) {
                    $excludeTrip = false;

                    $tripPrefs = $this->getPreferencesFromTrip($trip->getTripId());

                    $tripPrefArray = array();

                    foreach ($tripPrefs as $prefObject) {
                        $tripPrefArray[$prefObject->getPrefId()] = $prefObject->getIsActive();
                    }




                    foreach ($preferences as $pref_id => $pref_setting) {
                        if (!array_key_exists($pref_id, $tripPrefArray) && $pref_setting != 0) {
                            $excludeTrip = true;
                        } else if (array_key_exists($pref_id, $tripPrefArray)) {
                            if ($tripPrefArray[$pref_id] == 1 && $pref_setting === "-1" || $tripPrefArray[$pref_id] == 0 && $pref_setting === "1") $excludeTrip = true;
                        }
                    }

                    if ($hasDriver !== null) {
                        if (($hasDriver === '1' && is_null($rowData['driver_id']) || ($hasDriver === '-1' && !is_null($rowData['driver_id']))))
                            $excludeTrip = true;
                    }

                    if ($excludeTrip) continue;
                }




                $trip->setDeparturePoint($departurePoint);
                $trip->setDestinationPoint($destinationPoint);
                if (!is_null($rowData['driver_id'])) {
                    $driver = $this->showUserData($rowData['driver_id']);
                    $trip->setDriver($driver);
                }
                $passengers = $this->getPassengers($rowData['trip_id']);
                $trip->setPassengers($passengers);

                array_push($tripArray, $trip);
            }
            return $tripArray;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }


    }

    public function countNumberOfTrips($trip, $interval)
    {

        $departure = $trip->getDeparturePoint()->getPointId();
        $destination = $trip->getDestinationPoint()->getPointId();
        $departureDate = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
        $arrivalDate = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
        $travelTime = $trip->getTravelTime();
        $interval = -($interval);
        // $interval = -($interval - ($travelTime / (60 * 60)));

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM CPL_trip INNER JOIN CPL_google_point as dep on CPL_trip.departure_google_id = dep.google_id AND dep.google_id = :departure 
                                                INNER JOIN CPL_google_point as des on CPL_trip.destination_google_id = des.google_id AND des.google_id = :destination
                                                WHERE date_of_departure BETWEEN DATE_ADD(:departureDate, INTERVAL :interval MINUTE) AND :arrivalDate");

            $stmt->bindParam(':departure', $departure, PDO::PARAM_INT);
            $stmt->bindParam(':destination', $destination, PDO::PARAM_INT);
            $stmt->bindParam(':departureDate', $departureDate, PDO::PARAM_STR);
            $stmt->bindParam(':arrivalDate', $arrivalDate, PDO::PARAM_STR);
            $stmt->bindParam(':interval', $interval, PDO::PARAM_INT);
            //   $stmt->bindParam(':travelTime', $travelTime, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function assignUserToTrip($tripID, $userID)
    {

        try {
            if ($this->isPassengerInLogTable($tripID, $userID)) {
                $this->removePassengerFromLog($tripID, $userID);
            }

            $stmt = $this->db->prepare("INSERT INTO `CPL_passenger` (`trip_id`, `user_id`) VALUES (:tripID, :userID)");
            $stmt->bindParam('tripID', $tripID);
            $stmt->bindParam('userID', $userID);
            $stmt->execute();

            $err = $stmt->errorInfo();

            if (isset($err[1])) {
                if ($err[1] == 1062)
                    return false;
            }
            return true;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function setDriverToTrip($tripID, $userID)
    {

        try {
            $stmt = $this->db->prepare("UPDATE `CPL_trip` SET driver_id = :userID WHERE trip_id = :tripID");
            $stmt->bindParam('tripID', $tripID);
            $stmt->bindParam('userID', $userID);
            return $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
            return false;
        }

    }

    public function removeDriverFromTrip($tripID)
    {

        try {
            $stmt = $this->db->prepare("UPDATE `CPL_trip` SET driver_id = NULL WHERE trip_id = :tripID");
            $stmt->bindParam('tripID', $tripID);
            return $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
            return false;
        }

    }

    public function removeUserFromTrip($tripId, $userId)
    {
        try {

            $stmt = $this->db->prepare('delete from CPL_passenger where trip_id = :tripId and user_id = :userId;');
            $stmt->bindParam('tripId', $tripId);
            $stmt->bindParam('userId', $userId);
            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getPoint($pointId)
    {
        try {

            $stmt = $this->db->prepare('select `point`.* from CPL_google_point as `point` where google_id = :pointId;');
            $stmt->bindParam(':pointId', $pointId);
            $stmt->execute();

            $point = $stmt->fetchObject('Point');
            return $point;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getAllPoints()
    {
        try {
            $stmt = $this->db->prepare('SELECT `point`.* FROM CPL_point AS `point`');
            $stmt->execute();
            $points = [];

            while ($point = $stmt->fetchObject('Point')) {
                $points[] = $point;
            }
            return $points;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
            return [];
        }
    }

    public function getAllCampuses()
    {
        try {
            $tripArray = array();
            $stmt = $this->db->prepare('SELECT * FROM CPL_department');
            $stmt->execute();

            while ($campuses = $stmt->fetchObject('Campus')) {
                array_push($tripArray, $campuses);
            }
            return $tripArray;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getCampusName($campus_id)
    {
        try {
            $stmt = $this->db->prepare('SELECT dep_name FROM CPL_department WHERE dep_id = :campus_id');
            $stmt->bindParam(':campus_id', $campus_id);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getMyTrips($userId) {
        try {

            $tripArray = array();

            $stmt = $this->db->prepare(
                'select trip.*, depart.address as departure_address, dest.address as destination_address
                    from CPL_passenger as pass inner join CPL_trip as trip on trip.trip_id = pass.trip_id 
                    inner join CPL_google_point as depart on depart.google_id = trip.departure_google_id
                    inner join CPL_google_point as dest on dest.google_id = trip.destination_google_id
                    where pass.user_id = :userID;');
            $stmt->bindParam('userID', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $count = $stmt->rowCount();

            for ($count; $count > 0; $count--) {
                $rowData = $stmt->fetch();
                //$departurePoint = $this->getPoint($rowData['departure_google_id']);
                //$destinationPoint = $this->getPoint($rowData['destination_google_id']);

                //$departurePoint = new Point();
                //$destinationPoint = new Point();
                //$departurePoint->setPointId($rowData['departure_google_id']);
                //$destinationPoint->setPointId($rowData['destination_google_id']);
                $trip = Trip::fromArray($rowData);
                $trip->createDeparturePoint($rowData['departure_google_id'], $rowData['departure_address']);
                $trip->createDestinationPoint($rowData['destination_google_id'], $rowData['destination_address']);
                if (!is_null($rowData['driver_id'])) {
                    $driver = $this->showUserData($rowData['driver_id']);
                    $trip->setDriver($driver);
                } else {
                    $trip->setDriver(new User());
                }
                //$trip->initTrip();
                array_push($tripArray, $trip);
            }
            return $tripArray;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;

        }
    }

    public function getTrip($tripId)
    {
        try {

            $stmt = $this->db->prepare('select trip.* from CPL_trip as trip
                where trip_id = :tripId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->execute();

            $rowData = $stmt->fetch();

            $departurePoint = $this->getPoint($rowData['departure_google_id']);
            $destinationPoint = $this->getPoint($rowData['destination_google_id']);

            //$departurePoint = new Point();
            //$destinationPoint = new Point();
            //$destinationPoint->setPointId($rowData['destination_google_id']);
            //$departurePoint->setPointId($rowData['departure_google_id']);

            $trip = Trip::fromArray($rowData);
            $trip->setDeparturePoint($departurePoint);
            $trip->setDestinationPoint($destinationPoint);
            $trip->setTravelTime($rowData['traveltime']);
            if (!is_null($rowData['driver_id'])) {
                $driver = $this->showUserData($rowData['driver_id']);
                $trip->setDriver($driver);
            }

            $passengers = $this->getPassengers($rowData['trip_id']);
            $trip->setPassengers($passengers);
            //$trip->initTrip();
            return $trip;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getMyDeletedTrips($userId)
    {
        try {
            $tripArray = array();
            /*
            $stmt = $this->db->prepare(
                'SELECT trip.*
                FROM CPL_deleted_passenger AS pass INNER JOIN CPL_deleted_trip AS trip ON trip.trip_id = pass.trip_id 
                WHERE pass.user_id = :userID;');
            $stmt->bindParam('userID', $userId);
            $stmt->execute();
            */
            $stmt = $this->db->prepare('select trip.*, depart.address as departure_address, dest.address as destination_address
                from CPL_deleted_passenger as pass inner join CPL_deleted_trip as trip on trip.trip_id = pass.trip_id 
                inner join CPL_google_point as depart on depart.google_id = trip.departure_google_id
                inner join CPL_google_point as dest on dest.google_id = trip.destination_google_id
                where pass.user_id = :userId;');
            $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $count = $stmt->rowCount();

            for ($count; $count > 0; $count--) {
                $rowData = $stmt->fetch();
                //$departurePoint = $this->getPoint($rowData['departure_point_id']);
                //$destinationPoint = $this->getPoint($rowData['destination_point_id']);
                $trip = Trip::fromArray($rowData);
                $trip->createDeparturePoint($rowData['departure_google_id'], $rowData['departure_address']);
                $trip->createDestinationPoint($rowData['destination_google_id'], $rowData['destination_address']);

                if (!is_null($rowData['driver_id'])) {
                    $driver = $this->showUserData($rowData['driver_id']);
                    $trip->setDriver($driver);
                } else {
                    $trip->setDriver(new User());
                }
                //$trip->setDeparturePoint($departurePoint);
                //$trip->setDestinationPoint($destinationPoint);

                array_push($tripArray, $trip);
            }
            return $tripArray;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;

        }
    }

    public function getPassengers($tripId)
    {
        try {

            $passengers = Array();
            $stmt = $this->db->prepare('select users.* from CPL_users as users inner join CPL_passenger as pass 
                on users.user_id = pass.user_id
                where pass.trip_id = :tripId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->execute();

            while ($pass = $stmt->fetchObject('User')) {
                array_push($passengers, $pass);
            }
            return $passengers;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function isUserInAtrip(int $user_id, int $trip_id): bool
    {
        $userInATrip = true;

        try {
            $stmt = $this->db->prepare("SELECT * FROM `CPL_passenger` WHERE user_id = :user_id AND trip_id=:trip_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch() == null)
                $userInATrip = false;

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $userInATrip;
    }


    public function getUserPassword($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT pass_hash FROM `CPL_users` WHERE epost = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $col = $stmt->fetch();

            $password = $col[0];

            return $password;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getUserId($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM `CPL_users` WHERE epost = :email");
            $stmt->bindParam('email', $email);
            $stmt->execute();

            $col = $stmt->fetch();

            $userId = $col[0];

            return $userId;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }


    public function emailExists(String $epost): bool
    {

        try {

            $stmt = $this->db->prepare("SELECT count(*) FROM `CPL_users` WHERE `epost`=:epost");
            $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
            $stmt->execute();

            $email_exists = $stmt->fetch();

            if ($email_exists['count(*)'] == 0)
                return false;
            else
                return true;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return false;
    }

    public function newEventdeleteTrip($tripId, $latestArrivalDate)
    {
        try {
            $latestArrival = $latestArrivalDate->format('Y-m-d H:i:s');
            $stmt = $this->db->prepare("CREATE EVENT delete_trip_$tripId
                ON SCHEDULE AT DATE_ADD(:departure, INTERVAL 30 MINUTE)
                DO 
                BEGIN 
                DELETE FROM CPL_trip WHERE trip_id =:id; END ");

            $stmt->bindParam('id', $tripId);
            $stmt->bindParam('departure', $latestArrival);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }


    public function setAccept(string $id_verification)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET accept=1 WHERE id_verification=:id_verification");
            $stmt->bindParam(':id_verification', $id_verification, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function getAccepted($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT accept FROM `CPL_users` WHERE user_id = :user_id");
            $stmt->bindParam('user_id', $user_id);
            $stmt->execute();

            $accept = $stmt->fetch();
            $accepted = $accept[0];

            return $accepted;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function checkIfTripExists($tripID)
    {
        try {
            $trip = true;

            $stmt = $this->db->prepare("SELECT * FROM CPL_trip WHERE trip_id = :tripID");
            $stmt->bindParam('tripID', $tripID);
            $stmt->execute();

            if ($stmt->fetch() == null)
                $trip = false;

            return $trip;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function isPassengerInLogTable($tripId, $userId): bool
    {
        try {
            $stmt = $this->db->prepare('select * from `CPL_deleted_passenger`
                where trip_id = :tripId and user_id = :userId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->rowCount();
            if ($result === 0) {
                return false;
            } else {
                return true;
            }

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function removePassengerFromLog($tripId, $userId)
    {
        try {

            $stmt = $this->db->prepare('delete from `CPL_deleted_passenger` 
                where trip_id = :tripId and user_id = :userId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function addNewPreference($prefName)
    {
        try {

            $stmt = $this->db->prepare('insert into `CPL_preferences`(pref_description) 
                values (:prefName);');
            $stmt->bindParam('prefName', $prefName, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function deletePreference($prefId)
    {
        try {
            $stmt = $this->db->prepare('delete from `CPL_preferences` where pref_id = :prefId;');
            $stmt->bindParam('prefId', $prefId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function setNewPreferenceOnTrip($tripId, $prefId, $isActive)
    {
        try {
            $stmt = $this->db->prepare('insert into `CPL_trip_preferences` 
                values (:tripId, :prefId, :isActive);');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->bindParam('prefId', $prefId, PDO::PARAM_INT);
            $stmt->bindParam('isActive', $isActive, PDO::PARAM_INT);
            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function updatePreferenceOnTrip($tripId, $prefId, $isActive)
    {
        try {
            $stmt = $this->db->prepare('UPDATE `CPL_trip_preferences` SET is_active = :isActive
                WHERE trip_id = :tripId and pref_id = :prefId');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->bindParam('prefId', $prefId, PDO::PARAM_INT);
            $stmt->bindParam('isActive', $isActive, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function removePreferenceFromTrip($tripId, $prefId)
    {
        try {
            $stmt = $this->db->prepare('delete from `CPL_trip_preferences` where 
                trip_id = :tripId and pref_id = :prefId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->bindParam('prefId', $prefId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getPreferencesFromTrip($tripId)
    {
        $preferenceList = array();
        try {
            $stmt = $this->db->prepare('select trip_pref.pref_id, pref_description, is_active 
                from `CPL_preferences` as pref 
                inner join `CPL_trip_preferences` as trip_pref 
                on pref.pref_id = trip_pref.pref_id
                where trip_pref.trip_id = :tripId;');
            $stmt->bindParam('tripId', $tripId, PDO::PARAM_INT);
            $stmt->execute();

            while ($pref = $stmt->fetchObject('Preference')) {
                array_push($preferenceList, $pref);
            }

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $preferenceList;
    }

    public function getAllPreferences()
    {
        $preferences = [];

        try {
            $stmt = $this->db->prepare('SELECT * FROM CPL_preferences');
            $stmt->execute();
            while ($preference = $stmt->fetchObject('Preference')) {
                $preferences[] = $preference;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $preferences;
    }

    public function userHasJustDeletedCars($user_id): bool
    {

        try {

            $stmt = $this->db->prepare("SELECT count(*) FROM `CPL_vehicle` WHERE `user_id`=:user_id AND `hidden`=0");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $userHasJustDeletedCars = $stmt->fetch();

            if ($userHasJustDeletedCars['count(*)'] == 0)
                return true;
            else
                return false;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return false;
    }

    public function insertChatMessage($author_id, $message, $trip_id)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `chat_message` (`author_id`, `message`, `trip_id`) VALUES (:author_id, :message, :trip_id) ");
            $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();

            $output = $this->db->lastInsertId();
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
            return -1;
        }
        return $output;
    }

    public function getChatMessages($trip_id)
    {
        $messages = [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM `chat_message` WHERE `trip_id`=:trip_id ORDER BY `id` DESC LIMIT 15");
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();

            while ($message = $stmt->fetchObject('ChatMessage')) {
                $message->setAuthorName($this->getUserName($message->getAuthorId()));
                $messages[] = $message;
            }


        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
            return [];
        }
        return array_reverse($messages);
    }

    public function getUnreadChatMessages($trip_id, $last_read_id)
    {
        $messages = [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM `chat_message` WHERE `trip_id`=:trip_id AND `id` > :last_read_id");
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->bindParam(':last_read_id', $last_read_id, PDO::PARAM_INT);
            $stmt->execute();

            while ($message = $stmt->fetchObject('ChatMessage')) {
                $message->setAuthorName($this->getUserName($message->getAuthorId()));
                $messages[] = $message;
            }

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
            return [];
        }
        return $messages;
    }

    public function getUserName($user_id)
    {
        try {

            $stmt = $this->db->prepare("SELECT `name` FROM `CPL_users` WHERE user_id = :user_id  ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $username = $stmt->fetchColumn();

        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $username;
    }

    public function showUsers(): array
    {

        $users = array();

        try {

            $stmt = $this->db->prepare("SELECT * FROM `CPL_users` ");
            $stmt->execute();

            $userTypes = $this->getUserTypes();
            $campuses = $this->getAllCampuses();
            $count = (count($userTypes) > count($campuses)) ? count($userTypes) : count($campuses);

            while ($user = $stmt->fetchObject('User')) {
             /*   $userType = $this->getAUserType($user->getUserTypeId());
                $campusName = $this->getCampusName($user->getCampusId());
                $user->createUserTypeObject($user->getUserTypeId(), $userType);
                $user->createCampusObject($user->getCampusId(), $campusName);*/
                for($i = 0; $i < $count; $i++){
                    if($i < count($userTypes)) {
                        if ($userTypes[$i]->getType_id() == $user->getUserTypeId())
                            $user->createUserTypeObject($user->getUserTypeId(), $userTypes[$i]->getType());
                    }
                    if($i < count($campuses)) {
                        if ($campuses[$i]->getDep_id() == $user->getCampusId())
                            $user->createCampusObject($user->getCampusId(), $campuses[$i]->getDep_name());
                    }
                }

                $users[] = $user;
            }


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $users;

    }

    public function setUserBan(String $epost)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET banned_user=1 WHERE epost=:epost");
            $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function setUserAktiv(String $epost)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET banned_user=0 WHERE epost=:epost");
            $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function getTripDestinationId($tripId)
    {
        try {

            $stmt = $this->db->prepare('select destination_google_id from CPL_trip where trip_id = :tripId;');
            $stmt->bindParam('tripId', $tripId);
            $stmt->execute();

            $destId = $stmt->fetch()['destination_google_id'];
            return $destId;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function updateGooglePoint($point) {
        try {
            $id = $point->getPointId();
            $address = $point->getPointName();

            $stmt = $this->db->prepare('UPDATE `CPL_google_point` SET `address` = :address WHERE `google_id` = :id');
            $stmt->bindParam('id', $id, PDO::PARAM_STR);
            $stmt->bindParam('address', $address, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            print $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    public function insertIntoGooglePoint($point){

        $this->updateGooglePoint($point);

        try {
            $pointId = $point->getPointId();
            $pointAddress = $point->getPointName();

            $stmt = $this->db->prepare('insert into `CPL_google_point`(google_id, address) values (:googleId, :address)');
            $stmt->bindParam('googleId', $pointId);
            $stmt->bindParam('address', $pointAddress);
            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getUserTypes()
    {
        try {
            $array = array();
            $stmt = $this->db->prepare("SELECT * FROM CPL_user_type");
            $stmt->execute();

            while ($userTypes = $stmt->fetchObject("UserType"))
                array_push($array, $userTypes);

            return $array;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getAUserType($userTypeId)
    {
        try {

            $stmt = $this->db->prepare("SELECT type FROM CPL_user_type WHERE type_id = :userTypeId");
            $stmt->bindParam(':userTypeId', $userTypeId, PDO::PARAM_INT);
            $stmt->execute();

            $userType = $stmt->fetchColumn();
            return $userType;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }


    public function createPreference(Preference $preference)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_preferences` (`pref_id`, `pref_description`)
					VALUES (NULL, :pref_description) ");

            $pref_description = $preference->getPrefDescription();

            $stmt->bindParam(':pref_description', $pref_description, PDO::PARAM_STR);

            $stmt->execute();

            $pref_id = $this->db->lastInsertId();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $pref_id;
    }

    public function setPrefDescription($pref_description, $pref_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_preferences` SET pref_description=:pref_description WHERE pref_id=:pref_id");
            $stmt->bindParam(':pref_description', $pref_description, PDO::PARAM_STR);
            $stmt->bindParam(':pref_id', $pref_id, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function sendRapportedUser(ReportedUsers $rapport)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_reported_user` ( `rapport_id`, `reported_user_id`, `sender_user_id`, `reason`, `description`)
					VALUES (NULL, :reported_user_id, :sender_user_id, :reason, :description)");

            $reportedUserId = $rapport->getReportedUserId();
            $senderUserId = $rapport->getSenderUserId();
            $reason = $rapport->getReason();
            $description = $rapport->getDescription();

            $stmt->bindParam(':reported_user_id', $reportedUserId, PDO::PARAM_INT);
            $stmt->bindParam(':sender_user_id', $senderUserId, PDO::PARAM_INT);
            $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function showReportedUsers() {

        try {
            $array = array();
            $stmt = $this->db->prepare("SELECT 
   report.*, rep.name as reportedUserName, rep.surname as reportedUserSurname, send.name as senderUserName, send.surname as senderUserSurname
FROM (select * from CPL_reported_user) report
LEFT JOIN CPL_users rep ON rep.user_id = report.reported_user_id 
LEFT JOIN CPL_users send ON send.user_id = report.sender_user_id");
            $stmt->execute();

            while($reportedUsers = $stmt->fetchObject("ReportedUsers"))
                array_push($array, $reportedUsers);

            return $array;

        } catch(InvalidArgumentException $e){
            print $e->getMessage() . PHP_EOL;
        }

    }


    public function getAllReasons()
    {
        $reasons = [];

        try {
            $stmt = $this->db->prepare('SELECT * FROM CPL_reasons');
            $stmt->execute();
            while ($reason = $stmt->fetchObject('Reasons')) {
                $reasons[] = $reason;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $reasons;
    }

    public function createReason(Reasons $reasons)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_reasons` (`reason_id`, `reason_description`)
					VALUES (NULL, :reason_description) ");

            $reason_description = $reasons->getReasonDescription();

            $stmt->bindParam(':reason_description', $reason_description, PDO::PARAM_STR);

            $stmt->execute();

            $reason_id = $this->db->lastInsertId();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $reason_id;
    }

    public function setReasonDescription($reason_description, $reason_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_reasons` SET reason_description=:reason_description WHERE reason_id=:reason_id");
            $stmt->bindParam(':reason_description', $reason_description, PDO::PARAM_STR);
            $stmt->bindParam(':reason_id', $reason_id, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function deleteReason($reason_id)
    {
        try {
            $stmt = $this->db->prepare('delete from `CPL_reasons` where reason_id = :reason_id;');
            $stmt->bindParam('reason_id', $reason_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getAllTrips()
    {
        $tripArray = array();
        try {
            $stmt = $this->db->prepare('select distinct trip.*, depart.address as departure_address, dest.address as destination_address
                    from CPL_passenger as pass inner join CPL_trip as trip on trip.trip_id = pass.trip_id 
                    inner join CPL_google_point as depart on depart.google_id = trip.departure_google_id
                    inner join CPL_google_point as dest on dest.google_id = trip.destination_google_id');
            $stmt->execute();

            $count = $stmt->rowCount();

            for ($count; $count > 0; $count--) {
                $rowData = $stmt->fetch();
                //$departurePoint = $this->getPoint($rowData['departure_point_id']);
                //$destinationPoint = $this->getPoint($rowData['destination_point_id']);

                $trip = Trip::fromArray($rowData);
                $trip->createDeparturePoint($rowData['departure_google_id'], $rowData['departure_address']);
                $trip->createDestinationPoint($rowData['destination_google_id'], $rowData['destination_address']);
                if (!is_null($rowData['driver_id'])) {
                    $driver = $this->showUserData($rowData['driver_id']);
                    $trip->setDriver($driver);
                } else {
                    $trip->setDriver(new User());
                }
                $tripID = $rowData['trip_id'];
                $passengers = $this->getPassengers($tripID);
                $preferences = $this->getPreferencesFromTrip($tripID);
                $trip->setPassengers($passengers);
                $trip->setPreferences($preferences);

                array_push($tripArray, $trip);
            }
            return $tripArray;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function countNumberOfCurrentTrips()
    {
        try {
            $stmt = $this->db->prepare('SELECT count(*) FROM CPL_trip');
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function countNumberOfDrivers()
    {
        try {
            $stmt = $this->db->prepare('SELECT count(*) FROM CPL_trip WHERE driver_id IS NOT NULL');
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function countNumberOfDeletedTrips()
    {
        try {
            $stmt = $this->db->prepare('SELECT count(*) FROM CPL_deleted_trip');
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function countNumberOfCampusUsers($campus)
    {
        try {
            $stmt = $this->db->prepare('SELECT count(*) FROM CPL_users WHERE campus_id = :campus');
            $stmt->bindParam(':campus', $campus, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function countNumberOfCampusPassengers($campus)
    {
        try {
            $stmt = $this->db->prepare('SELECT count(*) FROM CPL_passenger as pass, CPL_users as user WHERE pass.user_id = user.user_id and campus_id :campus');
            $stmt->bindParam(':campus', $campus, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function sendFeedback(Feedback $feedback)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_feedback` ( `feedback_id`, `sender_id`, `feedback_text`)
					VALUES (NULL, :sender_id, :feedback_text)");

            $senderId = $feedback->getSenderId();
            $feedback_text = $feedback->getFeedbackText();


            $stmt->bindParam(':sender_id', $senderId, PDO::PARAM_INT);
            $stmt->bindParam(':feedback_text', $feedback_text, PDO::PARAM_STR);
            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function showFeedbacks() {

        try {
            $array = array();
            $stmt = $this->db->prepare("SELECT 
   feedback.*, send.name as senderName, send.surname as senderSurname, send.epost as epost
FROM (select * from CPL_feedback) feedback
LEFT JOIN CPL_users send ON send.user_id = feedback.sender_id");
            $stmt->execute();

            while($feedbacks = $stmt->fetchObject("Feedback"))
                array_push($array, $feedbacks);

            return $array;

        } catch(InvalidArgumentException $e){
            print $e->getMessage() . PHP_EOL;
        }

    }

    public function setAnswered($feedback_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_feedback` SET answered=1 WHERE feedback_id=:feedback_id");
            $stmt->bindParam(':feedback_id', $feedback_id, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function setSolved($rapport_id)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_reported_user` SET solved=1 WHERE rapport_id=:rapport_id");
            $stmt->bindParam(':rapport_id', $rapport_id, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function insertFeideUserToDatabase($feideId, $email, $firstName, $lastName)
    {

        $tmp_name = "img/no_avatar.jpg";
        $photo = file_get_contents($tmp_name);

        try {

            $stmt = $this->db->prepare("INSERT INTO `CPL_users` (user_id, feide_id, epost, surname, name, photo, banned_user, type_id, campus_id, accept) VALUES (NULL, :feideId, :email, :lastName, :firstName, :photo, 0, 3, 16, 1)");
            $stmt->bindParam(':feideId', $feideId, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
            $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
            $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
            $stmt->execute();



        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $this->db->lastInsertId();
    }

    public function updateFeideUser($userId, $email, $firstName, $lastName)
    {
        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET epost = :email, surname = :lastName, name = :firstName, WHERE user_id = :userId");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
            $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function checkIfFeideUser($email)
    {
        try {

            $stmt = $this->db->prepare("SELECT feide_id FROM `CPL_users` WHERE epost = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $isFeideUser = $stmt->fetchColumn();

            return empty ($isFeideUser) ? false : true;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function setPasswordRecoveryId($id_pass_recover, $epost)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET id_pass_recover=:id_pass_recover WHERE epost=:epost");
            $stmt->bindParam(':id_pass_recover', $id_pass_recover, PDO::PARAM_STR);
            $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function setNewPassword($id_pass_recover, $pass_hash)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users` SET pass_hash=:pass_hash WHERE id_pass_recover=:id_pass_recover");
            $stmt->bindParam(':id_pass_recover', $id_pass_recover, PDO::PARAM_STR);
            $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function validateTrip(Trip $trip, $userId) :bool {
        $count = 0;
        try {
            $departureDate = $trip->getDateOfDeparture()->format('Y-m-d H:i:s');
            $arrivalDate = $trip->getLatestArrivalDate()->format('Y-m-d H:i:s');

            $stmt = $this->db->prepare('SELECT trip.*, pass.* FROM `CPL_trip` AS trip INNER JOIN `CPL_passenger` AS pass
                ON trip.trip_id = pass.trip_id WHERE pass.user_id = :userId 
                AND (trip.date_of_departure BETWEEN :departureDate and :arrivalDate
                OR trip.latest_arrival_date BETWEEN :departureDate and :arrivalDate);');
            $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('departureDate', $departureDate);
            $stmt->bindParam('arrivalDate', $arrivalDate);
            $stmt->execute();

            $count = $stmt->rowCount();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        if ($count >= 1) {
            return false;
        }
        else {
            return true;
        }
    }



    public function cancelTrip($tripId) {
        try {

            $stmt = $this->db->prepare('delete from CPL_trip where trip_id = :tripId');
            $stmt->bindParam('tripId', $tripId);
            $stmt->execute();



        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getAllQuestions()
    {
        $questions = [];

        try {
            $stmt = $this->db->prepare('SELECT * FROM CPL_questions');
            $stmt->execute();
            while ($question = $stmt->fetchObject('Questions')) {
                $questions[] = $question;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $questions;
    }

    public function createQuestion(Questions $questions)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_questions` (`question_id`, `question`, `answer`)
					VALUES (NULL, :question, :answer) ");

            $question = $questions->getQuestion();
            $answer = $questions->getAnswer();

            $stmt->bindParam(':question', $question, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);

            $stmt->execute();

            $question_id = $this->db->lastInsertId();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $question_id;
    }

    public function updateQuestion(Questions $question)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_questions` SET question=:question, answer=:answer WHERE question_id=:question_id");

            $question_id = $question->getQuestionId();
            $answer = $question->getAnswer();
            $question = $question->getQuestion();


            $stmt->bindParam(':question', $question, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function deleteQuestion($question_id)
    {
        try {
            $stmt = $this->db->prepare('delete from `CPL_questions` where question_id = :question_id');

            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }


    public function getPermissionsForUserType($usetypeId)
    {
        $permissions = [];

        try {
            $stmt = $this->db->prepare('select usertype_perm.*, perm.permission_name
from `CPL_usertype_permission` as usertype_perm
left join `CPL_permission` as perm
on usertype_perm.permission_id = perm.permission_id
where usertype_perm.type_id = :usetypeId');
            $stmt->bindParam(':usetypeId', $usetypeId, PDO::PARAM_INT);
            $stmt->execute();
            while ($permission = $stmt->fetchObject('Permissions')) {
                $permissions[] = $permission;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $permissions;
    }

    public function setIsCheckedPerm($type_id, $permission_id, $check)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_usertype_permission` SET isChecked=:isChecked WHERE type_id=:type_id AND permission_id=:permission_id");
            $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
            $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
            $stmt->bindParam(':isChecked', $check, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function getUsertypeName($type_id)
    {
        try {

            $stmt = $this->db->prepare("SELECT type FROM `CPL_user_type` WHERE type_id = :type_id");
            $stmt->bindParam(':type_id', $type_id, PDO::PARAM_STR);
            $stmt->execute();

            $types = $stmt->fetch();
            $type = $types[0];

            return $type;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getAllTypesNames()
    {
        $types = [];

        try {
            $stmt = $this->db->prepare('SELECT * FROM CPL_user_type');
            $stmt->execute();
            while ($type = $stmt->fetchObject('UserType')) {
                $types[] = $type;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $types;
    }

    public function createUserType(UserType $newUserType)
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `CPL_user_type` (`type_id`, `type`)
					VALUES (NULL, :type) ");

            $type = $newUserType->getType();

            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->execute();

            $type_id = $this->db->lastInsertId();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $type_id;
    }


    public function createUserTypePermissions($type_id, $permission_id)
    {

        try {

            $stmt = $this->db->prepare("INSERT INTO `CPL_usertype_permission` (`type_id`, `permission_id`)
                                        VALUES (:type_id, (SELECT permission_id FROM `CPL_permission` WHERE permission_id=:permission_id))");

            $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);
            $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);

            $stmt->execute();


        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }

    public function selectMaxPermId()
    {
        try {
            $stmt = $this->db->prepare("SELECT MAX(permission_id) FROM `CPL_permission`");
            $stmt->execute();

            $maxPerm = $stmt->fetch();
            $maximum = $maxPerm[0];

            return $maximum;

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function deleteUser($userId){
        try {
            $stmt = $this->db->prepare("DELETE FROM CPL_users WHERE user_id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function getNotificationsForUser($user_id)
    {
        $notifications = [];

        try {
            $stmt = $this->db->prepare("select usernotifications.notification_id, usernotifications.isChecked, notifications.notification_name
from CPL_users_notifications as usernotifications
left join CPL_notifications as notifications
on usernotifications.notification_id = notifications.notification_id
where usernotifications.user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            while ($notification = $stmt->fetchObject('Notifications')) {
                $notifications[] = $notification;
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }


        return  $notifications;
    }

    public function setIsCheckedAlert($user_id, $notification_id, $check)
    {

        try {

            $stmt = $this->db->prepare("UPDATE `CPL_users_notifications` SET isChecked=:isChecked WHERE user_id=:user_id AND notification_id=:notification_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
            $stmt->bindParam(':isChecked', $check, PDO::PARAM_INT);
            $stmt->execute();

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return true;
    }
}
