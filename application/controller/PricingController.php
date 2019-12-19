<?php

class PricingController extends Controller
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
    public function index()
    {
        $this->View->render('pricing/index',array(
                'subscriptions' => UserModel::GetPublicSubscriptions())
            );
    }
    //      $token = Request::post("stripeToken");

    public function checkout()
    {
      Request::post("id");

    }
    public function create()
    {
      $days = (int)Request::post("weekdays");// Pull In data

      $Allways = (int)Request::post("247");
      $montly = (int)Request::post("billing");

      $pricing = 1.0;
      if ($montly != 1)
      {
        $pricing = .9;
      }
      $Access = 1.0;
      if ($Allways == 1){
        $Access = 1.5;
      }
      $WeekdayDiscount = $days * .03;
      if ($WeekdayDiscount > .25)
      {
        $WeekdayDiscount = .25;
      }
      $weekdayPrice = (1 - $WeekdayDiscount) * 8 * $days; // calcuate the prices based off stuff

      $Total =  $weekdayPrice * (int)$Access * $pricing;
      if ($days == 8 ) // Check For Weekend Warrior
      {
        redirect::to("pricing/index#memberships");
      }
      if ($days == 11 ) // Check For Hobbiest Plan
      {
        redirect::to("pricing/index#memberships");
      }
      if ($days == 14) // Check For Maker Plan
      {
        redirect::to("pricing/index#memberships");
      }
      if ((int)$Total >= 130)
      {
        redirect::to("pricing/index#memberships");
      }
      echo $Total;
    }
}
