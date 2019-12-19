<?php

/**
 * UserModel
 * Handles all the PUBLIC profile stuff. This is not for getting data of the logged in user, it's more for handling
 * data of all the other users. Useful for display profile information, creating user lists etc.
 */
class UserModel
{
    /**
     * Gets an array that contains all the users in the database. The array's keys are the user ids.
     * Each array element is an object, containing a specific user's data.
     * The avatar line is built using Ternary Operators, have a look here for more:
     * @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
     *
     * @return array The profiles of all users
     */
    public static function getPublicProfilesOfAllUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_active, user_has_avatar, user_deleted, ActiveSub, ToolQualif FROM users";
        $query = $database->prepare($sql);
        $query->execute();

        $all_users_profiles = array();

        foreach ($query->fetchAll() as $user) {

            // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
            // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
            // the user's values
            array_walk_recursive($user, 'Filter::XSSFilter');

            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id = $user->user_id;
            $all_users_profiles[$user->user_id]->user_name = $user->user_name;
            $all_users_profiles[$user->user_id]->user_email = $user->user_email;
            $all_users_profiles[$user->user_id]->user_active = $user->user_active;
            $all_users_profiles[$user->user_id]->user_deleted = $user->user_deleted;
            $all_users_profiles[$user->user_id]->ActiveSub = $user->ActiveSub;
            $all_users_profiles[$user->user_id]->ToolQualif = $user->ToolQualif;
            $all_users_profiles[$user->user_id]->user_avatar_link = (Config::get('USE_GRAVATAR') ? AvatarModel::getGravatarLinkByEmail($user->user_email) : AvatarModel::getPublicAvatarFilePathOfUser($user->user_has_avatar, $user->user_id));
        }

        return $all_users_profiles;
    }

    /**
     * Gets a user's profile data, according to the given $user_id
     * @param int $user_id The user's id
     * @return mixed The selected user's profile
     */
    public static function getPublicProfileOfUser($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_active, user_has_avatar, user_deleted
                FROM users WHERE user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $user = $query->fetch();

        if ($query->rowCount() == 1) {
            if (Config::get('USE_GRAVATAR')) {
                $user->user_avatar_link = AvatarModel::getGravatarLinkByEmail($user->user_email);
            } else {
                $user->user_avatar_link = AvatarModel::getPublicAvatarFilePathOfUser($user->user_has_avatar, $user->user_id);
            }
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_DOES_NOT_EXIST'));
        }

        // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
        // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
        // the user's values
        array_walk_recursive($user, 'Filter::XSSFilter');

        return $user;
    }

    /**
     * @param $user_name_or_email
     *
     * @return mixed
     */
    public static function getUserDataByUserNameOrEmail($user_name_or_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id, user_name, user_email FROM users
                                     WHERE (user_name = :user_name_or_email OR user_email = :user_name_or_email)
                                           AND user_provider_type = :provider_type LIMIT 1");
        $query->execute(array(':user_name_or_email' => $user_name_or_email, ':provider_type' => 'DEFAULT'));

        return $query->fetch();
    }

    /**
     * Checks if a username is already taken
     *
     * @param $user_name string username
     *
     * @return bool
     */
    public static function doesUsernameAlreadyExist($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_name = :user_name LIMIT 1");
        $query->execute(array(':user_name' => $user_name));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Checks if a email is already used
     *
     * @param $user_email string email
     *
     * @return bool
     */
    public static function doesEmailAlreadyExist($user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("SELECT user_id FROM users WHERE user_email = :user_email LIMIT 1");
        $query->execute(array(':user_email' => $user_email));
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Writes new username to database
     *
     * @param $user_id int user id
     * @param $new_user_name string new username
     *
     * @return bool
     */
    public static function saveNewUserName($user_id, $new_user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_name = :user_name WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_name' => $new_user_name, ':user_id' => $user_id));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }
    public static function SetStripeID($user_id, $StripeID)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET StripeID = :StripeID WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':StripeID' => $StripeID, ':user_id' => $user_id));
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    /**
     * Writes new email address to database
     *
     * @param $user_id int user id
     * @param $new_user_email string new email address
     *
     * @return bool
     */
    public static function saveNewEmailAddress($user_id, $new_user_email)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_email = :user_email WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(':user_email' => $new_user_email, ':user_id' => $user_id));
        $count = $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }

    /**
     * Edit the user's name, provided in the editing form
     *
     * @param $new_user_name string The new username
     *
     * @return bool success status
     */
    public static function editUserName($new_user_name)
    {
        // new username same as old one ?
        if ($new_user_name == Session::get('user_name')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_SAME_AS_OLD_ONE'));
            return false;
        }

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if (!preg_match("/^[a-zA-Z0-9]{2,64}$/", $new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr(strip_tags($new_user_name), 0, 64);

        // check if new username already exists
        if (self::doesUsernameAlreadyExist($new_user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            return false;
        }

        $status_of_action = self::saveNewUserName(Session::get('user_id'), $new_user_name);
        if ($status_of_action) {
            Session::set('user_name', $new_user_name);
            Session::add('feedback_positive', Text::get('FEEDBACK_USERNAME_CHANGE_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }
    }

    /**
     * Edit the user's email
     *
     * @param $new_user_email
     *
     * @return bool success status
     */
    public static function editUserEmail($new_user_email)
    {
        // email provided ?
        if (empty($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        // check if new email is same like the old one
        if ($new_user_email == Session::get('user_email')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_SAME_AS_OLD_ONE'));
            return false;
        }

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($new_user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        // strip tags, just to be sure
        $new_user_email = substr(strip_tags($new_user_email), 0, 254);

        // check if user's email already exists
        if (self::doesEmailAlreadyExist($new_user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            return false;
        }

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if (self::saveNewEmailAddress(Session::get('user_id'), $new_user_email)) {
            Session::set('user_email', $new_user_email);
            Session::set('user_gravatar_image_url', AvatarModel::getGravatarLinkByEmail($new_user_email));
            Session::add('feedback_positive', Text::get('FEEDBACK_EMAIL_CHANGE_SUCCESSFUL'));
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
        return false;
    }

    /**
     * Gets the user's id
     *
     * @param $user_name
     *
     * @return mixed
     */
    public static function getUserIdByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id FROM users WHERE user_name = :user_name AND user_provider_type = :provider_type LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch()->user_id;
    }

    /**
     * Gets the user's data
     *
     * @param $user_name string User's name
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserDataByUsername($user_name)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, user_name, user_email, user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type,
                       user_failed_logins, user_last_failed_login,StripeID
                  FROM users
                 WHERE (user_name = :user_name OR user_email = :user_name)
                       AND user_provider_type = :provider_type
                 LIMIT 1";
        $query = $database->prepare($sql);

        // DEFAULT is the marker for "normal" accounts (that have a password etc.)
        // There are other types of accounts that don't have passwords etc. (FACEBOOK)
        $query->execute(array(':user_name' => $user_name, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }

    /**
     * Gets the user's data by user's id and a token (used by login-via-cookie process)
     *
     * @param $user_id
     * @param $token
     *
     * @return mixed Returns false if user does not exist, returns object with user's data when user exists
     */
    public static function getUserDataByUserIdAndToken($user_id, $token)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // get real token from database (and all other data)
        $query = $database->prepare("SELECT user_id, user_name, user_email, user_password_hash, user_active,
                                          user_account_type,  user_has_avatar, user_failed_logins, user_last_failed_login
                                     FROM users
                                     WHERE user_id = :user_id
                                       AND user_remember_me_token = :user_remember_me_token
                                       AND user_remember_me_token IS NOT NULL
                                       AND user_provider_type = :provider_type LIMIT 1");
        $query->execute(array(':user_id' => $user_id, ':user_remember_me_token' => $token, ':provider_type' => 'DEFAULT'));

        // return one row (we only have one result or nothing)
        return $query->fetch();
    }

    public static function getUserStripeID($user_id)
    {
      $database = DatabaseFactory::getFactory()->getConnection();

      $sql = "SELECT user_id, user_name, user_email, user_password_hash, user_active,user_deleted, user_suspension_timestamp, user_account_type,
                     user_failed_logins, user_last_failed_login,StripeID
                FROM users
               WHERE (user_id = :user_id )

               LIMIT 1";
      $query = $database->prepare($sql);

      // DEFAULT is the marker for "normal" accounts (that have a password etc.)
      // There are other types of accounts that don't have passwords etc. (FACEBOOK)
      $query->execute(array(':user_id' => $user_id));

      // return one row (we only have one result or nothing)
      return $query->fetch()->StripeID;
    }


    //charge a Customer
    public static function chargeUser($user_id,$value,$desc) // Charge A user for a fee that has been incured such as checkout with a cart
    {
      \Stripe\Stripe::setApiKey( Config::get('STRIPE_PRIVATE_KEY'));
      $StripeID = self::getUserStripeID($user_id);
      $charge = \Stripe\Charge::create([
        'amount' => (int)$value * 100,
        'currency' => 'usd',
        'customer' => $StripeID,
        'description' => $desc
      ]);

      $Result = self::RecordCharge($user_id,$value,$charge->id,$desc);
      if ($charge->failure_code != null)
      {
        $Result = 'false';
      }
      if ($Result == true){
        Session::add('feedback_positive',Text::get('FEEDBACK_ACCOUNT_CHARGED') . $value);
      }else{
        Session::add('feedback_negitive',Text::get('FEEDBACK_CHARGE_FAILED') . $charge->failure_code . " Fail Message " . $charge->failure_message);

      }
    }

    public static function RecordCharge($user_id,$value,$chargeID,$desc) // Record A charge to the database
    {
      $database = DatabaseFactory::getFactory()->getConnection();

      $query = $database->prepare("INSERT INTO `charges`( `user_id`, `value`, `stripe_charge`, `description`) VALUES (:user_id,:value,:chargeID, :descript) LIMIT 1");
      $query->execute(array(':chargeID' => $chargeID, ':user_id' => $user_id,':value' => $value,':descript'=> $desc));
      if ($query->rowCount() == 1) {
          return true;
      }
      return false;
    }

    public static function DeleteCustomer($customer_id) // Remove A customer from the stripe database
    {
        \Stripe\Stripe::setApiKey( Config::get('STRIPE_PRIVATE_KEY'));
        $customer = \Stripe\Customer::retrieve(
          $customer_id
        );
        $customer->delete();
        if($customer->deleted == 'true')
        {
          Session::add('feedback_positive',Text::get('FEEDBACK_CUSTOMER_DELETED'));
        }else{

            Session::add('feedback_negitive',Text::get('FEEDBACK_CUSTOMER_DELETED_FAIL'));
        }
    }

    public static function CreateSubscription($name,$priceM,$description,$days,$HourAcess,$priceY,$extra_day,$guest_pass,$storage,$rental_discount,$machine_discount,$priority_bookings) // Create A subscription
    {
      if ($HourAcess == null)
      {
        $HourAcess = 0;
      }

      if ($priority_bookings == null)
      {
        $priority_bookings = 0;
      }

      \Stripe\Stripe::setApiKey( Config::get('STRIPE_PRIVATE_KEY'));
      $ResultM = \Stripe\Plan::create([
        'amount' => (int)$priceM * 100,
        'currency' => 'usd',
        'interval' => "month", //Specifies billing frequency. Either day, week, month or year.
        'product' => ['name' => $name],
        "trial_period_days" => "0"

      ]);
      \Stripe\Stripe::setApiKey( Config::get('STRIPE_PRIVATE_KEY'));
      $ResultY = \Stripe\Plan::create([
        'amount' => (int)$priceY * 100,
        'currency' => 'usd',
        'interval' => "year", //Specifies billing frequency. Either day, week, month or year.
        'product' => ['name' => $name],
        "trial_period_days" => "0"

      ]);

      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("INSERT INTO `subscriptions`( `name`, `priceM`, `description`, `days`, `24Hour`, `priceY`,`stripe_id_M`, `stripe_id_Y`, `extra_day`, `guest_pass`, `storage_discount`,
       `rental_discount`, `machine_discount`, `priority_bookings`) VALUES (:name,:priceM,:description,:days,:HourAcess,:priceY,:stripe_id_M,:stripe_id_Y,:extra_day, :guest_pass, :storage_discount,
       :rental_discount, :machine_discount , :priority_bookings)");
      $query->execute(array(
              ':name' => $name,
              ':priceM' => $priceM,
              ':description' => $description,
              ':days' => $days,
              ':HourAcess' => $HourAcess,
              ':priceY' => $priceY,
              "stripe_id_M" =>$ResultM->id,
              "stripe_id_Y" =>$ResultM->id,
              ":extra_day" =>$extra_day,
              ":guest_pass" =>$guest_pass,
              ":storage_discount" =>$storage,
              ":rental_discount" =>$rental_discount,
              ":machine_discount" =>$machine_discount,
              ":priority_bookings" =>$priority_bookings
      ));
      if ($query->rowCount() == 1) {
          Session::add('feedback_positive',Text::get('FEEDBACK_PLAN_CREATED'));
      }else {
          Session::add('feedback_negitive',Text::get('FEEDBACK_PLAN_CREATION_ERROR'));
      }


    }
    public static function GetAllSubcriptions() // Get subcriptions including the disabled for the admin pannel
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("SELECT * FROM `subscriptions` WHERE visible = 1");
      $query->execute();

      $subscriptions = array();
      $hour = "24Hour";
      foreach ($query->fetchAll() as $subcription) {

          // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
          // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
          // the user's values
          //array_walk_recursive($subcription, 'Filter::XSSFilter');

          $subscriptions[$subcription->subscription_id] = new stdClass();
          $subscriptions[$subcription->subscription_id]->subscription_id = $subcription->subscription_id;
          $subscriptions[$subcription->subscription_id]->name = $subcription->name;
          $subscriptions[$subcription->subscription_id]->priceM = $subcription->priceM;
          $subscriptions[$subcription->subscription_id]->priceY = $subcription->priceY;
          $subscriptions[$subcription->subscription_id]->description = $subcription->description;
          $subscriptions[$subcription->subscription_id]->days = $subcription->days;
          $subscriptions[$subcription->subscription_id]->$hour = $subcription->$hour;
          $subscriptions[$subcription->subscription_id]->stripe_id_M = $subcription->stripe_id_M;
          $subscriptions[$subcription->subscription_id]->stripe_id_Y = $subcription->stripe_id_Y;
          $subscriptions[$subcription->subscription_id]->enabled = $subcription->enabled;
          $subscriptions[$subcription->subscription_id]->extra_day = $subcription->extra_day;
          $subscriptions[$subcription->subscription_id]->guest_pass = $subcription->guest_pass;
          $subscriptions[$subcription->subscription_id]->storage_discount = $subcription->storage_discount;
          $subscriptions[$subcription->subscription_id]->rental_discount = $subcription->rental_discount;
          $subscriptions[$subcription->subscription_id]->machine_discount = $subcription->machine_discount;
          $subscriptions[$subcription->subscription_id]->priority_bookings = $subcription->priority_bookings;
      }

      return $subscriptions;

    }

    public static function CheckSubscription($user_id) // Cehck if the user is subscribed to a plan
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // get real token from database (and all other data)
        $query = $database->prepare("SELECT ActiveSub
                                     FROM users
                                     WHERE user_id = :user_id
                                       AND user_suspension_timestamp IS NULL LIMIT 1");
        $query->execute(array(':user_id' => $user_id ));

        // return one row (we only have one result or nothing)
        if($query->fetch()->ActiveSub == true){
          return true;
        }else{
          return false;
        }
    }
    public static function DeleteSubscription($sub_id)
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("UPDATE  `subscriptions` SET
                                   `visible`= '0'  WHERE `subscription_id` = :sub_id");
      $query->execute(array(':sub_id' => $sub_id ));
    }

    public static function TestTool($user_id) // Get A users tool Qualifacitons
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("SELECT ToolQualif
                                   FROM users
                                   WHERE user_id = :user_id
                                     AND user_suspension_timestamp IS NULL LIMIT 1");
      $query->execute(array(':user_id' => $user_id ));
      return $query->fetch()->ToolQualif;
    }

    public static function SetToolQualifaction($user_id,$value) // Set a users tool Qualifacitons
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("UPDATE users SET ToolQualif = :value

                                   WHERE user_id = :user_id
                                     AND user_suspension_timestamp IS NULL LIMIT 1");
      $query->execute(array(':user_id' => $user_id, ':value' => $value ));
      $count = $query->rowCount();
      if ($count == 1) {
          return true;
      }
      return false;

    }
    public static function DisableSub($sub_id,$value) // Disable a Subscription
    {
      if ($value == null){
        $value = 1;
      }
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("UPDATE subscriptions SET enabled = :value

                                   WHERE subscription_id = :subscription_id LIMIT 1");
      $query->execute(array(':subscription_id' => $sub_id, ':value' => $value ));
      $count = $query->rowCount();
      if ($count == 1) {

        Session::add('feedback_positive',Text::get('FEEDBACK_SUBSCRIPTION_DISABLED'));
          return true;
      }
      return false;

    }
    public static function GetPublicSubscriptions() // Get All subscriptions that are enabled
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("SELECT * FROM `subscriptions` WHERE enabled = 1 AND visible = 1");
      $query->execute();

      $subscriptions = array();
      $hour = "24Hour";
      foreach ($query->fetchAll() as $subcription) {

          // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
          // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
          // the user's values
          //array_walk_recursive($subcription, 'Filter::XSSFilter');

          $subscriptions[$subcription->subscription_id] = new stdClass();
          $subscriptions[$subcription->subscription_id]->subscription_id = $subcription->subscription_id;
          $subscriptions[$subcription->subscription_id]->name = $subcription->name;
          $subscriptions[$subcription->subscription_id]->priceM = $subcription->priceM;
          $subscriptions[$subcription->subscription_id]->priceY = $subcription->priceY;
          $subscriptions[$subcription->subscription_id]->description = $subcription->description;
          $subscriptions[$subcription->subscription_id]->days = $subcription->days;
          $subscriptions[$subcription->subscription_id]->$hour = $subcription->$hour;
          $subscriptions[$subcription->subscription_id]->stripe_id_M = $subcription->stripe_id_M;
          $subscriptions[$subcription->subscription_id]->stripe_id_Y = $subcription->stripe_id_Y;
          $subscriptions[$subcription->subscription_id]->enabled = $subcription->enabled;
          $subscriptions[$subcription->subscription_id]->extra_day = $subcription->extra_day; // Value
          $subscriptions[$subcription->subscription_id]->guest_pass = $subcription->guest_pass;// Value
          $subscriptions[$subcription->subscription_id]->storage_discount = $subcription->storage_discount;// Precent
          $subscriptions[$subcription->subscription_id]->rental_discount = $subcription->rental_discount;// Precent
          $subscriptions[$subcription->subscription_id]->machine_discount = $subcription->machine_discount;// Precent
          $subscriptions[$subcription->subscription_id]->priority_bookings = $subcription->priority_bookings; // Check Box
      }

      return $subscriptions;
    }
}
