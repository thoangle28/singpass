<div class="accordion-item" data-order="row-item-<?php print $id;?>">
    <h2 class="accordion-header" id="headingWork<?php print($id + 1); ?>">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#work<?php print($id + 1); ?>" aria-expanded="false" aria-controls="work<?php print($id + 1); ?>">
            <strong>Work Address <?php print($id + 1); ?></strong>
        </button>
        <span class="remove" title="Click to remove">X</span>
    </h2>
    <div id="work<?php print($id + 1); ?>" class="accordion-collapse collapse" aria-labelledby="headingWork<?php print($id + 1); ?>" data-bs-parent="#mc-work-address">
        <div class="accordion-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-48 col-lg-4 d-flex-end "><label class="col-form-label">Default work</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input form-control" type="checkbox" id="defaultWork" name="is_default" value="1">
                                    <label class="form-check-label" for="defaultWork">Yes</label>
                                    <input type="text" value="<?php print $type_id;?>" name="address_type_id" class="form-control d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-6 d-flex-end "><label class="col-form-label">Unit</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="unit" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-48 col-lg-4 d-flex-end "><label class="col-form-label required">Block</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="block" required value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-48 col-lg-4 d-flex-end "><label class="col-form-label">Building</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="building" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="mb-4 row align-items-center">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-2 d-flex-end"><label class="col-form-label required">Street</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="street" required value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-6 d-flex-end "><label class="col-form-label required">Postal</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="postal" required value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-48 col-lg-4 d-flex-end "><label class="col-form-label required">Country</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <?php $selected = ''; ?>
                                <?php listDropDownCountries('country', $selected, 'required'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-48 col-lg-4 d-flex-end "><label class="col-form-label">Address Label</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <?php $args = ['Office' => 'Office', 'Work Site' => 'Work Site'];
                                $select = '';
                                ?>
                                <?php create_select_control($args, 'address_label', $select); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>