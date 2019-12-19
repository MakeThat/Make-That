<?php

class RFIDController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function index($user_id)
    {
      echo(UserModel::CheckSubscription($user_id));
    }
    public function tool($user_id)
    {
      echo(UserModel::TestTool($user_id));
    }

}
