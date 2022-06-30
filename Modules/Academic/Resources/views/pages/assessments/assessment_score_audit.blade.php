@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 254 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Log Perubahan Nilai</h5>
        </div>
        <div class="col-4 p-0 text-right">
            
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div class="pt-3 pl-1 pr-1 pb-3" data-options="region:'center'">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <label class="mb-1" style="width:120px;">Bulan:</label>
                    <span class="mr-2"></span>
                    <label class="mb-1" style="width:80px;">Tahun:</label>
                    <span class="mr-2"></span>
                    <label class="mb-1" style="width:200px;">Pengguna:</label>
                </div>
                <div class="col-12">
                    <form id="form-assessment-score-audit">
                    <select id="AssessmentScoreAuditMonth" class="easyui-combobox" style="width:120px;height:22px;" data-options="panelHeight:125">
                        <option value="01">Januari</option>
                        <option value="02">Pebruari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">Nopember</option>
                        <option value="12">Desember</option>
                    </select>
                    <span class="mr-2"></span>
                    <select id="AssessmentScoreAuditYear" class="easyui-combobox" style="width:80px;height:22px;" data-options="panelHeight:125">
                        @foreach ($years as $year)
                        <option value="{{ $year->years }}">{{ $year->years }}</option>
                        @endforeach
                    </select>
                    <span class="mr-2"></span>
                    <select id="AssessmentScoreAuditUser" class="easyui-combobox" style="width:200px;height:22px;" data-options="panelHeight:125,valueField:'user',textField:'name'">
                        <option value="">---</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->logged }}">{{ ucwords($user->name) }}</option>
                        @endforeach
                    </select>
                    <span class="mr-2"></span>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Search'" style="height:22px;width: auto;" onclick="filterAssessmentScoreAudtiLog()">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Clear'" style="height:22px;width: auto;" onclick="resetFilterAssessmentScoreAudtiLog()">Batal</a>
                    </form>
                </div>
                <div class="col-12 mt-2">
                    <table id="tb-assessment-score-audit-log" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                        data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                        <thead>
                            <tr>
                                <th data-options="field:'timestamp',width:150">Tanggal</th>
                                <th data-options="field:'info',width:550,resizeable:true">Info</th>
                                <th data-options="field:'score_before',width:80,resizeable:true,align:'center'">Sebelum</th>
                                <th data-options="field:'score_after',width:80,resizeable:true,align:'center'">Sesudah</th>
                                <th data-options="field:'reason',width:200,resizeable:true">Alasan</th>
                                <th data-options="field:'logged',width:150,resizeable:true">Pengguna</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var d = new Date()
    $(function () {
        $("#AssessmentScoreAuditMonth").combobox("setValue", d.getMonth() + 1)
        $("#tb-assessment-score-audit-log").datagrid({
            url: '{{ url('academic/assessment/score/audit/data') }}',
            queryParams: { 
                _token: '{{ csrf_token() }}', 
                fmonth: $('#AssessmentScoreAuditMonth').combobox('getValue'), 
                fyear: $('#AssessmentScoreAuditYear').datebox('getValue') 
            }
        })
    })
    function filterAssessmentScoreAudtiLog() {
        $("#tb-assessment-score-audit-log").datagrid("load", { 
            _token: "{{ csrf_token() }}", 
            fuser: $("#AssessmentScoreAuditUser").combobox("getValue"),
            fmonth: $("#AssessmentScoreAuditMonth").combobox("getValue"), 
            fyear: $('#AssessmentScoreAuditYear').combobox("getValue"),
        })
    }
    function resetFilterAssessmentScoreAudtiLog() {
        $("#form-assessment-score-audit").form("reset")
        $("#AssessmentScoreAuditMonth").combobox("setValue", d.getMonth() + 1)
        $("#tb-assessment-score-audit-log").datagrid("load", { 
            _token: "{{ csrf_token() }}", 
            fmonth: $("#AssessmentScoreAuditMonth").combobox("getValue"), 
            fyear: $("#AssessmentScoreAuditYear").combobox("getValue"),
        })
    }
</script>