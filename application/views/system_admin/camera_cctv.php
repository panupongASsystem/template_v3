     <!DOCTYPE html>
     <html>

     <head>
         <title>การถ่ายทอดสดด้วย Video.js</title>
         <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet">
         <script src="https://vjs.zencdn.net/7.14.3/video.js"></script>
     </head>

     <body>
         <video id="my-video" class="video-js" controls preload="auto" width="640" height="360" data-setup="{}" autoplay>
             <source src="<?php echo $rsedit->camera_api; ?>" type="application/x-mpegURL">
         </video>
     </body>

     </html>

             <!-- <!DOCTYPE html>
        <html>

        <head>
            <title>การถ่ายทอดสดด้วย Video.js</title>
            <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet">
            <script src="https://vjs.zencdn.net/7.14.3/video.js"></script>
        </head>

        <body>
            <video id="my-video" class="video-js" controls preload="auto" width="640" height="360" data-setup="{}" autoplay>
                <source src="http://hls.assystem.co.th:8080/hls/cctv1_1.m3u8" type="application/x-mpegURL">
            </video>
        </body>
        </html> -->