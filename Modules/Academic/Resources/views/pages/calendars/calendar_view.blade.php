<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-5 p-0">
            <p class="mb-1">Kalender Akademik {{ $calendar['description'] }}</p>
            <p class="mb-1">Departemen {{ $calendar['department'] }}</p>
            <p class="mb-1">Periode {{ $calendar['period'] }}</p>
            <br/>
            <table id="tb-academic-calendar-view" class="easyui-datagrid" style="width:100%;height:80%" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'activity',width:350,resizeable:true,sortable:true">Kegiatan</th>
                        <th data-options="field:'start',width:80,resizeable:true,sortable:false,align:'center'">Dari</th>
                        <th data-options="field:'end',width:80,resizeable:true,sortable:false,align:'center'">Sampai</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-7">
            <div id="yearly-calendar"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dataSources = [
        @foreach ($activities as $activity)
        { id: "{{ $activity->id }}", name: "{{ $activity->activity }}", startDate: getDate("{{ $activity->start }}"), endDate: getDate("{{ $activity->end }}") },
        @endforeach
    ]
    new Calendar('#yearly-calendar', {
        language: 'id',
        minDate: getDate("{{ $calendar['start_date'] }}"),
        maxDate: getDate("{{ $calendar['end_date'] }}"),
        dataSource: dataSources
    })
    $(function () {
        let id = "{!! $calendar['id'] !!}"
        var dg = $("#tb-academic-calendar-view")
        dg.datagrid({
            url: "{{ url('academic/calendar/activity/data') }}",
            queryParams: { fcalview: id, _token: "{{ csrf_token() }}" },
        })
    })
    function getDate(param) {
        params = param.split("-")
        return new Date(params[0], params[1] - 1, params[2])
    }
</script>