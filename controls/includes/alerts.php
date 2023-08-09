
    <?php if ($data_account['address'] == "" && $userInSession != "") { ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning left-icon-big alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button>
                    <div class="media">
                        <div class="alert-left-icon-big">
                            <span><i class="mdi mdi-help-circle-outline"></i></span>
                        </div>
                        <div class="media-body">
                            <h5 class="mt-1 mb-2">Hello <?php echo ucwords($data_account['first_name'])?>!</h5>
                            <p class="mb-0"><small>Your profile is not yet completed, <a href="profile.php">Click here to finish registration.</a> </small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    <?php } ?>