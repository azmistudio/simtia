@php
	$balances = array();
@endphp
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">
		body {font-family: "Segoe UI", "Open Sans", serif !important;} 
		a { text-decoration: none } 
		a:hover { text-decoration: underline; cursor: pointer; }
		.cell-title {padding-top: 3px; padding-left: 5px; padding-right: 5px; width: 786px; word-wrap: break-word; white-space: nowrap; text-indent: 0px; text-align: center;}
		.header-title-td {border-bottom: 1px solid #333; padding-top: 3px; padding-left: 5px; padding-right: 5px; word-wrap: break-word; white-space: nowrap; text-indent: 0px;}
		.header-title {color: #333; font-size: 10px; line-height: 1.1499023; font-weight: bold;}
		.content-td {padding-top: 3px; padding-left: 5px; padding-right: 5px; word-wrap: break-word; white-space: nowrap; text-indent: 0px;}
		.content-span {color: #000000; font-size: 10px; line-height: 1.1499023;}
		.subtotal-td {border-top: 1px solid #003366; padding-top: 3px; padding-left: 5px; padding-right: 5px; word-wrap: break-word; white-space: nowrap; text-indent: 0px;}
	</style>
</head>
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	    <tbody>
			<tr>
				<td width="50%">&nbsp;</td>
				<td align="center">
					<br>
					<table cellpadding="0" cellspacing="0" border="0" style="empty-cells: show; width: 842px; border-collapse: collapse; background-color: white;">
						<tbody>
							<tr valign="top" style="height:0">
								<td style="width:28px"></td>
								<td style="width:28px"></td>
								<td style="width:12px"></td>
								<td style="width:65px"></td>
								<td style="width:10px"></td>
								<td style="width:110px"></td>
								<td style="width:3px"></td>
								<td style="width:7px"></td>
								<td style="width:200px"></td>
								<td style="width:10px"></td>
								<td style="width:90px"></td>
								<td style="width:10px"></td>
								<td style="width:90px"></td>
								<td style="width:10px"></td>
								<td style="width:90px"></td>
								<td style="width:1px"></td>
								<td style="width:50px"></td>
								<td style="width:28px"></td>
							</tr>
						  	<tr valign="top" style="height:28px">
						    	<td colspan="18"></td>
						  	</tr>
						  	<tr valign="top" style="height:17px">
						    	<td></td>
						    	<td colspan="16" class="cell-title">
						    		<span style="font-size: 12px; line-height: 1.0078125;">{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</span>
						    	</td>
						    	<td></td>
						  	</tr>
						  	<tr valign="top" style="height:20px">
						    	<td></td>
						    	<td colspan="16" class="cell-title">
						    		<span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Rincian Buku Besar - {{ $requests['start'] }} s.d {{ $requests['end'] }}</span>
						    	</td>
						    	<td></td>
						  	</tr>
							<tr valign="top" style="height:16px">
							    <td></td>
							    <td colspan="16" class="cell-title">
							    	<span style="font-size: 10px; line-height: 1.0078125;">Tahun Buku {{ $requests['bookyear'] }} - Tercetak {{ date('d/m/Y H:i:s') }}</span>
							    </td>
							    <td></td>
							</tr>
						  	<tr valign="top" style="height:12px">
							    <td></td>
							    <td colspan="16" class="cell-title"></td>
							    <td></td>
						  	</tr>
						  	<tr valign="top" style="height:10px">
						    	<td colspan="18"></td>
						  	</tr>
						  	<tr valign="top" style="height:14px">
							    <td colspan="3"></td>
							    <td class="header-title-td">
							    	<span class="header-title">Tanggal</span>
							    </td>
							    <td></td>
							    <td class="header-title-td">
							    	<span class="header-title">Tipe Transaksi</span>
							    </td>
							    <td colspan="2"></td>
							    <td class="header-title-td">
							    	<span class="header-title">Departemen</span>
							    </td>
							    <td></td>
							    <td class="header-title-td">
							    	<span class="header-title">Keterangan</span>
							    </td>
							    <td></td>
							    <td class="header-title-td" style="text-align: right;">
							    	<span class="header-title">Debit</span>
							    </td>
							    <td></td>
							    <td class="header-title-td" style="text-align: right;">
							    	<span class="header-title">Kredit</span>
							    </td>
							    <td></td>
							    <td class="header-title-td" style="text-align: right;">
							    	<span class="header-title">Saldo Akhir</span>
							    </td>
							    <td colspan="3"></td>
						  	</tr>
						  	<tr valign="top" style="height:5px">
						    	<td colspan="18"></td>
						  	</tr>
						  	<!-- account -->
						  	@foreach ($accounts as $account)
						  	<tr valign="top" style="height:14px">
							    <td colspan="2"></td>
							    <td colspan="7" class="content-td">
							    	<span class="content-span" style="font-weight: bold;">{{ $account->code }} | {{ $account->name }}</span>
							    </td>
							    <td colspan="11"></td>
						  	</tr>
						  	<!-- end balance -->
						  	@foreach ($end_balances as $end_balance)
						  	@if ($end_balance->account_id == $account->id)
						  	@php 
						  		$balances[] = array(
						  			'account_id' => $end_balance->account_id, 
						  			'end_balance'=> $end_balance->end_balance,
						  		); 
						  	@endphp
						  	<tr valign="top" style="height:14px">
				                <td colspan="3"></td>
				                <td class="content-td"><span class="content-span">{{ $balance_date }}</span></td>
				                <td></td>
				                <td class="content-td"></td>
				                <td colspan="2"></td>
				                <td class="content-td"></td>
				                <td></td>
				                <td class="content-td"><span class="content-span">Saldo per {{ $balance_date }}</span></td>
				                <td></td>
				                <td class="content-td"></td>
				                <td></td>
				                <td class="content-td"></td>
				                <td></td>
				                <td class="content-td" style="text-align: right;"><span class="content-span">{{ number_format($end_balance->end_balance,2) }}</span></td>
				                <td colspan="3"></td>
			              	</tr>
						  	@endif
						  	@endforeach
						  	<!-- transaction -->
						  	@foreach ($account_details as $account_detail)
						  	@if ($account_detail->account_id == $account->id)
						  	@foreach ($balances as $balance)
						  	@if ($balance['account_id'] == $account->id)
			              	<tr valign="top" style="height:14px">
				                <td colspan="3"></td>
				                <td class="content-td" style="width: 65px;"><span class="content-span">{{ $account_detail->journal_date }}</span></td>
				                <td></td>
				                <td class="content-td" style="width: 80px;"><span class="content-span">{{ $account_detail->source }}</span></td>
				                <td colspan="2"></td>
				                <td class="content-td" style="width: 60px;"><span class="content-span">{{ $account_detail->deptname }}</span></td>
				                <td></td>
				                <td class="content-td" style="width: 500px;white-space: pre-wrap;"><span class="content-span">{{ $account_detail->remark }}</span></td>
				                <td></td>
				                <td class="content-td" style="width: 90px;text-align: right;"><span class="content-span">{{ number_format($account_detail->debit,2) }}</span></td>
				                <td></td>
				                <td class="content-td" style="width: 90px;text-align: right;"><span class="content-span">{{ number_format($account_detail->credit,2) }}</span></td>
				                <td></td>
				                <td class="content-td" style="width: 90px;text-align: right;"> 
				                	<span class="content-span">
				                		{{ number_format(($balance['end_balance'] + $account_detail->debit) - $account_detail->credit,2) }}
				                	</span>
				            	</td>
				                <td colspan="3"></td>
			              	</tr>
			              	@endif
						  	@endforeach
							@endif
						  	@endforeach
						  	<!-- sub total -->
						  	@foreach ($subtotals as $total)
						  	@if ($total->account_id == $account->id)
						  	<tr valign="top" style="height:14px">
				                <td colspan="12"> </td>
				                <td class="subtotal-td" style="width: 90px;text-align: right;"><span class="content-span"><b>{{ number_format($total->debit,2) }}</b></span></td>
				                <td> </td>
				                <td class="subtotal-td" style="width: 90px;text-align: right;"><span class="content-span"><b>{{ number_format($total->credit,2) }}</b></span></td>
				                <td colspan="5"> </td>
			              	</tr>
			              	@endif
						  	@endforeach
						  	@endforeach
						  	<tr valign="top" style="height:30px">
						    	<td colspan="18"></td>
						  	</tr>
						</tbody>
					</table>
					<br>
				</td>
				<td width="50%">&nbsp;</td>
			</tr>
	    </tbody>
  	</table>
</body>
</html>