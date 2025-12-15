<script>
    $(document).ready(function() {
        // ตรวจสอบว่ามี Canvas element อยู่หรือไม่
        if (document.getElementById('myCanvas')) {
            var obj = {
                values: [
                    <?php echo $onlineUsersDay; ?>,
                    <?php echo (!empty($onlineUsersWeek) ? $onlineUsersWeek[0]->user_count : 0); ?>,
                    <?php echo $onlineUsersCount; ?>
                ],
                colors: ['#F29026', '#3CB5F2 ', '#73AF49'],
                animation: true,
                animationSpeed: 10,
                fillTextData: false,
                fillTextColor: '#fff',
                fillTextAlign: 1.35,
                fillTextPosition: 'inner',
                doughnutHoleSize: 50,
                doughnutHoleColor: '#fff',
                offset: 0,
                pie: 'normal',
                isStrokePie: {
                    stroke: 20,
                    overlayStroke: true,
                    overlayStrokeColor: '#eee',
                    strokeStartEndPoints: 'Yes',
                    strokeAnimation: true,
                    strokeAnimationSpeed: 40,
                    fontSize: '60px',
                    textAlignement: 'center',
                    fontFamily: 'Arial',
                    fontWeight: 'bold'
                }
            };

            var values = obj.values;
            var colors = obj.colors;

            for (var i = 0; i < values.length; i++) {
                var cardId = "card" + values[i];
                var card = $("#" + cardId);
                if (card.length > 0) {
                    card.css("background-color", colors[i]);
                }
            }

            generatePieGraph('myCanvas', obj);
        } else {
            console.log('Canvas myCanvas not found in online users chart - skipping');
        }
    });
</script>