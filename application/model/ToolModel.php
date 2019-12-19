<?php

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class ToolModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
  *
  *  public static function getAllNotes()
  *  {
  *      $database = DatabaseFactory::getFactory()->getConnection();

  *      $sql = "SELECT user_id, note_id, note_text FROM notes WHERE user_id = :user_id";
  *      $query = $database->prepare($sql);
  *      $query->execute(array(':user_id' => Session::get('user_id')));

    *    // fetchAll() is the PDO method that gets all result rows
  *      return $query->fetchAll();
*    }
    */
    // Function to add a tool to the database
    public static function CreateTool($name,$price,$description,$time,$reserve,$type,$materials,$process,$saftey)
    {

      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("INSERT INTO `tools`( `name`, `price`, `time`, `reservable`, `type`, `materials`, `process`, `saftey`,`description`)
       VALUES (:name ,:price , :time, :reservable , :type , :materials , :process , :saftey, :description)");
      $query->execute(array(
              ':name' => $name,
              ':price' => $price,
              ':time' => $time,
              ':reservable' => $reserve,
              ':materials' => $materials,
              ':process' => $process,
              ":saftey" =>$saftey,
              ":description" =>$description,
              ":type" => $type
      ));

      if ($query->rowCount() == 1) {
          Session::add('feedback_positive',Text::get('FEEDBACK_TOOL_ADDED'));
      }else {
          Session::add('feedback_negitive',Text::get('FEEDBACK_TOOL_FAILED'));
      }

    }
    public static function UpdateTool($tool_id,$name,$price,$description,$time,$reserve,$type,$materials,$process,$saftey)
    {
      $database = DatabaseFactory::getFactory()->getConnection();
      $query = $database->prepare("UPDATE `tools` SET `name`=:name,`description`=:description,`price`=:price,`time`=:time,`reservable`=:reservable,`type`=:type
        ,`materials`=:materials,`process`=:process,`saftey`=:saftey WHERE tool_id =:tool_id");
      $query->execute(array(
              ':name' => $name,
              ':price' => $price,
              ':time' => $time,
              ':reservable' => $reserve,
              ':materials' => $materials,
              ':process' => $process,
              ":saftey" =>$saftey,
              ":description" =>$description,
              ":type" => $type,
              ":tool_id" => $tool_id
      ));

      if ($query->rowCount() == 1) {
          Session::add('feedback_positive',Text::get('FEEDBACK_TOOL_ADDED'));
      }else {
          Session::add('feedback_negitive',Text::get('FEEDBACK_TOOL_FAILED'));
      }
    }
    public static function DisableTool($tool_id,$value)
    {

        if(isset($value) == false ){
          $value = 1 ;
        }

        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE `tools` SET enabled = :value WHERE tool_id =:tool_id ");
        $query->execute(array(

                ":tool_id" => $tool_id,
                ":value"=> $value
        ));
        if ($query->rowCount() == 1) {
            Session::add('FEEDBACK_TOOL_DISABLED',Text::get('FEEDBACK_TOOL_ADDED'));
        }else {
            Session::add('feedback_negitive',Text::get('FEEDBACK_TOOL_ERROR'));
        }
    }

    public static function DeleteTool($tool_id)
    {

        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE `tools` SET deleted = 1 WHERE tool_id =:tool_id");
        $query->execute(array(

                ":tool_id" => $tool_id
        ));
        if ($query->rowCount() == 1) {
            Session::add('FEEDBACK_TOOL_DISABLED',Text::get('FEEDBACK_TOOL_DELETED'));
        }else {
            Session::add('feedback_negitive',Text::get('FEEDBACK_TOOL_ERROR'));
        }
    }
    public static function SetStatus($tool_id,$status)
    {


        $database = DatabaseFactory::getFactory()->getConnection();
        $query = $database->prepare("UPDATE `tools` SET status = :status WHERE tool_id =:tool_id");
        $query->execute(array(
                ":status" => $status,
                ":tool_id" => $tool_id
        ));
    }

    public static function GetAllTools()
    {
      $database = DatabaseFactory::getFactory()->getConnection();

      $sql = "SELECT * FROM tools WHERE NOT deleted = 1";
      $query = $database->prepare($sql);
      $query->execute();

      $AllTools = array();

      foreach ($query->fetchAll() as $tools) {

          // We create the STD clas above the Filter becasuse the discription if filtered
          // does not function as it should.
          $AllTools[$tools->tool_id] = new stdClass();
          $AllTools[$tools->tool_id]->description = $tools->description;
          array_walk_recursive($tools, 'Filter::XSSFilter');



          $AllTools[$tools->tool_id]->tool_id = $tools->tool_id;
          $AllTools[$tools->tool_id]->name = $tools->name;
          $AllTools[$tools->tool_id]->price = $tools->price;
          $AllTools[$tools->tool_id]->time = $tools->time;
          $AllTools[$tools->tool_id]->reservable = $tools->reservable;
          $AllTools[$tools->tool_id]->type = $tools->type;
          $AllTools[$tools->tool_id]->materials = $tools->materials;
          $AllTools[$tools->tool_id]->process = $tools->process;
          $AllTools[$tools->tool_id]->saftey = $tools->saftey;
          $AllTools[$tools->tool_id]->status = $tools->status;
          $AllTools[$tools->tool_id]->enabled = $tools->enabled;


        }

      return $AllTools;



    }
    public static function GetAllPublicTools()
    {
      $database = DatabaseFactory::getFactory()->getConnection();

      $sql = "SELECT * FROM tools WHERE deleted NOT 1 AND enabled = 1";
      $query = $database->prepare($sql);
      $query->execute();

      $AllTools = array();

      foreach ($query->fetchAll() as $tools) {

          // all elements of array passed to Filter::XSSFilter for XSS sanitation, have a look into
          // application/core/Filter.php for more info on how to use. Removes (possibly bad) JavaScript etc from
          // the user's values
          array_walk_recursive($tools, 'Filter::XSSFilter');

          $AllTools[$user->user_id] = new stdClass();
          $AllTools[$tools->tool_id]->tool_id = $tools->tools_id;
          $AllTools[$tools->tool_id]->name = $tools->name;
          $AllTools[$tools->tool_id]->price = $tools->price;
          $AllTools[$tools->tool_id]->time = $tools->time;
          $AllTools[$tools->tool_id]->reservable = $tools->reservable;
          $AllTools[$tools->tool_id]->type = $tools->type;
          $AllTools[$tools->tool_id]->materials = $tools->materials;
          $AllTools[$tools->tool_id]->process = $tools->process;
          $AllTools[$tools->tool_id]->saftey = $tools->saftey;
          $AllTools[$tools->tool_id]->status = $tools->status;



        }

      return $AllTools;

    }


}
