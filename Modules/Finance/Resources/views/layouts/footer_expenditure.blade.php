<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      body {margin: 0;padding: 0;font-size: 11px;font-family: "Segoe UI", "Open Sans", serif !important;}
      .text-left {text-align: left;}
      .text-center {text-align: center;}
      .text-right {text-align: right;}
      table.no-border, table.no-border th, table.no-border td {border: none;}
      table {border-collapse: collapse;border: 1px solid #000;font-size:14px;page-break-inside: auto;}
      tr {page-break-inside: avoid;page-break-after: auto;}
      th, td {border: 1px solid #000;padding: 3px;}
    </style>
    <script>
      function subst() {
        var vars = {};
        var query_strings_from_url = document.location.search.substring(1).split('&');
        for (var query_string in query_strings_from_url) {
          if (query_strings_from_url.hasOwnProperty(query_string)) {
            var temp_var = query_strings_from_url[query_string].split('=', 2);
            vars[temp_var[0]] = decodeURI(temp_var[1]);
          }
        }
        var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
        for (var css_class in css_selector_classes) {
          if (css_selector_classes.hasOwnProperty(css_class)) {
            var element = document.getElementsByClassName(css_selector_classes[css_class]);
            for (var j = 0; j < element.length; ++j) {
              element[j].textContent = vars[css_selector_classes[css_class]];
            }
          }
        }
        if (vars['page'] === vars['topage']) {
          document.getElementById('values').style.visibility = 'visible';
          document.getElementById('sign').style.visibility = 'visible';
        }
      }
    </script>
  </head>
  <body onload="subst()">
    <div id="values" style="margin-bottom:5px;visibility: hidden;">
      <table class="table" width="100%" style="font-size: 12px;">
        <tbody>
          <tr>
            <td colspan="4"><b><i>{{ ucwords($values['counted']) }} Rupiah</i></b> </td>
            <td class="text-right" width="15%"><b>{{ $receipt_majors->total }}</b></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="sign" style="visibility: hidden;">
      <table class="table" width="100%">
        <tbody>
          <tr>
            <td class="text-center" width="33%">
              Menyetujui
              <br/>
              <br/>
              <br/>
              __________________________________
            </td>
            <td class="text-center">
              Staf Keuangan
              <br/>
              <br/>
              <br/>
              {{ $receipt_majors->employee }}
            </td>
            <td class="text-center">
              Penerima
              <br/>
              <br/>
              <br/>
              {{ $receipt_majors->received_name }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div>
      <table style="border-bottom: 1px solid black; width: 100%;font-size: 12px;">
        <tr>
          <td style="text-align:right">
            Halaman <span class="page"></span> dari <span class="topage"></span>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>