# Render Charts

Laravel charts can be used without any rendering on the PHP side. Meaning it can be used and server as an API endpoint. There's no need to modify the configuration files or the chart to do such.

However, if you do not plan to develop the front-end as a SPA or in a different application and can use the
laravel Blade syntax, you can then use the `@chart` helper to create charts.

Keep in mind that you still need to import Chartisan and it's front-end library of your choice as explained in the [Chartisan](https://chartisan.dev) docs. The `@chart` blade helper does accept a string containing the
chart name to get the URL of. The following example can be used as a guide:

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chartisan example</title>
  </head>
  <body>
    <!-- Chart's container -->
    <div id="chart" style="height: 300px;"></div>
    <!-- Charting library -->
    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <!-- Your application script -->
    <script>
      const chart = new Chartisan({
        el: '#chart',
        url: "@chart('sample_chart')",
      });
    </script>
  </body>
</html>
```
