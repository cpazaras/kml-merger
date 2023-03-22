<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

include('dbconn.php');
include('functions.php');
include('header.php');

?>


<?
htmldump($_POST, '$_POST');	//debug
if(isset($_POST) && !empty($_POST)){
	htmldumpdie($_POST);	//debug
	die('POST');

}//end if
?>


<!-- datatables -->
<link href="scripts/datatables/datatables.min.css" rel="stylesheet"/>
<script src="scripts/datatables/datatables.min.js"></script>
<!-- /datatables -->


<div class="container" style="margin-top: 30px;">
	<div class="row">
		<div class="col-xs-12">

			<form name="uploadform" id="uploadform" method="post" enctype="multipart/form-data" action="">

				<div class="form-group row">
					<label for="market" class="col-sm-5 col-form-label">Super Market:</label>
					<div class="col-sm-7">
						<select name="market">
							<?foreach(get_enum_values('supermarket', 'market') as $value):?>
								<option value="<?=$value?>"><?=ucfirst($value)?></option>
							<?endforeach;?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="amount" class="col-sm-5 col-form-label">Amount:</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" name="amount" id="amount" value="" placeholder="" />
					</div>
				</div>


				<div class="form-group row">
					<label for="payer" class="col-sm-5 col-form-label">Paid by:</label>
					<div class="col-sm-7">
						<select name="payer">
							<?foreach(get_enum_values('supermarket', 'payer') as $value):?>
								<option value="<?=$value?>"><?=ucfirst($value)?></option>
							<?endforeach;?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="payer" class="col-sm-5 col-form-label">Payment Method:</label>
					<div class="col-sm-7">
						<select name="payment_method">
							<?foreach(get_enum_values('supermarket', 'payment_method') as $value):?>
								<option value="<?=$value?>"><?=ucfirst($value)?></option>
							<?endforeach;?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="market" class="col-sm-5 col-form-label">Paid On:</label>
					<div class="col-sm-7">
						<input type="date" class="form-control" name="date" id="date" value="<?=date('Y-m-d')?>" placeholder="" />
					</div>
				</div>




				<input type="submit" name="submit" value="Submit" />
				<br/>
				<br/>
				<div class="msg" style="display: none;"></div>
			</form>

		</div>
	</div>
</div>

<script type="text/javascript">
/*
//uncomment for ajax upload

	$(document).ready(function (e){
		$("#uploadform").on('submit',(function(e){
			e.preventDefault();
			$.ajax({
				url: "ajaxupload.php",
				type: "POST",
				data:  new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function(){
					$(".msg").removeClass('alert-danger').removeClass('alert-success').hide();
				},
				success: function(response){
					response = JSON.parse(response);

					//if error
					if(response['error'] == true){
						$(".msg").addClass('alert-danger').html("ERROR: "+response['error_msg']);

						return false;
					}//end if

					//success
					$(".msg").html(response['output']);
					download('merged.kml', response['merge_result']);

					return true;
				},
				error: function(error){
					console.log('AJAX ERROR: '+error);	//debug
				},
				complete: function(e){
					$(".msg").fadeIn();
				},
			});
		}));
	});
*/
</script>
<?php

include('footer.php');
