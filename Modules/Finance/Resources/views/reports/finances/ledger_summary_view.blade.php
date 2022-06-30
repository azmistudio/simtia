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
				                <td style="width:63px"></td>
				                <td style="width:100px"></td>
				                <td style="width:10px"></td>
				                <td style="width:140px"></td>
				                <td style="width:10px"></td>
				                <td style="width:90px"></td>
				                <td style="width:10px"></td>
				                <td style="width:90px"></td>
				                <td style="width:10px"></td>
				                <td style="width:90px"></td>
				                <td style="width:10px"></td>
				                <td style="width:90px"></td>
				                <td style="width:23px"></td>
				                <td style="width:50px"></td>
				                <td style="width:28px"></td>
			              	</tr>
		              		<tr valign="top" style="height:28px"><td colspan="16"> </td></tr>
		              		<tr valign="top" style="height:17px">
		                		<td> </td>
		                		<td colspan="16" class="cell-title">
                   		 			<span style="font-size: 12px; line-height: 1.0078125;">{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</span>
                  				</td>
		                		<td> </td>
		              		</tr>
		              		<tr valign="top" style="height:23px">
		                		<td> </td>
		                		<td colspan="16" class="cell-title">
			                    	<span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Ringkasan Buku Besar - {{ $requests['start'] }} s.d {{ $requests['end'] }}</span>
			                  	</td>
		                		<td> </td>
		              		</tr>
			              	<tr valign="top" style="height:16px">
			                	<td> </td>
			                	<td colspan="16" class="cell-title">
			                    	<span style="font-size: 10px; line-height: 1.0078125;">Tahun Buku {{ $requests['bookyear'] }} - Tercetak {{ date('d/m/Y H:i:s') }}</span>
			                  	</td>
			                	<td> </td>
			              	</tr>
		              		<tr valign="top" style="height:20px">
		                		<td colspan="16"> </td>
		              		</tr>
		              		<tr valign="top" style="height:14px">
				                <td colspan="2"> </td>
				                <td class="header-title-td" style="width: 100px;"><span class="header-title">Kode</span></td>
								<td> </td>
								<td class="header-title-td" style="width: 140px;"><span class="header-title">Nama</span></td>
								<td> </td>
								<td class="header-title-td" style="width: 90px;;text-align: right;"><span class="header-title">Saldo Awal</span></td>
								<td> </td>
								<td class="header-title-td" style="width: 90px;text-align: right;"><span class="header-title">Perubahan Debit</span></td>
								<td> </td>
								<td class="header-title-td" style="width: 90px;text-align: right;"><span class="header-title">Perubahan Kredit</span></td>
								<td> </td>
								<td class="header-title-td" style="width: 90px;text-align: right;"><span class="header-title">Saldo Akhir</span></td>
				                <td colspan="3"> </td>
				            </tr>
		              		<tr valign="top" style="height:5px">
		                		<td colspan="16"> </td>
		              		</tr>
		              		<!-- account -->
			                @foreach ($accounts as $account)

			                @if ($balance) 
			                	@if ($account->beg_balance > 0 || $account->trx_debit > 0 || $account->trx_credit > 0 || $account->end_balance > 0) 
			                	<tr valign="top" style="height:14px">
				                 	<td colspan="2"></td>
				                  	<td class="content-td" style="width: 100px;">
				                    	<span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">
				                      	@if ($account->parent > 0) 
				                        	&nbsp;&nbsp;{{ $account->code }}
				                      	@else
				                        	{{ $account->code }}
				                      	@endif
				                    	</span>
				                  	</td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 140px;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ $account->name }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->beg_balance,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_debit,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_credit,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->end_balance,2) }}</span></td>
				                  	<td colspan="3"></td>
				                </tr>
			                	@endif
			                @else
			                	<tr valign="top" style="height:14px">
				                 	<td colspan="2"></td>
				                  	<td class="content-td" style="width: 100px;">
				                    	<span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">
				                      	@if ($account->parent > 0) 
				                        	&nbsp;&nbsp;{{ $account->code }}
				                      	@else
				                        	{{ $account->code }}
				                      	@endif
				                    	</span>
				                  	</td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 140px;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ $account->name }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->beg_balance,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_debit,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_credit,2) }}</span></td>
				                  	<td> </td>
				                  	<td class="content-td" style="width: 90px;text-align: right;"><span class="content-span" style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->end_balance,2) }}</span></td>
				                  	<td colspan="3"></td>
				                </tr>
			                @endif

			                @endforeach
		              		<tr valign="top" style="height:28px">
		                		<td colspan="16"> </td>
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