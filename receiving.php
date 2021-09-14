<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>Receiving List</b>
						 <span class="float:right"><a class="btn btn-primary btn-sm col-sm-3 float-right" href="index.php?page=manage_receiving" id="">
		                    <i class="fa fa-plus"></i> New Entry 
		                </a></span>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Date/Time</th>
									<th class="text-center">Supplier</th>
									<th class="text-center">Total Amount</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$receiving = $conn->query("SELECT r.*,s.name as sname FROM receiving r inner join suppliers s on s.id = r.supplier_id order by r.id asc");
								while($row=$receiving->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p><b><?php echo date("M d, Y H:i A",strtotime($row['date_created'])) ?></b></p>
									</td>
									<td class="">
										<p><b><?php echo ucwords($row['sname']) ?></b></p>
									</td>
									<td class="text-right">
										<b><?php echo number_format($row['total_cost'],2) ?></b>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_receiving" type="button" onclick="location.href='index.php?page=manage_receiving&id=<?php echo $row['id'] ?>'" data-json='<?php echo json_encode($row) ?>'>Edit</button>
										<button class="btn btn-sm btn-danger delete_receiving" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p {
		margin:unset;
	}
	.custom-switch{
		cursor: pointer;
	}
	.custom-switch *{
		cursor: pointer;
	}
</style>
<script>
	$('#manage-receiving').on('reset',function(){
		$('input:hidden').val('')
		$('.select2').val('').trigger('change')
	})
	
	$('#manage-receiving').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_receiving',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully added",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					alert_toast("Data successfully updated",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	})
	$('.edit_receiving').click(function(){
		start_load()
		var data = $(this).attr('data-json');
			data = JSON.parse(data)
		var cat = $('#manage-receiving')
		cat.get(0).reset()
		cat.find("[name='id']").val(data.id)
		cat.find("[name='item_code']").val(data.item_code)
		cat.find("[name='name']").val(data.name)
		cat.find("[name='description']").val(data.description)
		cat.find("[name='price']").val(data.price)
		cat.find("[name='size']").val(data.size)
		end_load()
	})
	$('.delete_receiving').click(function(){
		_conf("Are you sure to delete this receiving?","delete_receiving",[$(this).attr('data-id')])
	})
	function delete_receiving($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_receiving',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	$('table').dataTable()
</script>