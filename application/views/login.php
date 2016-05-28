<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <?php echo form_open('user/verifylogin') ?>
                        <fieldset>
                            <div class="form-group">
                            <input class="form-control" placeholder="Username" name="username" type="text" autofocus="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <button class="btn btn-lg btn-success btn-block">Login</button>
                        </fieldset>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>