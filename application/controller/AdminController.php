<?php

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
        $this->View->render('admin/index', array(
                'users' => UserModel::getPublicProfilesOfAllUsers())
        );
    }
    public function DevIndex()
    {
    $this->View->render('admin/index', array(
            'users' => UserModel::getPublicProfilesOfAllUsers())
    );
    }
    public function actionAccountSettings()
    {
        if (Request::post('suspension') == null && Request::post('softDelete') == 'off')
        {
        AdminModel::setAccountSuspensionAndDeletionStatus(
            Request::post('suspension'), Request::post('softDelete'), Request::post('user_id')
        );
      }

        AdminModel::UpdateMakerspaceThings(
          Request::post('tool'),Request::post('active'),Request::post('user_id')
        );
       Redirect::to("admin/DevIndex");
    }

  public function CreateSubscription()
  {


  //  UserModel::CreateSubscription(Request::post("name"),Request::post("price"),Request::post("description"),Request::post("days"),Request::post("access"),Request::post("billing"));

    // Create a plan for the yearly billing
    UserModel::CreateSubscription(Request::post("name"),Request::post("priceM"),Request::post("description"),Request::post("days"),Request::post("access"),Request::post("priceY"),Request::post("extra"),Request::post("guest"),Request::post("storage"),Request::post("rental"),
    Request::post("machine"),Request::post("priority"));
    // Create one for the monthly Billing

    //var_dump(Request::post("access"));
    Redirect::to("admin/Subscriptions");

  }
  public function Subscriptions()
  {
    $this->View->render('admin/subscription', array(
            'subscriptions' => UserModel::GetAllSubcriptions())
    );
  }
  public function DisableSub(){

       UserModel::DisableSub(Request::post("id"),Request::post("disable"));
      var_dump(Request::post("disable"));
      Redirect::to("admin/Subscriptions");
  }
  public function DeleteSub()
  {
    UserModel::DeleteSubscription(Request::post("id"));

   Redirect::to("admin/Subscriptions");
  }
  public function Tools()
  {
    $this->View->render('admin/tools', array(
            'tools' => ToolModel::GetAllTools())
    );
  }

}
