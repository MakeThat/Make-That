<div class="container">
    <h1>Admin/index</h1>

    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Admin Panel</h3>

        <div>
          <p> Update All of the sites data straight from the admin panel its pretty cool <p>
          <a href="<?= config::get("URL"); ?>admin/Subscriptions"> Update Subscriptions </a><br>
          <a href="<?= config::get("URL"); ?>admin/tools"> Update Tools </a>
        </div>
        <div>
            <table class="overview-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>User's email</td>
                    <td>Activated ?</td>
                    <td>Active Subscription </td>
                    <td>Tool Qualifactions </td>
                    <td>Link to user's profile</td>
                    <td>suspension Time in days</td>
                    <td>Soft delete</td>
                    <td>Submit</td>
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= $user->user_id; ?></td>
                        <td class="avatar">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>"/>
                            <?php } ?>
                        </td>
                        <td><?= $user->user_name; ?></td>
                        <td><?= $user->user_email; ?></td>
                        <td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                        <form action="<?= config::get("URL"); ?>admin/actionAccountSettings" method="post">
                        <td><input type="checkbox" name="active" value ="Yes" <?php if ($user->ActiveSub == 1) { ?> checked <?php } ?> /></td>
                        <td><input type="number" name="tool" value="<?= $user->ToolQualif; ?>">
                          <td>
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
                        </td>

                            <td><input type="number" name="suspension" /></td>
                            <td><input type="checkbox" name="softDelete" <?php if ($user->user_deleted) { ?> checked <?php } ?> /></td>
                            <td>
                                <input type="hidden" name="user_id" value="<?= $user->user_id; ?>" />
                                <input type="submit" />
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
