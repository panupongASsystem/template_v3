<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>ฟอร์มเรียกดูเอกสารตามวัน/เดือน/ปี</h2>
        </div>
        <div class="col-sm-10">
            <form action="<?php echo site_url('report/getform') ;?>" method="post" class="form-horizontal">
                <div class="form-group">
                    <div class="col-sm-1">
                        start
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="ds" class="form-control" required>
                    </div>
                    <br>
                    <div class="col-sm-1">
                        end
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="de" class="form-control" required>
                    </div>
                    <br>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-info" >ดูเอกสาร</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
