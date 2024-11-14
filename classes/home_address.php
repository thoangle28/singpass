<div class="accordion-item" data-order="row-item-<?php print $id;?>">
    <h2 class="accordion-header" id="heading<?php print $id; ?>">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse<?php print $id; ?>" aria-expanded="false" aria-controls="collapse<?php print $id; ?>">
            <strong>Home Address <?php print($id + 1); ?></strong>
        </button>
        <span class="remove" title="Click to remove">X</span>
    </h2>

    <div id="collapse<?php print $id; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php print $id; ?>" data-bs-parent="#mc-accordion">
        <div class="accordion-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Property Type</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <input type="text" value="<?php print $type_id; ?>" name="address_type_id" class="form-control d-none">                               
                                <?php
                                $args = ['HDB' => 'HDB', 'Private Residential' => 'Private Residential'];
                                $propertyType = '';//($hdbtype > 120) ? 'Private Residential' : 'HDB';
                                ?>
                                <?php create_select_control($args, 'property_type', $propertyType); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Housing Type</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group" data-default="<?php print $hdbtype;?>" data-property="<?php print $property;?>">
                                <?php
                                $selected = '';
                                print housingType('housing_type', $selected, '', 'required');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-6">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end"><label class="col-form-label">Existing Staying</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <?php $args = [1 => 'Yes', 0 => 'No'];
                                $select = 1;
                                ?>
                                <?php create_select_control($args, 'existing_staying', $select); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end"><label class="col-form-label required">Residential Status</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <?php $args = [
                                    'Self-Owned' => 'Self-Owned',
                                    'Rental' => 'Rental',
                                    'Loan / Mortgaged' => 'Loan / Mortgaged',
                                    'Living with parents' => 'Living with parents',
                                    'Living with employer' => 'Living with employer'
                                ];
                                $select = '';
                                ?>
                                <?php create_select_control($args, 'home_ownership', $select, 'required'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-lg-6 d-flex-end "><label class="col-form-label">Unit</label></div>
                        <div class="col-12 col-sm-8 col-lg-6">
                            <div class="input-group">
                                <?php $selected = ''; ?>
                                <input type="text" class="form-control" name="unit" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Block</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <?php $selected = ''; ?>
                                <input type="text" class="form-control" name="block" required value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Building</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="building" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-4 row align-items-center">
                        <div class="col-12 col-sm-4 col-lg-2 d-flex-end"><label class="col-form-label required">Street</label></div>
                        <div class="col-12 col-sm-8 col-lg-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="street" required value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-xl-6 col-xxl-4">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-6 d-flex-end "><label class="col-form-label required">Postal</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="postal" required value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4 d-flex-end"><label class="col-form-label required">Country</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">
                            <div class="input-group">
                                <?php listDropDownCountries('country', $selected, 'required'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-6 col-xxl-5">
                    <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-2 col-xl-2 col-xxl-4 d-flex-end"><label class="col-form-label">Address Label</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-10 col-xl-10 col-xxl-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="address_label" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>