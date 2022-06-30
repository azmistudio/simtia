<html>
    <head>
        <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Statistik Penerimaan Calon Santri</title>
        <style type="text/css">
            body {width: 100%;height: 100%;margin: 0;padding: 0;font-family: "Segoe UI", "Open Sans", serif !important;}
            * {box-sizing: border-box;-moz-box-sizing: border-box;}
            #header {margin-bottom: 10px;background-color: #fff;}
            .text-left {text-align: left;}
            .text-center {text-align: center;}
            .text-right {text-align: right;}
            .break {page-break-before: avoid;}
            .must-break {page-break-before: always;}
            .center {margin: 0 auto;position: relative;display: flex;justify-content: center;}
            table.no-border, table.no-border th, table.no-border td {border: none;}
            table {border-collapse: collapse;border: 1px solid #000;}
            th, td {border-top: 1px solid #000;}
            .page {width: 297mm;min-height: 210mm;padding: 5mm;margin: 10mm auto;margin-top: 0;background: white;box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);}
            .subpage {padding: 0.75cm;}
            .button {background-color: #EEE;border: none;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;}
            @page {size: A4 landscape;margin: 0;}
            @media print {html, body {width: 297mm;height: auto;}.page {margin: 0;border: initial;border-radius: initial;width: initial;min-height: initial;box-shadow: initial;background: initial;page-break-after: always;}#btn-print {display: none;}}
        </style>
        <script src="{{ asset('lib/jquery-easyui/jquery.min.js') }}"></script>
        <script src="{{ asset('js/highcharts.src.js') }}"></script>
    </head>
    <body style="">
        <div class="text-center">
            <button id="btn-print" type="button" class="button" onclick="window.print()" style="margin-bottom: 10px;">Cetak Laporan</button>
        </div>
        <div class="page">
            <div class="subpage">
                <div id="header">
                    <table class="table no-border" style="width:100%;">
                        <tbody>
                            <tr>
                                <th rowspan="2" style="width:10%;"><img src="{{ $profile['logo'] }}" height="80px" /></th>
                                <td><b>{{ strtoupper(Session::get('institute')) }}</b></td>
                            </tr>
                            <tr>
                                <td style="font-size:11px;">
                                    {{ $profile['address'] }}<br/>
                                    Telp. {{ $profile['phone'] }} - Fax. {{ $profile['fax'] }}<br/>
                                    Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr/>
                <div id="body">
                    <br/>
                    <div class="text-center" style="font-size:16px;"><b>Statisik Penerimaan Calon Santri</b></div>
                    <br/>
                    <div>
                        <table class="table no-border">
                            <tbody style="font-size:13px;font-weight: 700;">
                                <tr>
                                    <td>Departemen</td>
                                    <td style="width:30px;text-align:center;">:</td>
                                    <td>{{ $payloads->department }}</td>
                                </tr>
                                <tr>
                                    <td>Proses Penerimaan</td>
                                    <td style="width:30px;text-align:center;">:</td>
                                    <td>{{ $payloads->admission }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <table class="no-border" style="width:100%">
                        <tbody>
                            <tr>
                                <td width="60%"><div id="bar-admission-stat" style="height: 250px;width: 100%;border: solid 1px #d5d5d5;"></div></td>
                                <td><div id="pie-admission-stat" style="height: 250px;width: 100%;border: solid 1px #d5d5d5;"></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    <table class="table" style="width:100%;font-size: 14px;">
                        <thead>
                            <tr>
                                <th class="" style="width:5%;">No.</th>
                                <th class="text-left">{{ $subtitle }}</th>
                                <th class="">Jumlah</th>
                                <th class="">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($rows as $val)
                              <tr>
                                <td class="text-center">{{ $no }}</td>
                                <td>{{ $val['subject'] }}</td>
                                <td class="text-center">{{ $val['total'] }}</td>
                                <td class="text-center">{{ $val['percent'] }}</td>
                              </tr> 
                              @php $no++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                var category = [
                    @foreach ($rows as $val)
                    "{{ $val['subject'] }}",
                    @endforeach
                ]
                var result = [
                    @foreach ($rows as $val)
                    { y: {{ $val['total'] }}, label: "{{ $val['subject'] }}" },
                    @endforeach
                ]
                $('#bar-admission-stat').highcharts({
                    chart: { type: 'column' },
                    title: {
                        text: '<b>Berdasarkan {{ $subtitle }}</b>',
                        style: { fontSize: '14px' }
                    },
                    xAxis: { categories: category },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah',
                            align: 'high'
                        },
                        labels: { overflow: 'justify' },
                        tickWidth: 1,
                    },
                    plotOptions: {
                        column: {
                            dataLabels: { enabled: true },
                            showInLegend: true
                        },
                        series: { cursor: 'pointer', enableMouseTracking: false, shadow: false, animation: false }
                    },
                    series: [{
                        name: "Jumlah",
                        data: result
                    }]
                })
                $('#pie-admission-stat').highcharts({
                    chart: { type: 'pie' },
                    title: {
                        text: '<b>% {{ $subtitle }}</b>',
                        style: { fontSize: '14px' }
                    },
                    tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.percentage:.1f} %<br/>({point.label})',
                            },
                        },
                        series: { cursor: 'pointer' }
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                        name: "Jumlah",
                        colorByPoint: true,
                        data: result
                    }]
                })
                console.log(result)
            })
        </script>
    </body>
</html>