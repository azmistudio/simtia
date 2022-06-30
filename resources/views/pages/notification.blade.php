@if (count($lists) > 0)
<ul style="padding: 10px 20px 0 30px;">
	@foreach ($lists as $value)
		@php $items = json_decode($value->items); @endphp
		@if ($value->msg_type == 'update')
			<li>Versi baru tersedia (versi {{ $items[0] }}), <a href="#" onclick="updateInfo()">klik disini</a> untuk memperbarui aplikasi.</li>
		@else
			@for ($i = 0; $i < count($items); $i++)
			<li>{{ $items[$i] }}</li>
			@endfor
		@endif
	@endforeach
@else
<div style="padding: 10px 20px 0 30px;">
	<p>Tidak ada notifikasi baru.</p>
</div>
@endif
{{-- modal --}}
<div id="update-info-w" class="easyui-window" title="Versi Terbaru" data-options="footer:'#footer-info-w',modal:true,closed:true,collapsible:false,minimizable:false,maximizable:false,closable:false" style="width:600px;height:500px;padding:10px;"></div>
<div id="footer-info-w" style="padding:5px;">
    <div class="text-right">
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Process'" href="javascript:void(0)" onclick="updateApp()" style="width:160px;height: 22px;">Perbarui Aplikasi</a>
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Cancel'" href="javascript:void(0)" onclick="$('#update-info-w').window('close')" style="width:80px;height: 22px;">Batal</a>
    </div>
</div>
<div id="update-w" class="easyui-window" title="Memperbarui Aplikasi..." data-options="modal:true,closed:true,collapsible:false,minimizable:false,maximizable:false,closable:false" style="width:400px;height:130px;">
	<div class="container">
		<div class="row pt-3">
			<div class="col-12">
				<div class="mb-1">
					<span id="upd-status"></span>
				</div>
			</div>
			<div class="col-12">
				<div id="upd-progress" class="easyui-progressbar" style="width:355px;"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var valueMax = 0
function updateInfo() {
	$("#update-info-w").window("open").window("refresh", "{{ url('app-updater/description') }}")
}
function updateApp() {
	$("#update-info-w").window("close")
	$("#update-w").window("open")
	setTimeout(executeUpdate(), 3000)
}
function executeUpdate() {
	valueMax = 30
	$("#upd-status").text("Mengunduh berkas terbaru...")
	setTimeout(progressVal(), 3000)
	$.getJSON("{{ url('app-updater/update/download') }}", function(response) {
		if (response.success) {
			valueMax = 70
			$("#upd-status").text("Mengesktrak berkas...")
			setTimeout(progressVal(), 3000)
			//
			$.getJSON("{{ url('app-updater/update/extract') }}" + "?filename_tmp=" + response.message.filename_tmp + "&archive=" + response.message.archive + "&lastVersion=" + response.message.lastVersion, function(response) {
				if (response.success) {
					valueMax = 95
					$("#upd-status").text("Mengesktrak berkas...")
					setTimeout(progressVal(), 3000)
					// 
					$.getJSON("{{ url('app-updater/update/install') }}" + "?filename_tmp=" + response.message.filename_tmp + "&archive=" + response.message.archive + "&lastVersion=" + response.message.lastVersion, function(response) {
						if (response.success) {
							valueMax = 100
							$("#upd-status").text("Pembaruan selesai")
							setTimeout(progressVal(), 1000)
							setTimeout(
								$.messager.alert({
					        		title: "Informasi",
					        		msg: response.message,
					        		fn: function(){
					        			window.location.reload()
					        		}
					        	}), 1000
							)
						} else {
							$.messager.alert({
				        		title: "Peringatan",
				        		msg: response.message,
				        		icon: "error",
				        		fn: function(){
				        			window.location.reload()
				        		}
				        	})
						}
					}).fail(function(xhr) {
				        failResponse(xhr)
				    })
				} else {
					$.messager.alert({
		        		title: "Peringatan",
		        		msg: response.message,
		        		icon: "error",
		        		fn: function(){
		        			window.location.reload()
		        		}
		        	})
				}
			}).fail(function(xhr) {
		        failResponse(xhr)
		    })
		} else {
			$.messager.alert({
        		title: "Peringatan",
        		msg: response.message,
        		icon: "error",
        		fn: function(){
        			window.location.reload()
        		}
        	})
		}
	}).fail(function(xhr) {
        failResponse(xhr)
    })
}
function progressVal() {
	var value = $("#upd-progress").progressbar("getValue")
    if (value < valueMax) {
        value += Math.floor(Math.random() * 10)
        $("#upd-progress").progressbar("setValue", value)
        setTimeout(arguments.callee, 3000)
    }
}
</script>