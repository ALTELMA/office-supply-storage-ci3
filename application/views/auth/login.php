<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $text_please_sign_in; ?></h3>
                </div>
                <div class="panel-body">
                    <?php if($this->session->flashdata('error')): ?>
                        <span class="text-danger">
                            <?php echo $this->session->flashdata('error'); ?>
                        </span>
                    <?php endif; ?>
                    <?php echo form_open('auth/postLogin') ?>
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="<?php echo $text_username; ?>" name="username" type="text" autofocus="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="<?php echo $text_password; ?>" name="password" type="password" value="">
                            </div>
                            <button class="btn btn-lg btn-success btn-block"><?php echo $text_login; ?></button>
                        </fieldset>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
