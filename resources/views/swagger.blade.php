<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('swagger-ui.css') }}">
    <style>
        /* Your additional custom styles */
        body {
            margin: 0;
            padding: 0;
        }
        #swagger-ui {
            display: block;
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="{{ asset('swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('swagger-ui-standalone-preset.js') }}"></script>
    <script>
        window.onload = function () {
            const ui = SwaggerUIBundle({
                url: "{{ asset('openapi.yaml') }}", // Path to your OpenAPI specification file
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout",
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ]
            });

            window.ui = ui;
        };
    </script>
</body>
</html>
