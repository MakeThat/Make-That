<?php

class ToolController extends Controller
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
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     *
          */

     // NO SECURITY CHECKS Needed BEcause of the fact that it is using admin panel also the Request::post() Function Handels Security
    public function create()
    {
    //  ToolModel::CreateTool($name,$price,$description,$time,$reserve,$type,$materials,$process,$saftey);
      ToolModel::CreateTool(Request::post("name"),Request::post("price"),Request::post("description"),Request::post("time"),Request::post("reserve"),Request::post("type"),Request::post("materials")
      ,Request::post("process"),Request::post("saftey"));
      Redirect::to("admin/tools");
    }
    public function update()
    {
  //  ToolModel::UpdateTool($tool_id,$name,$price,$time,$reserve,$type,$materials,$process,$saftey);
  ToolModel::CreateTool(Request::post("tool_id"),Request::post("name"),Request::post("price"),Request::post("description"),Request::post("time"),Request::post("reserve"),Request::post("type"),Request::post("materials")
  ,Request::post("process"),Request::post("saftey"));
    }
    public function Delete()
    {

      ToolModel::DeleteTool(Request::post("id"));
    Redirect::to("admin/tools");
    }
    public function Disable()
    {
      ToolModel::DisableTool(Request::post("id"),Request::post("disable"));
      Redirect::to("admin/tools");
    }

}
