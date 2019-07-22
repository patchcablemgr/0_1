<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/redirectToLogin.php';
$qls->Security->check_auth_page('operator.php');
?>

<?php require 'includes/header_start.php'; ?>
    <!-- DataTables -->
    <link href="assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
<?php require 'includes/header_end.php'; ?>


<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <h4 class="page-title">Purchasing</h4>
    </div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="card-box table-responsive">
			<h4 class="m-t-0 header-title"><b>Invoices</b></h4>
			<p class="text-muted font-13 m-b-30">
			Invoices and order statuses.
			</p>

			<table id="datatable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Order #</th>
						<th>Date</th>
						<th>Notes</th>
						<th>Status</th>
						<th>View</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$orgID = $qls->user_info['org_id'];
					$results = $qls->SQL->select('*', 'invoices', array('org_id' => array('=', $orgID)));
					while ($row = $qls->SQL->fetch_assoc($results)) {
						$datetimeString = date($row['date']);
						$datetime = new DateTime($datetimeString, new DateTimeZone('UTC'));
						$datetime->setTimezone(new DateTimeZone($qls->user_info['timezone']));
						echo "<tr>";
						echo "<td>".$row['order_id']."</td>";
						echo "<td>".$datetime->format('j-M-Y g:i a T')."</td>";
						echo "<td>".$row['notes']."</td>";
						echo "<td>".$row['status']."</td>";
						echo "<td style=\"text-align:center;\"><button data-invoiceID=\"".$row['id']."\" class=\"viewButton btn btn-sm waves-effect waves-light btn-info\"> <i class=\"ion-eye\"></i> </button></td>";
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div> <!-- end row -->

<div id="invoiceContainer"></div>

<?php require 'includes/footer_start.php' ?>
    <!-- Required datatable js -->
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    
    <script src="assets/pages/jquery.invoices.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function () {
            $('#datatable').DataTable();
        });

    </script>
<?php require 'includes/footer_end.php' ?>
