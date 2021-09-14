<?php 
include 'db_connect.php';
$order = $conn->query("SELECT * FROM sales where id = {$_GET['id']}");
foreach($order->fetch_array() as $k => $v){
	$$k= $v;
}
$items = $conn->query("SELECT s.*,i.name FROM stocks s inner join items i on i.id=s.item_id where s.id in ($inventory_ids)");
?>

<style>
	.flex{
		display: inline-flex;
		width: 100%;
	}
	.w-50{
		width: 50%;
	}
	.text-center{
		text-align:center;
	}
	.text-right{
		text-align:right;
	}
	table.wborder{
		width: 100%;
		border-collapse: collapse;
	}
	table.wborder>tbody>tr, table.wborder>tbody>tr>td{
		border:1px solid;
	}
	p{
		margin:unset;
	}

</style>
<div class="container-fluid">
	<p class="text-center"><b>Receipt</b></p>
	<hr>
	<div class="flex">
		<div class="w-100">
			<p>Date: <b><?php echo date("M d, Y",strtotime($date_created)) ?></b></p>
		</div>
	</div>
	<hr>
	<p><b>Purchased List</b></p>
	<table width="100%">
		<thead>
			<tr>
				<td><b>QTY</b></td>
				<td><b>Order</b></td>
				<td class="text-right"><b>Amount</b></td>
			</tr>
		</thead>
		<tbody>
			<?php 
			while($row = $items->fetch_assoc()):
			?>
			<tr>
				<td><?php echo $row['qty'] ?></td>
				<td><p><?php echo $row['name'] ?></p><?php if($row['qty'] > 0): ?><small>(<?php echo number_format($row['price'],2) ?>)</small> <?php endif; ?></td>
				<td class="text-right"><?php echo number_format($row['price'] * $row['qty'],2) ?></td>
			</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<hr>
	<table width="100%">
		<tbody>
			<tr>
				<td><b>Total Amount</b></td>
				<td class="text-right"><b><?php echo number_format($total_amount,2) ?></b></td>
			</tr>
			<?php if($amount_tendered > 0): ?>


			<tr>
				<td><b>Amount Tendered</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered,2) ?></b></td>
			</tr>
			<tr>
				<td><b>Change</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered - $total_amount,2) ?></b></td>
			</tr>
		<?php endif; ?>
			
		</tbody>
	</table>
</div>