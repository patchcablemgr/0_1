<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/redirectToLogin.php';
$qls->Security->check_auth_page('administrator.php');
\Stripe\Stripe::setApiKey(STRIPE_SK);

// Collect CC info
if($qls->sub_info['cust_id']) {
	$custID = $qls->sub_info['cust_id'];
	$subLevel = $qls->sub_info['sub_level'];
	$customer = \Stripe\Customer::retrieve($custID);
	$subID = $customer['subscriptions']['data'][0]['id'];
	$subscription = \Stripe\Subscription::retrieve($subID);
	$plan = $subscription->plan->id;
	$accountBalance = $customer->account_balance;
	$accountBalance = $accountBalance/100;
	setlocale(LC_MONETARY, 'en_US.UTF-8');
	$accountBalance = money_format('%.2n', $accountBalance);
	
	$last4 = '**** '.$customer['sources']['data'][0]['last4'];
	$cardExp = $customer['sources']['data'][0]['exp_month'].'/'.$customer['sources']['data'][0]['exp_year'];
	
	$epoch = $customer['subscriptions']['data'][0]['current_period_end'];
	$dt = new DateTime("@$epoch", new DateTimeZone('UTC'));
	$dt->setTimezone(new DateTimeZone($qls->user_info['timezone']));
	$subExp = $dt->format('Y-m-d');
	$subButtonCardInfoText = 'Update';
	$selectPlanEnabled = true;
} else {
	$last4 = 'N/A';
	$cardExp = 'N/A';
	$subStatus = 'N/A';
	$subExp = 'N/A';
	$accountBalance = 'N/A';
	$subButtonCardInfoText = 'Add Card';
	$selectPlanEnabled = false;
}
?>

<?php require 'includes/header_start.php'; ?>

<!-- X-editable css -->
<link type="text/css" href="assets/plugins/x-editable/css/bootstrap-editable.css" rel="stylesheet">

<script src="https://checkout.stripe.com/checkout.js"></script>

<?php require 'includes/header_end.php'; ?>

<div id="removeUserModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div title="Close">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<i class="zmdi zmdi-close"></i>
					</button>
				</div>
                <h4 class="modal-title" id="removeUserModalLabel">Remove User</h4>
            </div>
            <div class="modal-body">
                Delete: username?
            </div>
			<div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary waves-effect waves-light">Ok</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="updatePlanModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="updatePlanModal" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div title="Close">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<i class="zmdi zmdi-close"></i>
					</button>
				</div>
                <h4 class="modal-title" id="cancelSubModalLabel">Update Plan</h4>
            </div>
            <div class="modal-body">
				<p>Updating your plan from "<strong id="confirmCurrentPlan"></strong>" to "<strong id="confirmNewPlan"></strong>" will charge <strong id="confirmAmount"></strong> to the account.</p>
                <p>If the account balance indicates a credit (negative balance), the credit will be applied to the charge before the payment card.</p>
				<p>Proceed with plan update?</p>
            </div>
			<div class="modal-footer">
                <button id="buttonUpdateNo" type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">No</button>
                <button id="buttonUpdateYes" type="button" class="btn btn-danger waves-effect waves-light">Yes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <h4 class="page-title">Admin - General</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 col-xl-12">
		<div class="row">
			<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 col-xl-12">
				<div id="alertMsg"></div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-xs-12 col-md-6 col-lg-6 col-xl-3">
		<div class="card-box">
			<h4 class="header-title m-t-0 m-b-30">Payment Info</h4>
			<table class="table">
				<tr>
					<th>Last 4:</th>
					<td id="paymentInfoLast4"><?php echo $last4; ?></td>
				</tr>
				<tr>
					<th>Expiration:</th>
					<td id="paymentInfoExpiration"><?php echo $cardExp; ?></td>
				</tr>
			</table>
			<button id="buttonCardInfo" type="button" class="btn btn-primary w-md waves-effect waves-light"><?php echo $subButtonCardInfoText; ?></button>
		</div>
	</div>
	<div class="col-sm-12 col-xs-12 col-md-6 col-lg-6 col-xl-3">
		<div class="card-box">
			<h4 class="header-title m-t-0 m-b-30">Subscription Info</h4>
			<table class="table">
				<tr>
					<th>Plan:</th>
					<td>
						<select id="selectPlan" class="form-control" <?php echo $selectPlanEnabled ? '' : 'disabled';?>>
							<option value="free" <?php echo $plan == STRIPE_FREE_PLANID ? 'selected' : '';?>>Free</option>
							<option value="monthly" <?php echo $plan == STRIPE_MONTHLY_PLANID ? 'selected' : '';?>>Monthly - <?php echo PRICE_STRING_MONTHLY; ?></option>
							<option value="yearly" <?php echo $plan == STRIPE_YEARLY_PLANID ? 'selected' : '';?>>Yearly - <?php echo PRICE_STRING_YEARLY; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Account Balance:</th>
					<td id="accountBalance"><?php echo $accountBalance; ?></td>
				</tr>
				<tr>
					<th>Renews Before:</th>
					<td id="subscriptionRenewal"><?php echo $subExp; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="col-sm-12 col-xs-12 col-md-6 col-lg-6 col-xl-3">
		<div class="card-box">
			<h4 class="header-title m-t-0 m-b-30">Invite User</h4>
			<form>
				<fieldset class="form-group">
					<label for="invitationEmail">Email address</label>
					<input id="invitationEmail" type="email" class="form-control" placeholder="Enter email">
					<small class="text-muted">Invite a user to your team.
					</small>
				</fieldset>
				<button id="invitationSubmit" type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>

	<div class="col-sm-12 col-xs-12 col-md-6 col-lg-6 col-xl-3">
		<div class="card-box">
			<h4 class="header-title m-t-0 m-b-30">Organization Name</h4>
			<a href="#" id="inline-orgName" data-type="text" data-pk="1" data-title="Enter organization name"><?php echo $qls->org_info['name']; ?></a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 col-xl-6">
		<div class="card-box">
			<h4 class="header-title m-t-0 m-b-30">Manage Users</h4>
			<div class="p-20">
				<table class="table table-sm">
					<thead>
					<tr>
						<th>User</th>
						<th>Status</th>
						<th>2FA</th>
						<th>Role</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						$groupArray = array();
						$query = $qls->SQL->select('*', 'groups');
						while($groupRow = $qls->SQL->fetch_assoc($query)) {
							$groupArray[$groupRow['id']] = $groupRow;
						}
						
						$query = $qls->SQL->select('*', 'users', array('org_id' => array('=', $qls->user_info['org_id'])));
						while($row = $qls->SQL->fetch_assoc($query)) {
							echo '<tr>';
							echo '<td>'.$row['username'].'</td>';
							echo '<td>Active</td>';
							if($row['mfa']) {
								echo '<td>Yes</td>';
							} else {
								echo '<td>No</td>';
							}
							if($row['id'] == $qls->user_info['id']) {
								echo '<td>'.$groupArray[$row['group_id']]['name'].'</td>';
							} else {
								echo '<td><a class="editableUserRole" href="#" data-type="select" data-pk="'.$row['id'].'" data-value="'.$row['group_id'].'" data-userType="active"></a></td>';
							}
							
							echo '<td>';
							if($row['id'] != $qls->user_info['id']) {
								echo '<button class="buttonRemoveUser btn btn-sm waves-effect waves-light btn-danger" data-userType="active" data-userID="'.$row['id'].'" type="button" title="Remove user">';
								echo '<i class="fa fa-remove"></i>';
								echo '</button>';
							}
							echo '</td>';
							echo '</tr>';
						}
						
						$query = $qls->SQL->select('*', 'invitations', array('org_id' => array('=', $qls->user_info['org_id']), 'AND', 'used' => array('=', 0)));
						while($row = $qls->SQL->fetch_assoc($query)) {
							echo '<tr>';
							echo '<td>'.$row['email'].'</td>';
							echo '<td>Pending</td>';
							echo '<td><a class="editableUserRole" href="#" data-type="select" data-pk="'.$row['id'].'" data-value="'.$row['group_id'].'" data-userType="invitation"></a></td>';
							echo '<td>';
								echo '<button class="buttonRemoveUser btn btn-sm waves-effect waves-light btn-danger" data-userType="invitation" data-userID="'.$row['id'].'" type="button" title="Remove user">';
								echo '<i class="fa fa-remove"></i>';
								echo '</button>';
							echo '</td>';
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php require 'includes/footer_start.php' ?>

<script src="assets/pages/jquery.admin.js"></script>

<!-- Modal-Effect -->
<script src="assets/plugins/custombox/js/custombox.min.js"></script>
<script src="assets/plugins/custombox/js/legacy.min.js"></script>

<!-- XEditable Plugin -->
<script src="assets/plugins/moment/moment.js"></script>
<script type="text/javascript" src="assets/plugins/x-editable/js/bootstrap-editable.min.js"></script>

<?php require 'includes/footer_end.php' ?>
