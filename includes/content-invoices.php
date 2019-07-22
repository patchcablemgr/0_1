<?php
define('QUADODO_IN_SYSTEM', true);
require_once('header.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/shared_tables.php';
	
	$orgID = $qls->user_info['org_id'];
	$invoiceID = $_POST['invoiceID'];
	$invoice = $qls->SQL->fetch_assoc($qls->SQL->select('*', 'invoices', array(
		'id' => array(
			'=',
			$invoiceID
		),
		'AND',
		'org_id' => array(
			'=',
			$orgID
		)
	)));
	$lineItems = json_decode($invoice['order_items'], true);
	$orderNumber = substr($invoice['org_id'], -4).'-'.date('y', strtotime($invoice['date'])).'-'.$invoice['order_id'];
	$datetimeString = date($invoice['date']);
	$datetime = new DateTime($datetimeString, new DateTimeZone('UTC'));
	$datetime->setTimezone(new DateTimeZone($qls->user_info['timezone']));
	$address = $qls->app_SQL->fetch_assoc($qls->app_SQL->select('*', 'table_address', array('id' => array('=', $invoice['addr_id']))));
}
?>
<div class="row">
    <div class="col-xs-12">
        <div class="card-box">
            <div class="panel-body">
                <div class="clearfix">
                    <div class="pull-left">
                        <h2 class="logo" style="color: #2b3d51 !important;">Otterm8</h2>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12">

                        <div class="pull-xs-left m-t-30">
                            <address>
                                <strong>Shipped to:</strong><br>
                                <?php echo $address['displayName']; ?><br>
                                <?php echo $address['street1']; ?><br>
								<?php if($address['street2'] != '') {
									echo $address['street2'].'<br>';
								}?>
								<?php echo $address['city'].' '.$address['state'].' '.$address['zip']; ?><br>
								<?php echo $address['country']; ?><br>
                            </address>
                        </div>
                        <div class="pull-xs-right m-t-30">
                            <p><strong>Order Date: </strong> <?php echo $datetime->format('j-M-Y g:i a T'); ?></p>
                            <p class="m-t-10"><strong>Order Status: </strong> <span class="label label-danger"><?php echo $invoice['status']; ?></span></p>
                            <p class="m-t-10"><strong>Order #: </strong> <?php echo $orderNumber; ?></p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

				<div class="row">
                    <div class="col-xs-12">
						<div class="pull-xs-left m-t-30">
							<strong>Notes:</strong><br>
							<small>
							<?php echo $invoice['notes']; ?>
							</small>
						</div>
					</div>
				</div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table m-t-30">
                                <thead class="bg-faded">
                                <tr><th>#</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total</th>
                                </tr></thead>
                                <tbody>
                                <?php
                                	foreach ($lineItems as $key => $value) {
                                		echo '<tr>';
                                		echo '<td>'.$key.'</td>';
										$product = $productTable[$value['product']]['name'];
										if($value['product'] == '4-4') {
											$product = $product . ' (qty. '.$invoice['label_count'].')';
										}
										$media = $value['media'] == '' ? '' : ' '.$mediaTypeTable[$value['media']]['name'];
										$length = $value['length'] == '' ? '' : ' '.$lengthTable[$value['length']]['name'];
										$lengthUnit = ' '.$mediaCategoryTypeTable[$lengthTable[$value['length']]['category_type_id']]['unit_of_length'] == '' ? '' : $mediaCategoryTypeTable[$lengthTable[$value['length']]['category_type_id']]['unit_of_length'];
										$color = $value['color'] == '' ? '' : ' '.$colorTable[$value['color']]['name'];
										echo '<td>'.$product.$media.$length.$lengthUnit.$color.'</td>';
                                		echo '<td>'.$value['qty'].'</td>';
                                		echo '<td>$'.number_format($value['price'],2).'</td>';
                                		echo '<td>$'.number_format($value['price']*$value['qty'],2).'</td>';
                                		echo '</tr>';
                                	}
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="clearfix m-t-30">
                            <h5 class="small text-inverse font-600"><b>PAYMENT TERMS AND POLICIES</b></h5>

                            <small>
                                All order transactions are subject to Stripe.com's <a href="https://stripe.com/payment-terms/legal">payment terms</a>.  For queries regarding this invoice, please email support@otterm8.com referencing the invoice number.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-6 col-md-offset-3">
                        <p class="text-xs-right"><b>Sub-total:</b> $<?php echo number_format($invoice['total']/100,2);?></p>
                        <p class="text-xs-right">Shipping: $<?php echo number_format($invoice['shipping']/100,2);?></p>
                        <hr>
                        <h3 class="text-xs-right">$<?php echo number_format(($invoice['total']+$invoice['shipping'])/100,2); ?></h3>
                    </div>
                </div>
                <hr>
                <div class="hidden-print">
                    <div class="pull-xs-right">
                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- end row -->