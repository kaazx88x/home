<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{trans('localize.lucky_draw')}}</title>
    <link href="/backend/css/bootstrap.min.css" rel="stylesheet">
    <script src="/backend/js/jquery-2.1.1.js"></script>
    <script src="/backend/js/bootstrap.min.js"></script>
    <script src="/backend/js/plugins/JQuery-qrcode/qrcode.js"></script>
</head>

<body onload="window.print()">
{{--  <body>  --}}
<div class="container">
    <div class="row">
        @for($i = 0; $i < $total; $i++)
        <div class="col-xs-6" style="padding-bottom: 50px;">
            <div id="qr_output_{{$i}}" style="padding-left:10px;">
                <script>
                    $(document).ready(function(){
                        generate_qr('qr_output_{{$i}}', '{{ url("/api/v2/member/lucky-draw/redeem/sorry") }}');
                    });
                </script>
            </div>
        </div>
        @endfor
    </div>
</div>
<img id='my-image' src="{{ url('assets/images/meihome_logo.png') }}" style="display:none;"/>
<script>
    function generate_qr(inputName, url) {
        var options = {
            // render method: 'canvas', 'image' or 'div'
            render: 'canvas',

            // version range somewhere in 1 .. 40
            minVersion: 6,
            maxVersion: 15,

            // error correction level: 'L', 'M', 'Q' or 'H'
            ecLevel: 'L',

            // offset in pixel if drawn onto existing canvas
            left: 0,
            top: 0,

            // size in pixel
            size: 300,

            // code color or image element
            fill: '#000',

            // background color or image element, null for transparent background
            background: '#FFF',

            // content
            text: url,

            // corner radius relative to module width: 0.0 .. 0.5
            radius: 0.4,

            // quiet zone in modules
            quiet: 0,

            // modes
            // 0: normal
            // 1: label strip
            // 2: label box
            // 3: image strip
            // 4: image box
            mode: 4,

            mSize: 0.10,
            mPosX: 0.5,
            mPosY: 0.5,

            label: 'no label',
            fontname: 'sans',
            fontcolor: '#000',

            image: $('#my-image')[0]
        };

        $('#' + inputName).qrcode(options);
    }
</script>
</body>
</html>
