<div class="container">
    <h1>Admin/index</h1>

    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Subscriptions</h3>

        <div>
          This shows you all of the created subscriptions that have been created by the admins And How to create a new one
        </div>
        <div>

          <form action="<?= config::get("URL"); ?>admin/CreateSubscription" method="post" id = "subscription">
            <b>Subscription Name: </b> <input type="text" name="name" placeholder="Really Cool Name" maxlength="30" required><br>
            <textarea name="description" form="subscription" rows="4" cols="120" maxlength="1000" required>Enter text here...</textarea>
            <b> Days:  <input type="number" min="0.00" max="10000.00" step="1" name ="days" placeholder="5"required /> </b>
            <b> Price (Monthly): <input type="number" min="0.00" max="10000.00" step="0.01" name ="priceM" placeholder="50" required /><br></b>
            <b> Price (Yearly): <input type="number" min="0.00" max="10000.00" step="0.01" name ="priceY" placeholder="50" required /><br></b>
            <b> 24 Hour Access: <input type="checkbox" name="access" value="1"> </b><br>
            <b> Features: </b><br>
            Extra Day Costs:<input type="number" min="0.00" max="10000.00" step="0.01" name ="extra" placeholder="10" required /> <br>
            Guest Pass Cost: <input type="number" min="0.00" max="10000.00" step="0.01" name ="guest" placeholder="8"required /><br>
            Storage Discount: <input type="number" min="0" max="100" step="0.01" name ="storage" placeholder="10" required/> (Precent)<br>
            Tool Rental Discount: <input type="number" min="0" max="100" step="0.01" name ="rental" placeholder="10" required/> (Precent)<br>
            Machine Use Discount:<input type="number" min="0" max="100" step="0.01" name ="machine" placeholder="10" required/> (Precent)<br>

            Priority Booking: <input type="checkbox" name="priority" value="1"> </b><br>

             <input type="submit" value="Submit">
           </form>
          </div>


        <div>

                <?php $hour = "24Hour"; foreach ($this->subscriptions as $subscription) { ?>
                  <div class="box">

                    <h3>Name: <?= $subscription->name; ?></h3>

                  <p> <b> Description</b>  <?= $subscription->description; ?>  <p>
                    <ul>
                     <li> Adding an extra day for: $<?= $subscription->extra_day; ?></li>
                     <li> Guest Passes at : $<?= $subscription->guest_pass; ?></li>
                     <li> Guest Passes at : $<?= $subscription->guest_pass; ?></li>
                     <?php if ($subscription->priority_bookings == 1 ) { ?>  <li> Prority Bookings </li> <?php } ?>
                     <?php if ($subscription->storage_discount == 100 ) { ?>  <li> Free Storage </li> <?php }elseif ($subscription->storage_discount != 0 ) { ?> <li><?= $subscription->storage_discount; ?>% Discount on Storage </li> <?php } ?>
                     <?php if ($subscription->rental_discount == 100 ) { ?>  <li> Free Tool Rentals </li> <?php }elseif ($subscription->rental_discount != 0 ) { ?> <li><?= $subscription->rental_discount; ?>% Discount on Tool Rentals</li> <?php } ?>
                      <?php if ($subscription->machine_discount == 100 ) { ?>  <li> Free Machine Use </li> <?php }elseif ($subscription->machine_discount != 0 ) { ?> <li><?= $subscription->machine_discount; ?>% Discount on Machine Use </li> <?php } ?>
                    </ul>
                    <br>
                    24 Hour Access: <input type="checkbox" name="softDelete" <?php if ($subscription->$hour) { ?> checked <?php } ?> disabled/>
                    <br>
                    <p> <b> Monthly price: <?= $subscription->priceM; ?>  |  Yearly price: <?= $subscription->priceY; ?>  | Days of Access Per month: <?= $subscription->days; ?></b>  </p>
                    <br>
                    <p> Stripe ID (Monthly): <?= $subscription->stripe_id_M; ?> </p>
                    <p> Stripe ID (Yearly): <?= $subscription->stripe_id_Y; ?> </p>
                      <form action="<?= config::get("URL"); ?>admin/DisableSub" method="post">
                        Disabled: <input type="checkbox" name="disable" value="0" <?php if ($subscription->enabled == 0) { ?> checked <?php } ?> />
                        <input type="hidden" value="<?= $subscription->subscription_id; ?>" name ="id">
                        <input type="submit" value="Submit">
                      </form>
                      <form action="<?= config::get("URL"); ?>admin/DeleteSub" method="post">

                        <input type="hidden" value="<?= $subscription->subscription_id; ?>" name ="id">
                        <input type="submit" value="Delete Subscription">
                      </form>
                  </div>
                <?php } ?>


    </div>
</div>
