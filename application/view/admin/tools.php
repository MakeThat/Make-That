<div class="container">
    <h1> Tool Creation </h1>

    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Tools</h3>

        <div>
          This shows you all of the created subscriptions that have been created by the admins And How to create a new one
        </div>
        <div>

          <form action="<?= config::get("URL"); ?>tool/create" method="post" id = "subscription">
            <b>Tool Name: </b> <input type="text" name="name" placeholder="Really Cool Name" maxlength="100" required><br>
            <textarea name="description" form="subscription" rows="4" cols="120" maxlength="1000" required>Enter text here...</textarea>
            <b> Unit Of time:  <input type="text" name="time" placeholder="hour"required /> </b>
            <b> Price (Per Unit of time): <input type="number" min="0.00" max="10000.00" step="0.01" name ="price" placeholder="50" required /><br></b>
            <b> Reservable: <input type="checkbox" name="reserve" value="1"> </b><br>
            Tool Type:
            <select name="type">
                <option value="Metal">Metal Working</option>
                <option value="Wood">Wood Working</option>
                <option value="plastic">Plastic</option>
                <option value="electronics">Electronics</option>
              </select><br>
          <b>  Materials: <input type="text" name="materials" placeholder="Wood And Plastic"required /> </b><br>
          <b>  Saftey: <input type="number" min="0" max="10000"  name ="saftey" placeholder="350" required /><br></b>
          <b>  Process: <input type="text" name="process" placeholder="Welding"required /> </b>

             <input type="submit" value="Submit">
           </form>
          </div>


        <div>

          <?php $hour = "24Hour"; foreach ($this->tools as $tools) { ?>
            <div class="box">
          <!--  <?php var_dump($tools) ?> -->
              <h3>Name: <?= $tools->name; ?></h3>

            <p> <b> Description</b>  </p><?= $tools->description; ?>

              <br>
              Reservable: <input type="checkbox" name="softDelete" <?php if ($tools->reservable == 1) { ?> checked <?php } ?> disabled/>
              <br>
              <p> Price: $<?= $tools->price; ?> per <?= $tools->time; ?>  </p>
              <br>

                <form action="<?= config::get("URL"); ?>tool/Disable" method="post">
                  Disabled: <input type="checkbox" name="disable" value="0" <?php if ($tools->enabled == 0) { ?> checked <?php } ?> />
                  <input type="hidden" value="<?= $tools->tool_id; ?>" name ="id">
                  <input type="submit" value="Submit">
                </form>
                <form action="<?= config::get("URL"); ?>tool/Delete" method="post">

                  <input type="hidden" value="<?= $tools->tool_id; ?>" name ="id">
                  <input type="submit" value="Delete Tool">
                </form>
            </div>
          <?php } ?>
    </div>
</div>
