<?php $this->layout('layout', ['title' => 'security']) ?>
<!DOCTYPE html>
<html lang="en">
<?php if(isset($_SESSION['secruity_danger'])):?>
							<div class="alert alert-danger text-dark" role="alert">
								<?php echo $_SESSION['secruity_danger'];
								unset($_SESSION['secruity_danger']);?>
								<?php endif;?>
                                <?php if (isset($_SESSION["secruity_sucsess"])):?>
                                    <div class="alert alert-sucsess text-dark" role="alert">
                                        <?php echo $_SESSION["secruity_sucsess"];
                                                 unset($_SESSION["secruity_sucsess"]);

                                        ?>
                                        <?php endif;?>
        <form action="update_security" method="POST">
            <div class="row">
                <div class="col-xl-6">
                    <div id="panel-1" class="panel">
                        <div class="panel-container">
                            <div class="panel-hdr">
                                <h2>Обновление эл. адреса и пароля</h2>
                            </div>
                            <div class="panel-content">
                                <input type="hidden" name="id" value="<?=$values['id'];?>">
                                <!-- email -->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Email</label>
                                    <input type="text" name="email" id="simpleinput" class="form-control" value="<?=$values['email'];?>">
                                </div>

                                <!-- password -->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Cтарый пароль</label>
                                    <input type="password" name="old_password" id="simpleinput" class="form-control">
                                </div>

                                <!-- password confirmation-->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Новый пароль</label>
                                    <input type="password" name="new_password" id="simpleinput" class="form-control">
                                </div>


                                <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                    <button class="btn btn-warning">Изменить</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script src="js/vendors.bundle.js"></script>
    <script src="js/app.bundle.js"></script>
    <script>

        $(document).ready(function()
        {

            $('input[type=radio][name=contactview]').change(function()
                {
                    if (this.value == 'grid')
                    {
                        $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-g');
                        $('#js-contacts .col-xl-12').removeClassPrefix('col-xl-').addClass('col-xl-4');
                        $('#js-contacts .js-expand-btn').addClass('d-none');
                        $('#js-contacts .card-body + .card-body').addClass('show');

                    }
                    else if (this.value == 'table')
                    {
                        $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-1');
                        $('#js-contacts .col-xl-4').removeClassPrefix('col-xl-').addClass('col-xl-12');
                        $('#js-contacts .js-expand-btn').removeClass('d-none');
                        $('#js-contacts .card-body + .card-body').removeClass('show');
                    }

                });

                //initialize filter
                initApp.listFilter($('#js-contacts'), $('#js-filter-contacts'));
        });

    </script>
</body>
</html>