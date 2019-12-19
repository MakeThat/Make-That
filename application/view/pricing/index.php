

<div class="container">
  <h1> Memberships</h1>
  <p><font size="+2"> Pick and Choose Between our vast quanity of memberships. If you cant find one you like use our Build your own membership tools to create a custom plan. Our memberships are based off the number of days that you can visit the makerspace.
  These days can be used any time during our open hours or in the case of premium plans you can get access 24/7.</p>

  <a href="#memberships">Take me to pre made memberships </a> <br>
  <a href="#build">Take me to build your own membership </a>

  <div class="box">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <h1 id ="memberships"> Pre Built Memberships </h1>
          <?php $hour = "24Hour"; foreach ($this->subscriptions as $subscription) {  $rand = rand();?>
  <div class="box">

    <h2> <?= $subscription->name; ?></h2>

  <p>   <?= $subscription->description; ?>  <p>
    <ul>
     <li> Adding an extra day for: $<?= $subscription->extra_day; ?></li>
     <li> Guest Passes at : $<?= $subscription->guest_pass; ?></li>
     <li> Guest Passes at : $<?= $subscription->guest_pass; ?></li>
     <?php if ($subscription->priority_bookings == 1 ) { ?>  <li> Prority Bookings </li> <?php } ?>
     <?php if ($subscription->storage_discount == 100 ) { ?>  <li> Free Storage </li> <?php }elseif ($subscription->storage_discount != 0 ) { ?> <li><?= $subscription->storage_discount; ?>% Discount on Storage </li> <?php } ?>
     <?php if ($subscription->rental_discount == 100 ) { ?>  <li> Free Tool Rentals </li> <?php }elseif ($subscription->rental_discount != 0 ) { ?> <li><?= $subscription->rental_discount; ?>% Discount on Tool Rentals</li> <?php } ?>
      <?php if ($subscription->machine_discount == 100 ) { ?>  <li> Free Machine Use </li> <?php }elseif ($subscription->machine_discount != 0 ) { ?> <li><?= $subscription->machine_discount; ?>% Discount on Machine Use </li> <?php } ?>
    </ul>

    <p> <b>  Days of Access Per month: <?= $subscription->days; ?>  | 24 Hour Access: <input type="checkbox" name="softDelete" <?php if ($subscription->$hour) { ?> checked <?php } ?> disabled/> </p>
    <p> Pricing:
      Monthly: $<?= $subscription->priceM; ?>.00 |  Yearly: $<?= $subscription->priceY; ?>.00
    </p>
    </b>
  </div>
  <form action="<?= config::get("URL"); ?>pricing/checkout" method="post" id="<?= $rand; ?>">
      <select form = "<?= $rand; ?>">
        <option value="month">Monthly Billing ($<?= $subscription->priceM; ?>.00)</option>
        <option value="year">Yearly Billing ($<?= $subscription->priceY; ?>.00)</option>
      </select>
    <input type="hidden" value="<?= $subscription->subscription_id; ?>" name ="id">
    <input type="submit" value="Buy">
<?php } ?>
      </div>
    <div class="box">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      </head>
        <h1 id ="build" >Build Your Own Membership </h1>
      <p> At Make That we belive that users shouldnt pay for what they dont need. While most makerspaces charge you for the full month when you might only show up
        3 or four weekends a month. We belive that if you just pay for the amount of days that you want to use the makerspace it works better for all of us. </p>
      <body>
        <form class="form-horizontal" action="<?php echo Config::get('URL'); ?>pricing/create" method="post" id="payment-form">
        <h2> Days of Access Per Month </h2>
        <p> We charge the types of days diffrently due to a variance in open hours </p>
        <p id="Price"> Current Price $90 </p>
          <h3> Weekday Access </h3>
          <p> This is the number of weekdays a month that you plan on accessing the makerspace </p>
           <input type="range" name="weekdays" min="0" max="31" id="Weekdays"  onload="Update()" oninput = "Update()"> <p id="Val1" > 12 Weekdays </p>

        <h2> Extras </h2>
          <h3> 24 Hour Access </h3>
          <p> <b> <input type="checkbox" name="247" id ="247" value="1" onclick="Update()"> </b> Be Able to vist the makerspace any time of day <br>

          <h3> Billing </h3>
          <p><b> Billing Period </p> </b>
          <p> Monthly <b> <input type="radio" name="billing" id = "m" value="Monthly" checked onclick="Update()"> </b> Be Billed on a Monthly Basis<br>
          <p> Yearly <b> <input type="radio" name="billing" id="n" value="Yearly" onclick="Update()"> </b> Be Billed on a Yearly Basis (10% or Greater Discount Per Month)<br>

          <input type="submit">
        </form>

        <script>

        function Update(){
        var WeekDays = document.getElementById("Weekdays").value;
        document.getElementById("Val1").innerHTML = WeekDays + " days";
        var MultiPly1 = 1.0
        var MultiPly2 = 1.0

          if (document.getElementById('n').checked)
          {
            MultiPly1 = .9;
          }
          if (document.getElementById("247").checked)
          {
            MultiPly2 = 1.5;
          }
          var WeekdayDiscount = WeekDays * .03;
          if (WeekdayDiscount > .25){ WeekdayDiscount = .25; }
          var WeekPrice = (1 - WeekdayDiscount ) * 10 * WeekDays;
          var Total = WeekPrice * MultiPly2 * MultiPly1;
          document.getElementById("Price").innerHTML = "Current Price $" + Math.round(Total) ;
        }
        </script>
      </body>

    </div>
</div>
