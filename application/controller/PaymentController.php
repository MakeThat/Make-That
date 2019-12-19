<?php

class PaymentController extends Controller
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
        $this->View->renderNoHeader('payment/index');
    }
    public function script()
    {
      $this->View->RenderScript('payment/script.js');
    }


    public function CreateCustomer()
    {
      $this->View->render('payment/cool');

    }
    public function cool()
    {
      $this->View->render('payment/cool');

    }
    public function ChargeUser($amount)
    {
      $desc = "This is a test charge";
      UserModel::chargeUser(Session::get('user_id'),$amount,$desc);
        Redirect::home();
    }

    public function Create()
    {
      \Stripe\Stripe::setApiKey( Config::get('STRIPE_PRIVATE_KEY'));
  //    $token = $_POST['stripeToken'];
      $token = Request::post("stripeToken");
      $address1 = Request::post("address1");
      $address2 = Request::post("address2");
      $city = Request::post("city");
      $country = Request::post("country");
      $zip = Request::post("Zipcode");
      $state = Request::post("State");
      $name = Request::post("full-name");
      $UserData = UserModel::getUserDataByUserNameOrEmail(Session::get('user_name'));
      $email = $UserData->user_email;
      $user_name = $UserData->user_name;


        $Customer = \Stripe\Customer::create([
        'description' => 'Make That PDX Customer' . $user_name,
        'source' => $token,
        'address.line1' => $address1,
        'address.line2' => $address2,
        //'address.country' => "us",
        'address.postal_code' => $zip,
        'address.state' => $state,
        'name' => $name,
        'email' => $email,
        'tax_exempt' => "exempt"

      ]);
      UserModel::SetStripeID(Session::get('user_id'), $Customer->id);
      Redirect::home();
    }
}
