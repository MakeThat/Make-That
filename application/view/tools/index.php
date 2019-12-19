<div class="container">
    <h1> Make That PDX's Tools </h1>

    

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div>
        All of the tools that we have currently in our shop.
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

                <form action="<?= config::get("URL"); ?>admin/DisableTool" method="post">
                  Disabled: <input type="checkbox" name="disable" value="0" <?php if ($tools->enabled == 0) { ?> checked <?php } ?> />
                  <input type="hidden" value="<?= $tools->tool_id; ?>" name ="id">
                  <input type="submit" value="Submit">
                </form>
                <form action="<?= config::get("URL"); ?>admin/DeleteTool" method="post">

                  <input type="hidden" value="<?= $tools->tool_id; ?>" name ="id">
                  <input type="submit" value="Delete Tool">
                </form>
            </div>
          <?php } ?>
    </div>
</div>
