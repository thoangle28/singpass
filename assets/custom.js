(function ($) {
  //variable to processing form
  var applicationForm = {};

  $(document).ready(function () {
    //stickyMenuBar();
    singPassConfirm();
    saveAuthCode();
    cancel(); //cancel
    addMoreAddress();
    validateControl();
    stepPersonal(); //step 1
    stepContact(); //step 2
    stepEmployment(); //step 3
    stepLoanDetails(); //step 4
    stepBankInfo(); //step 5
    stepSurety(); //step 6
    stepCompletion(); //step 7
    receiptDownloadPDF();
    stepContinueStep8(); // upload multiple files
    tabSectionMobile();
  });

  function singPassConfirm() {
    $("#company").change(function () {
      val = $(this).val();
      if (val) $(this).removeClass("border-danger");
      else $(this).addClass("border-danger");
    });

    $("#singPassLogin").click(function (e) {
      e.preventDefault();
      $cookie = $("#singPassConfirm").val();
      if (!$cookie) return;
      //set cookie to confirm & get personal data from singpass
      setCookie("mc_singpass_verify", $cookie, 1);
      url = $("#singPassUrl").val();
      window.location.href = url;
    });

    $("#loginWithNricNo #loginNormal").click(function (e) {
      button = $(this);
      e.preventDefault();
      
      fields = $('#loginWithNricNo input');
      allowLogin = true;
      fields.each(function(item){
        value = $(this).val();
        value = value.trim();
        if( !value ) {
          $(this).addClass('error');
          allowLogin = false;
        } else $(this).removeClass('error');
      });
      
      phone = $('#phonenumber').val();
      if( phone.length > 0 ) {
        condition = isNumber(phone);
        condition = condition && phone.length >= 8;
        allowLogin = condition;
        showMessage(
          $('#phonenumber'),
          condition,
          "Incorrect phone number"
        );

        if( !condition ) return;
      } else return;

      nricNo = $('#nricNo').val();   
      
      confirmed = validateNric(button, nricNo);
      
      //$.when(validateNric_).then(function(res){
      //  const { code, message, confirmed } = res;        
        if( allowLogin && confirmed) {
          nricNo = $('#nricNo').val();      
          $.ajax({
            type: "POST",
            url: mc_ajax.ajax_url,
            dataType: "json",
            data: {
              action: "login_verification", //phone_verification
              phonetocheck: phone,
              nric_no: nricNo
            },
            beforeSend: function() {
              button.text('Processing').attr('disabled','disabled');   
            },
            complete: function() {
              button.text('Login').removeAttr('disabled');   
            },
            success: function (response) {
              if (
                response.data.show_popup == 1 ||
                response.data.show_popup == "1"
              ) {                
                Swal.fire({
                  title: "Validate OTP (One Time Passcode)",
                  html: 'A One Time Passcode has been sent to <b>+65' + phone + 
                        '</b>. Please check your phone and enter the OTP in the field below to verify your phone number.' +
                        '<br />You have <span class="text-danger" id="countTimePhone">300</span> seconds left to enter the OTP and verify.',
                  input: "text",
                  customClass: 'swal-wide',
                  timer: 5*60000,
                  timerProgressBar: false,
                  showCancelButton: true,
                  confirmButtonText: "Confirm OTP",
                  showLoaderOnConfirm: true,
                  didOpen: () => {
                    const time = Swal.getPopup().querySelector("#countTimePhone");
                    let timeCountDown = 5*60;
                    timerInterval = setInterval(() => {
                      timeCountDown = timeCountDown - 1;
                      time.textContent = timeCountDown;
                    }, 1000);
                  },
                  preConfirm: async (optCode) => {    
                    if( !optCode ) {
                      return Swal.showValidationMessage('Please enter the OTP sent to your phone');
                    }         
                    
                    $.ajax({
                      type: "POST",
                      url: mc_ajax.ajax_url,
                      dataType: "json",
                      data: {
                        action: "verify_otp_login",
                        verfiy_info: {
                          phone: phone,
                          otp_verify: optCode,
                          nricno : nricNo,
                          application_id: null,
                        },
                      },
                      success: function (response) {  
                        if (
                          response.data.confirmed == 1 ||
                          response.data.confirmed == "1"
                        ) {
                          Swal.fire({
                            title: 'Login successfull!',
                            text: "Please wait 3 seconds, the browser is redirecting.",
                            icon: 'success',
                            allowOutsideClick: false,
                            showConfirmButton: false
                          });    
                          //console.log(response);
                          setTimeout(function(){
                            rand_ = Math.floor(Math.random()*100000000);
                            window.location.href = "/application-form?" + rand_;
                          }, 3000);       
                        } else {
                          Swal.showValidationMessage(response.message);    
                        }                
                      }
                    }); 

                    return false;
                  },
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  willClose: () => {
                    clearInterval(timerInterval);
                    button.text('Login').removeAttr('disabled');
                  }
                });
              } else {
                Swal.fire({
                  title: "Login without SingPass",
                  html: response.message,
                  showCancelButton: false,
                  icon: "error"
                });
                button.text('Login').removeAttr('disabled');
              }         
            },
            error: function (response) {
            },
          });
        }
      //});
    });
  }

  function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie =
      cname + "=" + cvalue + ";" + expires + ";path=/mc-application/";
    document.cookie =
      cname + "=" + cvalue + ";" + expires + ";path=/application-form/";
  }

  function saveAuthCode() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const authCode = urlParams.get("code");
    if (authCode) setCookie("mc_singpass_code", authCode, 1);

    const act = urlParams.get("act");
    if (window.location.search && !act) {
      const url = window.location.origin + window.location.pathname;
      window.history.replaceState({}, document.title, url);
    }
  }

  function addMoreAddress() {
    removeItem("#mc-accordion");
    addMoreHomeAndWork(
      "#addMoreHomeAddress",
      "#mc-accordion",
      "Home Address ",
      "Home"
    );

    removeItem("#mc-work-address");
    addMoreHomeAndWork(
      "#addMoreWorkAddress",
      "#mc-work-address",
      "Work Address ",
      "Work"
    );

    changHouseType();
  }

  function addMoreHomeAndWork(buttonId, sectionId, sectionTitle, section) {
    $(buttonId).click(function () {
      button = $(this);
      button.text("Processing...");
      totalItem = $(sectionId + " .accordion-item").length || 0;
      latestItem = $(sectionId + " .accordion-item").last();
      cloneItem = null;

      type_id =
        sectionId === "#mc-work-address"
          ? $("#work_address_type_id").val()
          : $("#home_address_type_id").val();
      hdbtype =
        sectionId === "#mc-accordion"
          ? $("[data-default]", $("#mc-accordion")).attr("data-default")
          : 0;
      property =
        sectionId === "#mc-accordion"
          ? $("[data-property]", $("#mc-accordion")).attr("data-property")
          : "";

      $.ajax({
        type: "POST",
        url: mc_ajax.ajax_url,
        data: {
          action: "home_address",
          id: totalItem,
          type: sectionId,
          type_id: type_id,
          hdbtype: hdbtype,
          property: property,
        },
        success: function (response) {
          $(sectionId + " .accordion-items").append(response);
          button.text("+ Add Another Address");
          cloneItem = $(sectionId + " .accordion-item").last();
          headingId = "heading" + section + totalItem;
          itemId = "collapse" + section + totalItem;
          $(".accordion-header", cloneItem).attr("id", headingId);

          $(".accordion-collapse", cloneItem)
            .attr("aria-labelledby", headingId)
            .attr("id", itemId);

          $(".accordion-button", cloneItem)
            .attr("data-bs-target", "#" + itemId)
            .attr("aria-controls", itemId)
            .html("<strong>" + sectionTitle + (totalItem + 1) + "</strong>")
            .trigger("click");
          //reset input
          $("input", cloneItem).val("");
          $('select', cloneItem).val("");
          $('select[name="existing_staying"]', cloneItem).val(1);    
          $('select[name="property_type"]', cloneItem).val('HDB');        
             
        },
      });
    });
  }

  function removeItem(accordion_id) {
    $("body").delegate(
      accordion_id + " .accordion-header span.remove",
      "click",
      function () {
        item = $(this);
        Swal.fire({
          title: "Are you sure?",
          text: "You want to remove this item.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            parent = item.parents(".accordion-item");
            parent.remove();
          }
        });
        /* if (confirm('Are you sure remove this item?')) {
        //$(this).closest('.accordion-item').remove();
        parent = item.parents('.accordion-item');
        parent.remove();
      } */
      }
    );
  }

  function changHouseType() {
    $("body").delegate(
      '.accordion-item select[name="property_type"]',
      "change",
      function () {
        value = $(this).val();
        if (value == "Private Residential") {
          value = "PR";
        }
        
        parent = $(this).parents(".accordion-item");
        $('select[name="housing_type"]', parent).val("");
        $('select[name="housing_type"] option', parent).addClass("d-none");
        $(
          'select[name="housing_type"] option[data-type="Blank"]',
          parent
        ).removeClass("d-none");
        $(
          'select[name="housing_type"] option[data-type="' + value + '"]',
          parent
        ).removeClass("d-none");

        selected = $('select[name="housing_type"]').parent(".input-group");
        property1 = selected.attr("data-property");
        property2 = $('select[name="property_type"]', parent).val();
        if (property1 == property2)
          $('select[name="housing_type"]', parent).val(selected.attr("data-default"));
      }
    );

    selected = $('select[name="housing_type"]').parent(".input-group");
    if (selected.length > 0)
      $('select[name="housing_type"]').val(selected.attr("data-default"));

    $("body").delegate(
      '[name="guarantor_info.property_type"]',
      "change",
      function () {
        value = $(this).val();
        if (value == "Private Residential") {
          value = "PR";
        }
        $('select[name="guarantor_info.housing_type"]').val("");
        parent = $(this).parents(".row");
        $('select[name="guarantor_info.housing_type"] option', parent).addClass(
          "d-none"
        );
        $(
          'select[name="guarantor_info.housing_type"] option[data-type="Blank"]',
          parent
        ).removeClass("d-none");
        $(
          'select[name="guarantor_info.housing_type"] option[data-type="' +
            value +
            '"]',
          parent
        ).removeClass("d-none");

        selected = $('select[name="guarantor_info.housing_type"]').parent(
          ".row"
        );
        property1 = selected.attr("data-property");
        property2 = $(
          'select[name="guarantor_info.property_type"]',
          parent
        ).val();
        if (property1 == property2)
          $('select[name="guarantor_info.housing_type"]').val(
            selected.attr("data-default")
          );
      }
    );

    selected = $('select[name="guarantor_info.housing_type"]').parent(
      ".input-group"
    );
    if (selected.length > 0)
      $('select[name="guarantor_info.housing_type"]').val(
        selected.attr("data-default")
      );
  }

  //steps submit form
  function chooseImagAndView() {
    uploadImagePreview();
    $(".upload-image").click(function () {
      $("#filePhoto").trigger("click");
    });

    $('#removeAvater').click(function(){      
      Swal.fire({
        title: 'Delete file!',
        text: 'Are you sure you want to delete the avatar?',
        icon: 'question',
        confirmButtonText: 'Yes, remove it',
        showCancelButton: true,
        customClass: {
          confirmButton: 'btn-danger',
          cancelButton: "btn-primary"
        }
      }).then((result) => {
        if(result.isConfirmed) {
          $("#filePhotoValue").val('');
          $('#removeAvater').addClass('d-none');
          url__ = '/wp-content/plugins/mc-application-form/assets/images/no_image.png';
          $("#imagePreview").attr("src", url__);
        }
      });
    });
  }

  function uploadImagePreview() {
    $("#filePhoto").on("change", function (event) {
      url__ = '/wp-content/plugins/mc-application-form/assets/images/no_image.png';
      // Get the selected file
      var file = event.target.files[0];
      var maxSize = 1 * 1024 * 1024; // 2MB

      // Check if the file is an image
      if (file && file.type.match("image.*")) {
        // Check file size
        if (file.size > maxSize) {
          $("#errorMessage").text("File size exceeds 2MB.");
          $("#imagePreview").attr("src", url__);
          return;
        }

        // Clear any previous error message
        $("#errorMessage").text("").addClass('d-none');;

        // Create a FileReader object
        var reader = new FileReader();
        // Set the onload function to display the image preview
        reader.onload = function (e) {
          fileBase64 = createFile(
            file.name,
            e.target.result,
            file.size,
            file.type,
            "personal"
          );
          var filesJSON = JSON.stringify(fileBase64);
          $("#filePhotoValue").val(filesJSON);
          $("#imagePreview").attr("src", e.target.result);
          $('#removeAvater').removeClass('d-none');
        };

        // Read the file as a Data URL
        reader.readAsDataURL(file);
      } else {
        // Clear the image preview and show an error message if the selected file is not an image
        $("#imagePreview").attr("src", url__);
        $("#errorMessage").text("Please select a valid image file.").removeClass('d-none');
        $('#removeAvater').addClass('d-none');
      }
    });
  }

  function uploadFilePreview(sectionId) {
    let filesPDF = [];
    let filePreview = [];

    $(sectionId + " .chooseFiles").click(function () {
      $(sectionId + " #doc_files").trigger("click");
    });

    $("body").delegate(
      sectionId + " #filePreview .file-preview span.delete",
      "click",
      function () {
        item = $(this);
        findParent = $(this).parents('.quick-file-uploader');
        //need read from old data
        tempFiles = $(sectionId + " #upload_files").val();

        if (tempFiles.length > 0) filesJSON = JSON.parse(tempFiles);
        Swal.fire({
          title: "Are you sure remove this file?",
          icon: "warning",
          showDenyButton: true,
          confirmButtonText: "Yes, remove it!",
          denyButtonText: "No",
          customClass: {
            confirmButton: "btn-danger",
            denyButton: "btn-primary",
          },
          allowOutsideClick: false,
        }).then((result) => {
          if (result.isConfirmed) {
            postion = item.attr("data-item");
            fname = item.attr("data-fname");           
            item.closest("li").remove();
            filesPDF = filesPDF.filter((item) => item.document_name !== fname );
            var filesJSON = JSON.stringify(filesPDF);
            filesJSON =
              filesJSON.length > 0 && filesJSON !== "[]" ? filesJSON : "";
            $(sectionId + " #upload_files").val(filesJSON);
            //call to function to updating files list
            fileObj = $('[name="doc_files"]', findParent);
            updateDocumentFilesCompletion(fileObj);    
          }
        });
      }
    );

    $(sectionId + " #doc_files").on("change", function (event) {
      let fileObj = $(this);
      // Get the selected file
      var fileList = event.target.files;
      var maxSize = 2 * 1024 * 1024; // 2MB
      filesPDF = [];
      filePreview = [];
      // Check if the file is a pdf
      const allowedTypes = ["application/pdf"];

      $check_files = true;
      for (const file of fileList) {
        if (!allowedTypes.includes(file.type)) {
         /*  alert(
            `File "${file.name}" could not be uploaded. Please select PDF file`
          ); */
          Swal.fire({
            title: "Phone Verification",
            text: 'File "' + file.name + '" could not be uploaded. Please select PDF file.',
            icon: "error",
          });
          $check_files = false;
        }
      }

      if (!$check_files) return;
      if (fileList.length > 0) {
        $(this).parents(".quick-file-uploader").removeClass("border-danger");
        $(this).parents(".quick-file-uploader").find('small.error').remove();
      }
      //fill to input
      i = 0;
      for (const file of fileList) {
        filePreview.push(
          '<li class="file-preview d-flex gap-2 mb-3"><span data-fname="' + file.name + '" data-item="' +
            i++ + '" title="Click to remove" class="delete"></span> <small>' +
            file.name +
            " (" +
            (file.size / 1024).toFixed(2) +
            "KB)</small></li>"
        );
        // Create a FileReader object
        var reader = new FileReader();
        // Set the onload function to display the image preview
        step = sectionId.replace("#step_", "");
        step = step === "loan" ? "loan_details" : step;
        step = step === "completion" ? "signature" : step;
        //employment , loan_details, guarantor
        reader.onload = function (e) {
          fileBase64 = createFile(
            file.name,
            e.target.result,
            file.size,
            file.type,
            step
          );
          filesPDF.push(fileBase64);
          var filesJSON = JSON.stringify(filesPDF);
          $(sectionId + " #upload_files").val(filesJSON);
        };
        // Read the file as a Data URL
        reader.readAsDataURL(file);
      }

      if (filePreview.length > 0) {
        $(sectionId + " .fileListPreview").removeClass("d-none");
        $(sectionId + " #filePreview").html(filePreview.join(""));
        updateDocumentFilesCompletion(fileObj);
      }
    });

    $('#supply_doc_offile').change(function(){
      obj = $('#step_employment .quick-file-uploader');

      if($(this).is(':checked')) {
        $('input[name="income_document_files"]').removeAttr('required');    
        $('.need-required', obj).removeClass('required');
        obj.removeClass('border-danger');   
        fileShowRequired(obj, false);
      } else {
        $('.need-required', obj).addClass('required');
        $('input[name="income_document_files"]').attr('required', 'required');
        obj.addClass('border-danger');
        fileShowRequired(obj, true);
      }
    });
  }

  function updateDocumentFilesCompletion(fileObj) {
    documentFiles = fileObj.next('.document_files');
    //console.log(documentFiles);
    file_name = documentFiles.attr('name');       
    switch(file_name) {
      case 'income_document_files':
        replace_for = 'income_documents';
      break;
      case 'document_files':
        replace_for = 'loan_documents';
      break;
      case 'guarantor_info.income_document_files':
        replace_for = 'guarantor_info.income_documents';
      break;
    }

    setTimeout(function(){
      docFiles = documentFiles.val();
      temp = (docFiles.length > 0 ) ? JSON.parse(docFiles) : [];
      mappingDocumentFiles(temp, $('[data-field="' + replace_for +'"]'));
    }, 1000);
  }

  function createFile(fname, base64, size, type, step) {
    step = step === "loan" ? "loan_details" : step;
    const fileBase64 = {
      document_name: fname || "",
      base64: base64,
      size: size,
      type: type,
      description: step,
    };

    return fileBase64;
  }

  function update_next_step(step__, status__, data__, next_step) {
    if (step__ != "contact") {
      $("#" + next_step + "-tab")
        .removeAttr("disabled")
        .trigger("click");
      $("html, body").animate(
        {
          scrollTop: $("#" + next_step).offset().top - 100,
        },
        1000
      );
    } else {
      $.ajax({
        type: "POST",
        url: mc_ajax.ajax_url,
        data: {
          action: "next_step",
          step: step__,
          status: status__,
          data: data__,
          next_step: next_step,
          nonce_field: "",
        },
        dataType: "json",
        success: function (response) {
          //console.log(response);
          if (response.code === 201) {
            $("#" + next_step + "-tab")
              .removeAttr("disabled")
              .trigger("click");
            $("html, body").animate(
              {
                scrollTop: $("#" + next_step).offset().top - 100,
              },
              1000
            );
          } else {
            //alert(response.message);
            Swal.fire({
              title: "Validate OTP",
              html: response.message,
              icon: "error",
            });

            $("html, body").animate(
              {
                scrollTop: $("#" + step__).offset().top - 100,
              },
              1000
            );
            return;
          }
        },
        error: function () {},
      });
    }
  }

  //Steps
  function updateApplicationForm(data) {
    applicationForm[data.step] = data;
    $("#v-pills-tab button#" + data.step + "-tab").attr(
      "data-next",
      "complete"
    );
    //console.log(applicationForm);
    percent = 0;
    step = 6;
    total = 0;
    personal = [];
    contact = [];
    loan = [];
    bank = [];
    employment = [];
    surety = [];

    total +=
      typeof applicationForm.personal !== "undefined" &&
      applicationForm.personal.status === "complete"
        ? 1
        : 0;
    total +=
      typeof applicationForm.contact !== "undefined" &&
      applicationForm.contact.status === "complete"
        ? 1
        : 0;
    total +=
      typeof applicationForm.loan !== "undefined" &&
      applicationForm.loan.status === "complete"
        ? 1
        : 0;
    total +=
      typeof applicationForm.employment !== "undefined" &&
      applicationForm.employment.status === "complete"
        ? 1
        : 0;
    total +=
      typeof applicationForm.bank !== "undefined" &&
      applicationForm.bank.status === "complete"
        ? 1
        : 0;
    total +=
      typeof applicationForm.guarantor !== "undefined" &&
      applicationForm.guarantor.status === "complete"
        ? 1
        : 0;

    percent = (total / step) * 100;
    percent = percent.toFixed(2);
    $(".application-status .progress-bar")
      .css({ width: percent + "%" })
      .attr("aria-valuenow", percent)
      .text(percent + "%");
  }

  function validateControl() {
    $('body').delegate("input, textarea, select", 'change', function () {
      value = $(this).val();
      value = value.trim();      
      if (value.length > 0) $(this).removeClass("error");
      parent = $(this).parents('.tab-pane');
      id = parent.attr('id');
      fname = $(this).attr('name');
      //select get text option
      if($(this).is('select')) {
        value = $('option:selected', this).text();
      }      
      //checkbox / radio
      if(($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')) {
        value = $(this).is(':checked') ? $(this).next('.form-check-label').text() : 'No';        
      }
      //phone
      if( $(this).hasClass('phone-number') && value.length > 0 ) {
        value = '+65' + value;
      }
      //phone
      if( (fname == 'installment' || $(this).hasClass('income')) && value.length > 0 ) {
        value = formatMoney(value);
      }

      //fname = (id == 'guarantor') ? "guarantor_info." + fname : fname;
     
      //check is home / work
      addressRows = $(this).parents('.mc-accordion');
      
      //clear
      if( value == 'Singapore NRIC No' || value == 'Singapore PR No') $('[data-field="identification_expiry"]').text('');

      if( addressRows.length <= 0) {
        $('[data-field="' + fname + '"]').text(value);       
      } else {    
        $('[data-field="' + fname + '"]').text(value);
        //find home / word address line i
        accordion = $(this).parents('.accordion-item');
        home_address = accordion.parents('.home-address');
        if( home_address.length > 0) {
          row_item = accordion.attr('data-order');
          $('#homeAddress .' + row_item + ' [data-field="' + fname + '"]').text(value);
        }

        work_address = accordion.parents('.work-address');
        if( work_address.length > 0) {
          row_item = accordion.attr('data-order');
          $('#workAddress .' + row_item + ' [data-field="' + fname + '"]').text(value);
        } 
      }     
    });

    $('input[type="email"]').on("change", function () {
      value = $(this).val();
  
      if (value.length > 0 && !validateEmail(value)) {
        $(this).addClass("error");
        labelError = $(this).next();
        if (labelError.length <= 0 || !labelError.hasClass("email-invalid"))
          $(this).after(
            '<span class="w-100 email-invalid text-danger position-absolute top-100 left-0"><small></small></span>'
          );
        labelError = $(this).next(".email-invalid");
        labelError.removeClass("d-none").find("small").text("Email is valid!");
      } else {
        __next = $(this).next();
        if(__next.attr('id') !== 'confirmedEmail') __next.addClass("d-none");
        $(this).removeClass("error");
      }
    });

    $('input[type="checkbox"]').on("change", function () {
      if ($(this).is(":checked"))
        $(this).closest(".error").removeClass("error");
    });

    $("input.phone-number").on("change", function () {
      value = $(this).val();
      if (value.length > 0 && !isValidPhoneNumber(value)) {
        //$(this).val('');
        return;
      }
    });

    $("#loan_type_id").change(function () {
      termUnit($(this).val(), $("#term_unit").val());
    });
  }

  function validateFields($fields_list, section = "", i) {
    var fields = $fields_list;
    checkedOk = true;
    data = {};
    //last Item
    latestItem = cloneItem = null;
    totalRow = $(section + " .row-item").length;
    //$(section + " .row-item").addClass('row-item-' + i);
    if (section !== "contact") {
      latestItem = $(section + " .row-item").last();
      cloneItem = i + 1 > totalRow ? latestItem.clone() : latestItem;
      cloneItem.removeClass('row-item-' + (i - 1));
      cloneItem.addClass('row-item-' + i);
    }

    fields.each(function () {
      reqAttr = $(this).attr("required");
      required = typeof reqAttr !== "undefined";

      var field_value = temp = $(this).val();
      field_value = (field_value && field_value.length > 0 ) ? temp.trim() : '';
      if ($(this).attr("type") === "checkbox")
        field_value = $(this).is(":checked") ? 1 : 0;

      if (field_value.length <= 0 && required) {
        checkedOk = false;
        $(this).addClass("error");
      } else {
        $(this).removeClass("error");
        //check if email
        if (
          $(this).attr("type") === "email" &&
          field_value.length > 0 &&
          !validateEmail(field_value)
        ) {
          checkedOk = false;
          $(this).addClass("error");
        }
        if (
          $(this).hasClass("phone-number") &&
          field_value.length > 0 &&
          !isValidPhoneNumber(field_value)
        ) {
          checkedOk = false;
          $(this).addClass("error");
        }

        field_value = isNumber(field_value) ? field_value - 0 : field_value;
        data[$(this).attr("name")] = field_value;
      }

      field_name = $(this).attr("name");
      field_value_1 = $(this).val();
      if ($(this).hasClass("phone-number")) {
        field_value_1 = field_value_1.length > 0 ? "(+65)" + field_value_1 : "";
      }
      
      if ($(this).is("select")) {
        field_value = $(this).find("option:selected").text();
        $('.contact-information [data-field="' + field_name + '"]').text(field_value);
      }

      if (section !== "contact") {
        if( field_name == "is_default" ) {
          field_value = ( field_value == 1) ? 'Yes' : 'No';
          field_name = "default_work";
        }
        $('[data-field="' + field_name + '"]', cloneItem).text(field_value);
      } else {        
        $('.contact-information [data-field="' + field_name + '"]').text(
          field_value_1
        );
      }
    }); //for

    if (section !== "contact") $(section).append(cloneItem);

    if (!checkedOk) return false;
    else return data;
  }

  function validateEmail(email) {
    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  function cancel() {
    $(".btnCancel").click(function () {
      window.location.href = "/application-form/?act=loan";
    });

    $("#noNewApplication").click(function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Create New Application",
        html: "The loan application is unable to submit now.<br />Please contact our friendly agent.",
        icon: "info",
      });
      return false;
    });
  }

  function stepPersonal() {
    chooseImagAndView();
    limitDate("identification_expiry"); 
    //updateFromNric();

    $("#identification_type").change(function () {
      value = $(this).val();
      if (value === "singapore_nric_no" || value === 'singapore_pr_no') {
        $("#identification_expiry")
          .attr("disabled", "")
          .removeAttr("required")
          .val("")
          .removeClass("error");
        $("#lbl_expiry_date").removeClass("required");
        $("#identification_expiry")
          .next("small.text-danger")
          .addClass("d-none");
      } else {
        $("#identification_expiry").attr("required", "").removeAttr("disabled");
        $("#lbl_expiry_date").addClass("required");
      }
    });

    $("#identification_expiry").change(function () {
      let expiryDate = $(this).val() ? checkExpiryDate($(this).val(), 2) : false;
      showMessage(
        $(this),
        expiryDate,
        "The expiration date must be greater than 02 months from the current date."
      );
    });
    
    $('#personal input[name="date_of_birth"]').change(function () {
      let age20 = $(this).val() ? calculateAge($(this).val()) : false;
      showMessage($(this), age20, "You must be 20 years old to apply.");
      if (!age20) checkedOk = false;
    });

    $("#btnContinueStep1").click(function (e) {
      e.preventDefault();

      var fields = $("#step_personal input, #step_personal select");
      checkedOk = true;
      data = {};
      fields.each(function () {
        reqAttr = $(this).attr("required");
        required = typeof reqAttr !== "undefined";

        field_value = $(this).val();
        field_value = field_value.trim();
        if (field_value.length <= 0 && required) {
          checkedOk = false;
          $(this).addClass("error");
        } else {
          $(this).removeClass("error");
        }
        //convert string to number
        field_value = isNumber(field_value) ? field_value - 0 : field_value;
        //dbo
        field_name = $(this).attr("name");
        if (
          ("filePhotoValue" === field_name || field_name == "cpf") &&
          field_value.length > 0
        ) {
          temp = JSON.parse(field_value);
          data[field_name] = temp;
        } else if (field_name != "legal_actions_against") data[field_name] = field_value;

        if ("date_of_birth" === field_name) {
          let age20 = field_value ? calculateAge(field_value) : false;
          showMessage($(this), age20, "You must be 20 years old to apply.");
          if (!age20) checkedOk = false;
        }

        if (
          "identification_expiry" === field_name &&
          $(this).attr("disabled") !== "disabled"
        ) {
          let expiryDate = field_value ? checkExpiryDate(field_value, 2) : false;
          showMessage(
            $(this),
            expiryDate,
            "The expiration date must be greater than 2 months from the current date."
          );
          if (!expiryDate) checkedOk = false;
        }

        if ($(this).is("select")) {
          field_value = $(this).find("option:selected").text();
        } else if ($(this).attr("type") === "checkbox") {
          field_value = field_value - 0 === 1 ? "Yes" : "No";
        } else if (
          (field_name === "date_of_birth" ||
            field_name === "identification_expiry") &&
          field_value.length > 0
        ) {
          field_value = formatDate(field_value);
        }

        if (field_name === "firstname") {
          fullname =
            $('#step_personal input[name="lastname"]').val() +
            " " +
            $('#step_personal input[name="firstname"]').val();
          $('.personal-information [data-field="name_of_applicant"]').text(
            fullname
          );
        } else if (field_name != "legal_actions_against") {          
          $('.personal-information [data-field="' + field_name + '"]').text( field_value );
        }
      });
      //legal_actions_against
      $('input[name="legal_actions_against"').each(function () {
        if ($(this).is(":checked")) {
          data["legal_actions_against"] = $(this).val();
          value = $(this).val();
          value = (value == 0 || value == "0") ? "No" : "Yes";
          $('.personal-information [data-field="legal_actions_against"]').text( value );
        }
      });
      //console.log(data);
      if (!checkedOk) return;

      //check NRIC
      id_no = $('[name="identification_no"]').val();
      cid_no = $('[name="identification_no_comfirm"]').val();
      if (id_no != cid_no) {
        //alert('NRIC No./FIN and Confirm NRIC do not match, please re-enter.');
        Swal.fire({
          title: "Data validation error",
          text: "NRIC No./FIN and Confirm NRIC do not match, please re-enter.",
          icon: "error",
        });
        $('[name="identification_no_comfirm"]').addClass("error");
        return;
      }

      let jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "personal",
      });
      //btoa(jsonString)
      update_next_step("personal", "complete", "", "contact");
    });
  }

  function stepContact() {
    checkPhoneEmail();
    
    $("body").delegate(
      "#step_contact input, #step_contact select",
      "change",
      function () {
        if ($(this).val()) $(this).removeClass("error");
      }
    );

    $("#btnContinueStep2").click(function (e) {
      e.preventDefault();
      next_step = true; tabs = [];
      objects = $(
        "#step_contact .contact-info input, #step_contact .contact-info select"
      );
      let contact = validateFields(objects, "contact", 0);
      if (!contact) return;

      home_type_id = $("#home_address_type_id").val();
      if (typeof home_type_id !== "undefined" && home_type_id > 0) {
        contact["home_address_type_id"] = home_type_id;
      }
 
      work_type_id = $("#work_address_type_id").val();
      if (typeof home_type_id !== "undefined" && work_type_id > 0) {
        contact["work_address_type_id"] = work_type_id;
      }

      //-------------------------------------------------
      //check home address
      home_address_rows = [];
      home_address_not_fill = true;
      if ($("#step_contact #mc-accordion").length > 0) {
        $("#step_contact #mc-accordion .accordion-item").each(function (i) {
          objects = $(".form-control", this);
          let address = validateFields(objects, "#homeAddress", i);          
          if (address) home_address_rows.push(address);
          else {
            home_address_not_fill = false;
            tabs.push($('.accordion-header strong', $(this)).text());
          }
        });
      }

      if (!home_address_not_fill) {
        Swal.fire({
          title: "Error",
          html: 'Please fill in all the required information in the sections below:<br />' + tabs.join(', '),
          icon: 'error'
        });
        return;
      }
      //-------------------------------------------------
      work_address_rows = [];
      work_address_not_fill = true;
      if ($("#step_contact #mc-work-address").length > 0) {
        $("#step_contact #mc-work-address .accordion-item").each(function (i) {
          objects = $(".form-control", this);
          let address = validateFields(objects, "#workAddress", i);
          if (address) work_address_rows.push(address);
          else {
            work_address_not_fill = false;
            tabs.push($('.accordion-header strong', $(this)).text());
          }
        });

        if (work_address_rows.length <= 0)
          $(".contact-work-address").addClass("d-none");
        else $(".contact-work-address").removeClass("d-none");
      }

      if (!work_address_not_fill) {
        Swal.fire({
          title: "Error",
          html: 'Please fill in all the required information in the sections below:<br />' + tabs.join(', '),
          icon: 'error'
        });
        return;
      }
      //$('#employment-tab').removeAttr('disabled').trigger('click');
      $('#step_employment input[name="annual_income"]').trigger("change");
      data = {
        contact: contact,
        home_address: home_address_rows,
        work_address: work_address_rows,
      };
  
      let jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "contact",
      });
      data__ = {
        email: $("#emailNeedCheck").val(),
        phone: $("#mobilephone_1").val(),
      };
      //console.log(data__);
      update_next_step("contact", "complete", data__, "employment");
    });
  }

  function stepEmployment() {
    //Quick File Uploader
    uploadFilePreview("#step_employment");

    $("#step_employment input.monthly_income").change(function () {
      grossMonthlyIncome("#step_employment");
    });

    $('#step_employment input[name="annual_income"]').change(function () {
      annualGrossIncome("#step_employment");
      num = $(this).val() - 0;
      if (isNumber(num) && num > 0) {
        if( num > 0) $(this).val(num.toFixed(2));
        $(this).closest(".input-group").find("small").remove();
        //$("#step_employment input.monthly_income").trigger('change');
      } else showMessage($(this), num >= 0, "The minimum amount is 0.");
    });

    $('#step_employment input[name="yrs_of_employment_period"]').change(
      function () {
        num = $(this).val();
        if (num) {
          if (!isNumber(num)) $(this).val("");
          showMessage($(this), num - 0 < 100, "Maximum 99 years");
          if (num - 0 < 100) {
            $(this).removeClass("error");
          } else {
            $(this).addClass("error");
            $(this).val("");
          }
        } else {
          showMessage($(this), true, "Maximum 99 years");
          $(this).removeClass("error");
        }
      }
    );

    $('#step_employment select[name="employment_status"]').change(function () {
      value = $(this).val();

      labels = $("#step_employment label.need-required");
      fields = $(
        "#step_employment input.need-required, #step_employment select.need-required"
      );
      if (value !== "UNEMP") {
        labels.addClass("required");
        fields.attr("required", "true");
      } else {
        labels.removeClass("required");
        fields.removeAttr("required").removeClass("error");
      }
    });

    $("#step_employment .document_files").change(function () {
      value = $(this).val();
      if (value.length > 0) {
        $(this).closest(".quick-file-uploader").removeClass("border-danger");
      }
    });

    $("#btnContinueStep3").click(function () {
      fields = $("#step_employment .form-control");
      checkedOk = true;
      data = {};
      fields.each(function () {
        reqAttr = $(this).attr("required");
        required = typeof reqAttr !== "undefined";

        field_value = $(this).val();
        field_value = field_value.trim();
        if (field_value.length <= 0 && required) {
          checkedOk = false;
          $(this).addClass("error");
          //find parents
          if ($(this).hasClass("document_files")) {
            $(this).closest(".quick-file-uploader").addClass("border-danger");
          }
        } else {
          $(this).removeClass("error");
          $(this).closest(".quick-file-uploader").removeClass("border-danger");
        }

        fileShowRequired($(this).closest(".quick-file-uploader"), (field_value.length <= 0 && required));

        field_value = isNumber(field_value) ? field_value - 0 : field_value;
        field_name = $(this).attr("name");
        exclude = ["income_document_files", "income_document"];
        if ("income_document_files" === field_name && field_value.length > 0) {
          temp = JSON.parse(field_value);
          data[$(this).attr("name")] = temp;
          mappingDocumentFiles(temp, $('[data-field="income_documents"]'));
        } else if (!exclude.includes(field_name)) data[$(this).attr("name")] = field_value;

        if ($(this).is("select")) {
          field_value = $(this).find("option:selected").text();
        } else if ($(this).hasClass("income")) {
          field_value = formatMoney(field_value);
        }

        if (!exclude.includes(field_name)) {
          temp =
            field_name == "company_telephone" && field_value
              ? "(+65)" + field_value.toString()
              : field_value;
          $('.employment-info [data-field="' + field_name + '"]').text(temp);
        }
      });
      //check income
      $("#step_employment input.income").each(function () {
        field_value = $(this).val();
        if (!isNumber(field_value)) {
          checkedOk = false;
          $(this).addClass("error");
        } else $(this).removeClass("error");
      });
      //income_document
      $income_doc = [];
      $arg = ["payslip", "cpf", "noa", "others"];
      $(
        '.income-document [data-field]:not([data-field="income_documents"])'
      ).text("No");
      $("#income_document input:checked").each(function (i) {
        $income_doc.push($(this).val() - 0);
        $(
          '.income-document [data-field="' + $arg[$(this).val() - 1] + '"]'
        ).text("Yes");
      });
      data["income_document"] = $income_doc;

      //console.log(data);
      if (!checkedOk) return;
      let jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "employment",
      });
      //btoa(jsonString)
      update_next_step("employment", "complete", "", "loan");
    });
  }

  function stepLoanDetails() {
    //Quick File Uploader
    uploadFilePreview("#step_loan");
    $('input[type="radio"]').change(function(){
      find_required = $(this).attr('data-required');
      obj_required = $('#step_loan ' + find_required);
      field_data = $(this).attr('name');
      $('[data-field="' + field_data + '"]').text($(this).val() === '1' ? 'Yes' : 'No');
      
      if($(this).is(':checked') && $(this).val() === '1') { 
        obj_required.fadeIn();
        obj_required.find('label').addClass('required');
        obj_required.find('textarea').attr('required','required');
      } else {
        obj_required.fadeOut();
        obj_required.find('label').removeClass('required');
        obj_required.find('textarea').removeAttr('required').val('');
        textarea = obj_required.find('textarea');
        field_data = textarea.attr('name');
        $('[data-field="' + field_data + '"]').text('');
      }
    });

    $('#loan_type_id, #loan_terms').change(function () {
      id = $(this).attr('id'); val = $(this).val();
      if( id == 'loan_terms') {
        showMessage( $(this), (isNumber(val) && val > 0), 'Loan terms is a number greater than 0');
      }
      calculateInstalment();
    });

    $("#term_unit").change(function () {
      termUnit($("#loan_type_id").val(), $(this).val());
      calculateInstalment();
    });

    $('input[name="loan_amount_requested"]').change(function () {
      loan_requested = $(this).val();
      if (!isNumber(loan_requested)) {
        $(this).val("");
        $(this).addClass("error");
      } else $(this).removeClass("error");

      condition = (loan_requested - 0) > 0;
      showMessage($(this), condition, "Loan Amount Required must be greater than 0");
      //calculateInstalment();     

      if( condition && checkDateToSendMLCB() ) {        
        checkMLCB(loan_requested);            
      } else 
        calculateInstalment();   

    });

    $('input[name="no_of_active_credit_loan"]').change(function () {
      value = $(this).val();
      value = value.trim();
      if (value) {
        if (!isNumber(value)) $(this).val("");
        showMessage($(this), value - 0 <= 100, "Maximum 100 symbols");
        value - 0 <= 100
          ? $(this).removeClass("error")
          : $(this).addClass("error");
      } else {
        showMessage($(this), true, "Maximum 100 symbols");
        $(this).removeClass("error");
      }
    });

    $("#step_loan .document_files").change(function () {
      value = $(this).val();
      if (value.length > 0) {
        $(this).closest(".quick-file-uploader").removeClass("border-danger");
      }
    });

    $("#btnContinueStep4").click(function () {
      fields = $("#step_loan .form-control");
      checkedOk = true;
      data = {};
      fields.each(function () {
        reqAttr = $(this).attr("required");
        required = typeof reqAttr !== "undefined";

        field_value = $(this).val();
        field_value = field_value.trim();
        if (field_value.length <= 0 && required) {
          checkedOk = false;
          $(this).addClass("error");
          if ($(this).hasClass("document_files")) {
            $(this).closest(".quick-file-uploader").addClass("border-danger");
          }
        } else {
          if ("loan_terms" === $(this).attr("name") && field_value.length > 0) {
            showMessage(
              $(this),
              isNumber(field_value),
              "Please enter a number"
            );
            checkedOk = isNumber(field_value);
          } else $(this).removeClass("error");

          $(this).closest(".quick-file-uploader").removeClass("border-danger");
        }
        
        fileShowRequired($(this).closest(".quick-file-uploader"), (field_value.length <= 0 && required));

        field_value = isNumber(field_value) ? field_value - 0 : field_value;
        field_name = $(this).attr("name");
        if (
          ("document_files" == field_name || "loan_interest" == field_name) &&
          field_value.length > 0
        ) {
          temp = JSON.parse(field_value);
          data[$(this).attr("name")] = temp;
          if ("loan_interest" == field_name) {
            data["interest"] = temp.interest;
            $('.loan-details [data-field="interest"]').text(temp.interest);
          } else {
            mappingDocumentFiles(temp, $('[data-field="loan_documents"]'));
          }
        } else if ("interest" !== field_name) {
          data[$(this).attr("name")] = field_value;
        }

        if ($(this).is("select")) {
          field_value = $(this).find("option:selected").text();
        } else if ($(this).hasClass("income") || field_name == 'installment') {
          field_value = formatMoney(field_value);
        }
        
        if( $(this).attr('type') === 'radio' && $(this).is(':checked')) {
          $('.loan-details [data-field="' + field_name + '"]').text(parseInt(field_value) === 1 ? 'Yes' : 'No');  
        }

        exclude = ['interest', 'document_files', 'benefit', 'politically'];
        if (!exclude.includes(field_name)) {
          $('.loan-details [data-field="' + field_name + '"]').text( field_value );
        }
      });

      //console.log(data);
      if (!checkedOk) return;

      let jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "loan",
      });
      update_next_step("loan", "complete", "btoa(jsonString)", "bank");
    });
  }

  function stepBankInfo() {

    $('#step_bank input[name="account_number_1"]').change(function(){
      value = $(this).val();
      showMessage($(this), isNumber(value) , "Bank account should contain numbers only." );
    });

    $("#btnContinueStep5").click(function () {
      fields = $("#step_bank .form-control");
      data = {};
      checkedOk = true;
      fields.each(function () {
        reqAttr = $(this).attr("required");
        required = typeof reqAttr !== "undefined";

        field_value = $(this).val();
        field_value = field_value.trim();
        if (field_value.length <= 0 && required) {
          checkedOk = false;
          $(this).addClass("error");
        } else {
          $(this).removeClass("error");
        }

        field_name = $(this).attr("name");

        if(field_name == 'account_number_1' && field_value.length > 0 && !isNumber(field_value) ) {
          checkedOk = false;
          showMessage($(this), isNumber(field_value) , "Bank account should contain numbers only." )
        }
        
        data[field_name] = field_value;
        //update to completion
        if (field_name === "date_of_salary")
          field_value = getOrdinalSuffix(field_value);
        $('.bank-information [data-field="' + field_name + '"]').text(
          field_value
        );
      });

      //console.log(data);
      if (!checkedOk) return;

      let jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "bank",
      });
      update_next_step("bank", "complete", "btoa(jsonString)", "guarantor");
    });
  }

  function stepSurety() {
    
    $("#accordionGuarantor .accordion-item .accordion-button").click(function(e){      
      return;
      e.stopPropagation();
      e.preventDefault();
      button_ = $('#guarantor'); // $(this).parents('.accordion-item')
      $('html, body').animate({
        scrollTop: button_.offset().top - 50
      }, 1000);
    });

    checkRequiredSurety();
    uploadFilePreview("#step_guarantor");

    $('#step_guarantor select[name="guarantor_info.employment_status"]').change(
      function () {
        value = $(this).val();
        checkChangePaymentStatus(value);
      }
    );

    $('input[name="guarantor_info.bank_acc"]').change(function(){
      value = $(this).val(); 
      condition = ( value.length > 0 && isNumber(value)) ? true : false;

      showMessage($(this), isNumber(value) , 'Bank account should contain numbers only.');
    });

    $("#btnContinueStep6").click(function () {
      data = {};
      checkedOk = true;
      field_value = "";
      fields = $("#step_guarantor .form-control");
      tabs = [];
      //check nric_no
      nric_no = $("#guarantor_nric_no").val();
      //if (nric_no.length > 0) {
        fields.each(function () {
          reqAttr = $(this).attr("required");
          required = typeof reqAttr !== "undefined";
          
          parent = $(this).parents('.accordion-item');
          tab_text = $('.accordion-button strong', parent).text();

          field_value = $(this).val();
          field_value = field_value.trim();
          if (field_value.length <= 0 && required) {
            checkedOk = false;
            $(this).addClass("error");
            if ($(this).hasClass("document_files")) {
              $(this).closest(".quick-file-uploader").addClass("border-danger");
            }           
            if( !tabs.includes(tab_text)) tabs.push(tab_text);
          } else {
            $(this).removeClass("error");
            $(this)
              .closest(".quick-file-uploader")
              .removeClass("border-danger");
          }
      
          field_value = (field_value.length > 0 && isNumber(field_value)) ? (field_value - 0) : field_value;
          field_name = $(this).attr("name");
          if (
            "guarantor_info.date_of_birth" === field_name &&
            field_value.length > 0
          ) {
            age20 = field_value ? calculateAge(field_value) : false;
            showMessage($(this), age20, "You must be 20 years old to apply.");            
            if (!age20) {
              checkedOk = false;
              if( !tabs.includes(tab_text)) tabs.push(tab_text);
            }
          }
          //check phone
          if (
            $(this).hasClass("phone-number") &&
            field_value.length > 0 &&
            !isValidPhoneNumber(field_value)
          ) {
            checkedOk = false;
            if( !tabs.includes(tab_text)) tabs.push(tab_text);
            $(this).addClass("error");
          }
          //check Email
          email = $(this).val();
          if( $(this).attr('type') == 'email' && email.length > 0 && !validateEmail(email)) {
            checkedOk = false;
            if( !tabs.includes(tab_text)) tabs.push(tab_text);
            $(this).addClass("error");
          }
          
          //bank account
          field_value = $(this).val();
          if( field_name == 'guarantor_info.bank_acc' && field_value.length > 0) {
            checkedOk = isNumber(field_value);
            showMessage($(this), checkedOk, "Bank account should contain numbers only.");     
          }

          field_name_fill = field_name;
          field_name = field_name.replace("guarantor_info.", "");
          //data[field_name] = field_value;
          if (
            "income_document_files" === field_name &&
            field_value.length > 0
          ) {
            temp = JSON.parse(field_value);
            data[field_name] = temp;
            mappingDocumentFiles(
              temp,
              $('[data-field="guarantor_info.income_documents"]')
            );
          } else data[field_name] = field_value;

          if ($(this).hasClass("phone-number"))
            field_value = field_value ? "(+65)" + field_value : "";
          else if ($(this).is("select"))
            field_value = $(this).find("option:selected").text();
          else if ($(this).hasClass("income"))
            field_value = formatMoney(field_value);

          if (field_name === "firstname") {
            fullname =
              $('#step_guarantor input[name="guarantor_info.lastname"]').val() +
              " " +
              $('#step_guarantor input[name="guarantor_info.firstname"]').val();
            $('.surty [data-field="guarantor_info.full_name"]').text(fullname);
          } else $('.surty [data-field="' + field_name_fill + '"]').text(field_value);
        }); //for

        income_document = [];
        $arg = ["payslip", "cpf", "noa", "others"];
        $("#step_guarantor .income_document input").each(function (i) {
          check_value = $(this).val();
          text = "No";
          if ($(this).is(":checked")) {
            income_document.push(check_value);
            text = "Yes";
          }

          $(
            '.surty .surty-income-document [data-field="guarantor_info.' +
              $arg[check_value - 1] +
              '"]'
          ).text(text);
        });

        data["income_document"] = income_document;
      //}

      if (!checkedOk) {
        Swal.fire({
          title: 'No data entered.',
          html: 'There are some fields inside the tab below that you have not completed.' +
                'Please enter the data to finish.<br /><b>'  + tabs.join(', ') + '</b>',
          icon: 'error'
        });
        return;
      }

      jsonString = JSON.stringify(data);
      updateApplicationForm({
        data: jsonString,
        status: "complete",
        step: "guarantor",
      });
      update_next_step(
        "guarantor",
        "complete",
        "btoa(jsonString)",
        "uploadfiles"
      );
      //surty
    });
  }

  function checkChangePaymentStatus(value) {
    guarantor_nric_no = $("#guarantor_nric_no").val();

    labels = $("#step_guarantor label.emp-need-required");
    fields = $(
      "#step_guarantor input.emp-need-required, #step_guarantor select.emp-need-required"
    );
    labels.removeClass("required");
    fields.removeAttr("required").removeClass("error");
    $("#step_guarantor .quick-file-uploader").removeClass("border-danger");

    if (guarantor_nric_no) {
      switch (value) {
        case "EMP":
          labels.addClass("required");
          fields.attr("required", "true");
          break;
        case "UNEMPINC":
          labels.addClass("required");
          fields.attr("required", "true");
          break;
      }
    }
  }

  function stepCompletion() {
    $("#btnContinueStep7").click(function () {

      formData = updateSubmitData();
      //console.log(formData);
  
      if( !formData ) return ;

      button = $(this);
      button.text("Processing...").attr("disabled", "disabled");
      data = {};
      data["allow_mlcb"] = 1;
      data["signature"] = [];
      
      mlcb_ = {};
      allowSubmit = formData;
      //wait until update data is complete
      setTimeout(function(){
        if (allowSubmit) {
          //show message
          Swal.fire({
            title: "Processing profile submission.",
            html: "Your profile is being sent to the receiving center.",
            didOpen: () => {
              Swal.showLoading();
            },
            allowOutsideClick: false,
          });

          $("#completion .form-check-input").each(function () {
            if ($(this).is(":checked")) {
              mlcb_[$(this).attr("name")] = $(this).val();
            }
          });
         
          $.ajax({
            type: "POST",
            url: mc_ajax.ajax_url,
            dataType: "json",
            data: {
              action: "application",
              dataForm: formData
            },
            success: function (response) {
              //console.log(response);
              if (response.error || response.message !== "success") {
                button.text("Submit").removeAttr("disabled");
                Swal.close();
                Swal.fire({
                  title: "The profile was not submitted successfully.",
                  html: response.message_error,
                  icon: "error",
                });
                return false;
              } else {
                button.attr("disabled", "disabled").text("Done");
                Swal.close();
                Swal.fire({
                  title: "Profile submitted successfully.",
                  text: "Please approach to the shop within 3 days",
                  icon: "success",
                });

                setTimeout(function () {
                  window.location.href = "?act=loan";
                }, 3000);
              }
            },
            error: function (response) {
              button.text("Submit").removeAttr("disabled");
            },
          });
        } else {
          //alert('Please complete filling in the data in all the fields of the form.');
          Swal.fire({
            title: "Data validation error",
            text: "Please complete filling in the data in all the fields of the form.",
            icon: "error",
          });
          $(this).text("Submit").removeAttr("disabled");
          return false;
        }
      }, 1000);
    });
  }

  function checkPhoneEmail() {
    var intervalIds = []; // Array to store interval IDs
    //phoneCheck
    $("#phoneCheck").click(function () {
      //send sms to phone
      phone = $("#mobilephone_1").val();
      $("#phoneLoading").removeClass("d-none");
      $("#phoneSubmit").addClass("d-none");
      $.ajax({
        type: "POST",
        url: mc_ajax.ajax_url,
        dataType: "json",
        data: {
          action: "phone_verification",
          phonetocheck: phone,
        },
        success: function (response) {
          if (
            response.data.show_popup == 1 ||
            response.data.show_popup == "1"
          ) {
            //call to show popup
            $("#phoneVerifycation").removeClass("d-none");
            $("#pastePhone").text("+65" + phone);
            startCountdown(5*60, $("#countTimePhone"), "phone", intervalIds);
          } else {
            //alert(response.message);
            Swal.fire({
              title: "Phone Verification",
              text: response.message,
              icon: "error",
            });
          }
          $("#phoneLoading").addClass("d-none");
          $("#phoneSubmit").removeClass("d-none");
        },
        error: function (response) {
          //console.log(response);
        },
      });

      $("#phoneVerifycation #btnClose").click(function () {
        $("#phoneVerifycation").addClass("d-none");
        $("#phoneVerifycation #phoneVerifyCode").val("");
        clearAllIntervals(intervalIds);
        $("#countTimePhone").text(5*60);
      });

      $("#phoneVerifycation #btnValidatePhone").click(function (e) {
        e.preventDefault();
        button = $(this);
        button.text("Processing....");
        $("#phoneVerifyError").text("").addClass("d-none");
        otpCode = $("#phoneVerifycation #phoneVerifyCode").val();

        if (otpCode.length <= 0) {
          $("#phoneVerifycation #phoneVerifyCode").addClass("error");
        } else {
          $.ajax({
            type: "POST",
            url: mc_ajax.ajax_url,
            dataType: "json",
            data: {
              action: "verify_otp_phone",
              verfiy_info: {
                phone: $("#mobilephone_1").val(),
                otp_verify: otpCode,
                application_id: null,
              },
            },
            success: function (response) {
              //console.log(response);
              if (
                response.data.confirmed === 1 ||
                response.data.confirmed == "1"
              ) {
                $("#phoneVerifycation #btnClose").trigger("click");
                $("#chkPhoneError").addClass("d-none");
                $("#chkPhoneIcon").removeClass("d-none");
                $("#phone_checked").val($("#mobilephone_1").val());
                button.text("Validate OTP");
              } else {
                $("#phoneVerifyError")
                  .text(response.message)
                  .removeClass("d-none");
                button.text("Validate OTP");
              }
            },
          });
        }
      });
    });

    $("#mobilephone_1").change(function () {
      old_phone = $("#phone_checked").val();
      new_phone = $(this).val();
      if (old_phone && old_phone != new_phone) {
        $("#chkPhoneIcon").addClass("d-none");
        $("#chkPhoneError").removeClass("d-none");
      }

      if (old_phone && new_phone == old_phone) {
        $("#chkPhoneIcon").removeClass("d-none");
        $("#chkPhoneError").addClass("d-none");
      }
    });
    //mailCheck
    $("#emailCheck").click(function () {
      //Email Verification via OTP
      var re = /\S+@\S+\.\S+/;
      //return re.test(email);
      email = $("#emailNeedCheck").val();
      if( !email || !re.test(email)) {
        Swal.fire({
          title: "Email verification OTP",
          html: "Please enter an email address.<br />OR<br />The email address is in an incorrect format.",
          icon: 'error',
        });

        return;
      }

      $("#emailLoading").removeClass("d-none");
      $("#emailSubmit").addClass("d-none");
      $.ajax({
        type: "POST",
        url: mc_ajax.ajax_url,
        dataType: "json",
        data: {
          action: "email_verification",
          emailtocheck: email,
          firstname: $('#step_personal input[name="firstname"]').val(), 
          lastname: $('#step_personal input[name="lastname"]').val()
        },
        success: function (response) {
          if (
            response.data.show_popup == 1 ||
            response.data.show_popup == "1"
          ) {
            //call to show popup
            $("#emailVerifycation").removeClass("d-none");
            $("#pasteEmail").text(email);
            startCountdown(5*60, $("#countTimeEmail"), "email", intervalIds);
          } else {
            //alert(response.message);
            Swal.fire({
              title: "Email Verification",
              text: response.message,
              icon: "error",
            });
          }
          $("#emailLoading").addClass("d-none");
          $("#emailSubmit").removeClass("d-none");
        },
        error: function (response) {
          //console.log(response);
        },
      });

      $("#emailVerifycation #btnClose").click(function () {
        $("#emailVerifycation").addClass("d-none");
        $("#emailVerifycation #emailVerifyCode").val("");
        clearAllIntervals(intervalIds);
        $("#countTimeEmail").text(5*60);
      });

      $("#emailVerifycation #btnValidateEmail").click(function (e) {
        e.preventDefault();

        button = $(this);
        button.text("Processing....");

        $("#emailVerifyError").text("").addClass("d-none");
        otpCode = $("#emailVerifycation #emailVerifyCode").val();

        if (otpCode.length <= 0) {
          $("#emailVerifycation #emailVerifyCode").addClass("error");
        } else {
          $.ajax({
            type: "POST",
            url: mc_ajax.ajax_url,
            dataType: "json",
            data: {
              action: "verify_otp",
              verfiy_info: {
                email: $("#emailNeedCheck").val(),
                otp_verify: otpCode,
                application_id: null,
              },
            },
            success: function (response) {
              //console.log(response);
              if (
                response.data.confirmed === 1 ||
                response.data.confirmed == "1"
              ) {
                $("#emailVerifycation #btnClose").trigger("click");
                $("#chkEmailError").addClass("d-none");
                $("#chkEmailIcon").removeClass("d-none");
                $("#email_checked").val($("#emailNeedCheck").val());
                $("#countTimeEmail").text(5*60);
                button.text("Validate OTP");
              } else {
                $("#emailVerifyError")
                  .text(response.message)
                  .removeClass("d-none");
                button.text("Validate OTP");
              }
            },
          });
        }
      });
    });

    $("#emailNeedCheck").change(function () {
      old_email = $("#email_checked").val();
      new_email = $(this).val();
      if (old_email && new_email != old_email) {
        $("#chkEmailIcon").addClass("d-none");
        $("#chkEmailError").removeClass("d-none");
      }

      if (old_email && new_email == old_email) {
        $("#chkEmailIcon").removeClass("d-none");
        $("#chkEmailError").addClass("d-none");
      }
    });
  }

  function getOrdinalSuffix(num) {
    let j = num % 10,
      k = num % 100;

    if (j == 1 && k != 11) {
      return num + "st";
    }
    if (j == 2 && k != 12) {
      return num + "nd";
    }
    if (j == 3 && k != 13) {
      return num + "rd";
    }
    return num + "th";
  }

  function formatMoney(amount, currency = "USD", locale = "en-US") {
    return new Intl.NumberFormat(locale, {
      style: "currency",
      currency: currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount);
  }

  function isNumber(value) {
    const regex = /^-?\d+(\.\d+)?$/;
    return regex.test(value);
  }

  function grossMonthlyIncome(sectionId) {
    grossMonthly = $(sectionId + " input.monthly_income");
    total3Months = 0;
    checkedNum = true;
    grossMonthly.each(function () {
      value = $(this).val();
      value = (value - 0)
      $(this).removeClass("error");
      if (isNumber(value)) total3Months += value;
   
      condition = (isNumber(value) && value >= 0);
      showMessage($(this), condition, "The minimum amount is 0.");  
      checkedNum = condition;
    });

    average = year = sixMonths = 0;
    if (checkedNum && total3Months > 0) {
      average = (total3Months / 3).toFixed(2);
      year = (average * 12).toFixed(2);
      sixMonths = (average * 6).toFixed(2);
    } else if( sectionId == '#step_guarantor'){
      average = year = sixMonths = '';
    }

    $(sectionId + " input.ave_monthly_income").val(average);
    $(sectionId + " input.annual_income").val(year);
    $(sectionId + " input.six_month_income").val(sixMonths);

    return checkedNum;
  }

  function annualGrossIncome(sectionId) {
    checkedNum = true;
    obj = $(sectionId + " input.annual_income");
    obj.removeClass("error");
    average = year = sixMonths = oneMonth = "";
    obj_value = obj.val();

    if (isNumber(obj_value) && obj_value > 0) {
      year = obj_value;
      oneMonth = (year / 12).toFixed(2);
      sixMonths = (year / 2).toFixed(2);
    } else {      
      if( sectionId != 'step_guarantor') {
        obj.val('');
        average = sixMonths = oneMonth = '';
      }
      checkedNum = false;
    }

    $(sectionId + " input.ave_monthly_income").val(oneMonth).removeClass('error');
    $(sectionId + " input.six_month_income").val(sixMonths).removeClass('error');
    $(sectionId + " input.monthly_income").val(oneMonth).removeClass('error');

    return checkedNum;
  }

  function termUnit(loanId, termId) {
    interest = 0;
    late_interest = 0;
    late_fee = 0;
    loanRate = JSON.parse($("#loanRate").val());
    //console.log(loanRate);
    term_keys = ['daily', 'weekly', 'biweekly', 'monthly', 'annual'];
    loanRate.forEach(function (item) {
      if ((loanId - 0) == item.type_id) {
        switch(term_keys[termId]) {
          case 'daily':
            interest = item.daily;
          break;
          case 'weekly':
            interest = item.weekly;
          break;
          case 'biweekly':
            interest = item.biweekly
          break;         
          case 'annual':
            interest = item.annual
          break;
          case 'monthly':
          default:
            interest = item.monthly
          break;
        }
        
        late_interest = item.late_interest;
        late_fee = item.late_fee;
      }
    });
    //late_interest
    fees = {
      interest: interest,
      late_interest: late_interest,
      late_fee: late_fee,
    };
    //monthly_late_fee
    $("#loanInterest").val(JSON.stringify(fees));
  }

  function formatDate(dateString) {
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ];

    // Create a new Date object from the input string
    const date = new Date(dateString);

    // Extract day, month, and year
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    // Format the date as '26 Mar, 1978'
    return `${day} ${month}, ${year}`;
  }

  function checkRequiredSurety() {
    $("#guarantor_nric_no").change(function () {
      value = $(this).val();
      //show / hidden on Completion
      if (value.length > 0) {
        $("#surety-report").removeClass("d-none");
      } else $("#surety-report").addClass("d-none");

      labels = $("#step_guarantor .need-required");
      fields = $(
        "#step_guarantor input.need-required, #step_guarantor select.need-required"
      );
      if (value.length > 0) {
        labels.addClass("required");
        fields.attr("required", "true");
      } else {
        labels.removeClass("required").removeClass("error");
        fields.removeAttr("required").removeClass("error");
        $("#step_guarantor input, #step_guarantor select").val("");
        $("#step_guarantor input[type='checkbox']").prop('checked', false);
        $('.fileListPreview ul').remove(); 
      }

      // Create a new event
      var event = new Event("change", {
        bubbles: true,
        cancelable: true,
      });
      // Dispatch the event
      var selectElement = document.getElementById(
        "guarantor_info.employment_status"
      );
      selectElement.dispatchEvent(event);
    });

    $('input[name="guarantor_info.date_of_birth"').change(function () {
      dbo = $(this).val();
      age20 = (dbo.length > 0 ) ? calculateAge(dbo) : true;      
      showMessage($(this), age20, "You must be 20 years old to apply.");
    });
    
    $("#step_guarantor input.monthly_income").change(function () {
      grossMonthlyIncome("#step_guarantor");
      num = $(this).val() - 0;
      if (num > 0) $(this).val(num.toFixed(2));
    });

    $("#step_guarantor input.annual_income").change(function () {
      annualGrossIncome("#step_guarantor");
      num = $(this).val() - 0;
      if (num > 0) $(this).val(num.toFixed(2));
    });
  }

  function checkMLCB(loan_requested) {
    //call message
    showLoading(
      "MLCB Checking",
      "Please wait a few minutes for MLCB to check your loan.",
      10000
    );
    $(".input-mlcb-checked .uncheck").removeClass("d-none");
    $(".input-mlcb-checked .checked").addClass("d-none");

    data_ = applicationForm;
    data_["loan_amount_requested"] = loan_requested;
    //console.log(data_);
    personal = JSON.parse(data_.personal.data);
    contact = JSON.parse(data_.contact.data);
    employment = JSON.parse(data_.employment.data);
    //contact phone
    mobilephone_1 = $('#step_contact #mobilephone_1').val();
    mobilephone_1 = (contact.contact.mobilephone_1 != mobilephone_1 && mobilephone_1.length > 0) 
                    ? mobilephone_1 : contact.contact.mobilephone_1;

    params = {};
    params['annual_income'] = employment.annual_income;
    params['block'] = contact.home_address[0].block;
    params['building'] = contact.home_address[0].building;
    params['street'] = contact.home_address[0].street;
    params['unit'] = contact.home_address[0].unit;
    params['postal_code'] = contact.home_address[0].postal;
    params['company_name'] = employment.company_name;
    params['mlcb_client_id'] = 0;
    params['mlcb_user_id'] = '';
    params['gender'] = personal.gender;
    params['employment_status'] = employment.employment_status;
    params['application_no'] = '';
    params['country_id'] = personal.nationality;
    params['monthly_income'] = employment.monthly_income;
    params['monthly_income_1'] = employment.monthly_income_1;
    params['monthly_income_2'] = employment.monthly_income_2;
    params['monthly_income_3'] = employment.monthly_income_3;
    params['six_months_income'] = employment.six_months_income;
    params['mobilephone_1'] = mobilephone_1;
    params['identification_no'] = personal.identification_no;
    params['identification_type'] = personal.identification_type;
    params['fullname'] = personal.lastname + ' '  + personal.firstname;
    params['date_of_birth'] = personal.date_of_birth;
    params['loan_amount_requested'] = loan_requested;
   
    $.ajax({
      type: "POST",
      url: mc_ajax.ajax_url,
      dataType: "json",
      data: {
        action: "check_mlcb",
        dataForm: params
      },
      success: function (response) {
        //console.log(response);
        message =
          "After checking the loan through MLCB, you are eligible to proceed with the loan you have entered.";
        icon__ = "success";
        if (response.code > 36500) {
          message = response.message;
          icon__ = "error";
        } else {
          balance = parseFloat(response.data.balance);
          loan_requested = parseFloat(loan_requested);

          if (loan_requested > balance || balance === 0) {
            message =
              "After MLCB's review of the loan, you are only eligible to borrow a maximum of <b>" +
              response.balance_format +
              "</b>. " +
              "You do not meet the criteria to continue with the submitted loan. Please adjust the loan amount to meet the allowable conditions.";
            icon__ = "warning";
          }
        }

        if (response.code == 36500) {
          html =
            '<ul class="mb-3 mlcb-results"><li><label>Data & Time:</label> ' +
            response.data.date +
            "</li><li><label>ID Type:</label> " +
            response.data.id_type +
            "</li>" +
            "<li><label>NRIC No.:</label> " +
            replaceRangeWithAsterisk(response.data.nric_no, 0, 4) +
            "</li><li><label>Obligation:</label> " +
            response.data.obligation +
            "</li>" +
            "<li><label>Name:</label> " +
            response.data.fullname +
            "</li><li><label>Balance:</label> " +
            response.balance_format +
            "</li></ul>";
          message = html + message;
          $(".input-mlcb-checked .uncheck").addClass("d-none");
          $(".input-mlcb-checked .checked").removeClass("d-none");
        }

        Swal.close();
        Swal.fire({
          title: "Results of the MLCB inspection",
          html: message,
          icon: icon__,
          allowOutsideClick: false
        }).then((result) => {
          if(result.isConfirmed) {
            calculateInstalment();
          };
        });

        if( response.continue === 1 || response.continue === '1') {
          $('#btnContinueStep4').removeAttr('disabled');
        } else
          $('#btnContinueStep4').attr('disabled','disabled');
      },
      error: function () {
        Swal.close();
      },
    });
  }

  function calculateAge(dbo) {
    var today = new Date();
    var birthDate = new Date(dbo);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return parseInt(age) > 19;
  }

  function showMessage(obj, condition, text) {
    message = obj.closest(".input-group").find("small");
    if (!condition) {
      obj.addClass("error");
      tag = '<small class="text-danger position-absolute top-100"></small>';
      if (message.length <= 0) obj.closest(".input-group").append(tag);
      obj.closest(".input-group").find("small").text(text);
    } else {
      obj.removeClass("error");
      obj.closest(".input-group").find("small").remove();
    }
  }

  function checkExpiryDate(expiryDate, numberOfMonths = 2) {
    
    var currentDate = new Date();
    var startDate = currentDate.toISOString().split('T')[0]; 
    
    var expiryDate = new Date(expiryDate);
    // Add the specified number of months
    let limitedDate = new Date(startDate);
    limitedDate.setMonth(limitedDate.getMonth() + numberOfMonths);
    
    return expiryDate > limitedDate;
  }

  function isValidPhoneNumber(phoneNumber) {
    const phoneRegex =
      /^\+?(\d{1,4})?[-. (]?(\d{1,3})?[)-. ]?(\d{1,4})[-. ]?(\d{1,4})[-. ]?(\d{1,9})$/;
    testPhone = phoneRegex.test(phoneNumber);
    if (!testPhone) {
      //alert('The phone number you entered is incorrect. Use "-" or "." to separate every 3 or 4 digits.');
      Swal.fire({
        title: "Data validation error",
        text: 'The phone number you entered is incorrect. Use "-" or "." to separate every 3 or 4 digits.',
        icon: "error",
      });
    }
    return testPhone;
  }

  function startCountdown(duration, display, phoneEmail, intervalIds) {
    clearAllIntervals(intervalIds);
    display.text(seconds);
    $("#emailVerifyError, #phoneVerifyError").addClass("d-none").val("");
    var seconds = duration;
    var interval = setInterval(function () {
      display.text(seconds);
      // Check if the timer has run out
      if (--seconds <= 0) {
        clearInterval(interval);
        if (phoneEmail == "email") {
          $("#emailVerifycation").addClass("d-none");
          $("#emailVerifycation #emailVerifyCode").val("");
        } else {
          $("#phoneVerifycation").addClass("d-none");
          $("#phoneVerifycation #phoneVerifyCode").val("");
        }
      }
    }, 1000);

    intervalIds.push(interval); // Store the interval ID
  }

  function clearAllIntervals(intervalIds) {
    for (var i = 0; i < intervalIds.length; i++) {
      clearInterval(intervalIds[i]);
    }
    intervalIds = []; // Clear the array
  }

  function replaceRangeWithAsterisk(str, start, end) {
    if (str.length <= 0) return "";

    const length = end - start + 1;
    const replacement = "*".repeat(length);
    return str.substring(0, start) + replacement + str.substring(end + 1);
  }

  function receiptDownloadPDF() {
    $(".receiptDownloadPDF").click(function (e) {
      e.preventDefault();
      showLoading(
        "Downloading PDF file...",
        '<small class="text-danger">Please do not block the popup on your browser.</small>',
        2000
      );
      $.ajax({
        type: "POST",
        url: mc_ajax.ajax_url,
        dataType: "json",
        data: {
          action: "receipt_downdoad_pdf",
          receipt_id: $(this).attr("data-receiptId"),
          receipt_no: $(this).attr("data-receiptNo"),
        },
        success: function (response) {
          //console.log(response);
          if (!response.error || response.error === "false") {
            Swal.close();
            viewPdf(response.pdf_base64);
          } else {
            Swal.fire({
              title: "Download File PDF",
              text: "There was an error loading the file for viewing. Please try again.",
              icon: "error",
            });
          }
        },
      });
    });
  }

  function base64ToBlob(base64, mime) {
    //check file
    if (base64) {
      temp = base64.split(",");
      if (temp.length == 2) base64 = temp[temp.length - 1];
    }

    const byteCharacters = atob(base64);
    const byteNumbers = new Array(byteCharacters.length);
    for (let i = 0; i < byteCharacters.length; i++) {
      byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    const byteArray = new Uint8Array(byteNumbers);
    return new Blob([byteArray], { type: mime });
  }

  function viewPdf(base64Pdf) {
    // Replace this with your base64-encoded PDF string
    //const base64Pdf = 'JVBERi0xLjQKJeLjz9MKMSAwIG9iago8PC9U...'; // shortened for example
    const mimeType = "application/pdf";
    // Convert base64 to Blob
    const pdfBlob = base64ToBlob(base64Pdf, mimeType);

    // Create a URL for the Blob
    const pdfUrl = URL.createObjectURL(pdfBlob);

    // Option 1: Open PDF in a new window
    window.open(pdfUrl, "View PDF", "width=1000,height=1000");

    // Option 2: Embed PDF in an iframe
    //const pdfViewer = document.getElementById('pdfViewer');
    //pdfViewer.innerHTML = `<iframe src="${pdfUrl}" width="100%" height="600px"></iframe>`;
  }

  function showLoading(title__, html__, timer__) {
    let timerInterval;
    Swal.fire({
      title: title__,
      html: html__,
      //timer: timer__,
      //timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
        const timer = Swal.getPopup().querySelector("b");
        timerInterval = setInterval(() => {
          //timer.textContent = `${Swal.getTimerLeft()}`;
        }, 100);
      },
      allowOutsideClick: false,
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then((result) => {});
  }

  function stickyMenuBar() {
    var $sidebar = $(".main-menu");

    if ($sidebar.length <= 0) return;

    var $window = $(window);
    var offset = $sidebar.offset();
    var topPadding = 38;

    var parent = $(".main-menu").parents(".card-body");

    $sidebar.css("z-index", 1000).css("position", "relative");
    $window.scroll(function () {
      var limited = parent.outerHeight();
      if ($window.scrollTop() > offset.top && $window.scrollTop() < limited) {
        $sidebar.stop().animate({
          top: $window.scrollTop() - offset.top + topPadding,
        });
      } else {
        $sidebar.stop().animate({
          top: 0,
        });
      }
    });
  }

  function limitDate(obj) {
    // Get the current date
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; // January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = "0" + dd;
    }
    if (mm < 10) {
      mm = "0" + mm;
    }

    today = yyyy + "-" + mm + "-" + dd;
    dateObj = document.getElementById(obj);
    if (typeof dateObj !== "undefined" && dateObj)
      dateObj.setAttribute("min", today);
  }

  function mappingDocumentFiles(fileList, documentObj) {

    fileHTML = "";
    if (!fileList || !Array.isArray(fileList)) return;
   
    iconPdf = "/wp-admin/assets/images/PDF_file_icon.svg";
    fileList.forEach(function (file) {
      const mimeType = "application/pdf";
      // Convert base64 to Blob
      const pdfBlob = base64ToBlob(file.base64, mimeType);
      // Create a URL for the Blob
      const pdfUrl = URL.createObjectURL(pdfBlob);
      // Option 1: Open PDF in a new window
      fileHTML +=
        '<li><a style="font-size: 14px; color: #000; font-weight: normal" href="' +
        pdfUrl +
        '" target="_blank">' +
        file.document_name +
        "</a></li>";
    });

    if (fileHTML) fileHTML = "<ul>" + fileHTML + "</ul>";

    documentObj.html(fileHTML);
  }

  function updateSubmitData() {
    var tabs = []; 
    formData = { 'personal' : {}, 'contact' : {}, 'loan' : {}, 'employment' : {}, 
                'bank' : {}, 'surety' : {}, 'home_address': [], 'work_address': [], 'more_files': {} };

    $('#v-pills-tabContent .tab-pane:not([id="completion"])').each(function(){
      parent = $(this);//field.parents('.tab-pane');
      id = $(this).attr('id');
      fields = $('select, input, textarea', parent);
      fields.each(function(){
        field = $(this);      
        type = field.attr('type');
        field_item = (id == 'guarantor') ? field.attr('name').replace('guarantor_info.','') : field.attr('name');
        item = checkFieldValue(tabs, field, field_item, type);
        switch(id) {
          case 'personal':
            if( field.attr('name') == 'legal_actions_against') {
              if( item.value != '__return_true') {
                formData.personal['legal_actions_against'] = item.value;
              }
            } else formData.personal[item.key] = item.value;
          break;
          case 'contact':
            contact = $(this).parents('.contact-info');
            if(contact.length > 0 ) formData.contact[item.key] = item.value;
          break;
          case 'loan':
            if( field.attr('name') == 'benefit' && item.value != '__return_true') {
              formData.loan['has_beneficial_owner'] = item.value;
            } else 
            if( field.attr('name') == 'politically' && item.value != '__return_true') {
              formData.loan['is_politically_exposed'] = item.value;
            } else 
            formData.loan[item.key] = item.value;
          break;
          case 'employment':
            if( field.attr('name') == 'income_document') {
              if( item.value != '__return_true') {
                list = [];
                if(typeof formData.employment.income_document === 'undefined')
                  list.push(item.value);
                else {
                  list = formData.employment.income_document;
                  list.push(item.value);
                }
                formData.employment['income_document'] = list;
              }
            } else formData.employment[item.key] = item.value;
          break;
          case 'bank':
            formData.bank[item.key] = item.value;
          break;
          case 'guarantor':            
            if( field_item == 'income_document') {
              if( item.value != '__return_true') {
                list = [];
                if(typeof formData.surety.income_document === 'undefined')
                  list.push(item.value);
                else {
                  list = formData.surety.income_document;
                  list.push(item.value);
                }
                formData.surety['income_document'] = list;
              }
            } else formData.surety[item.key] = item.value;
          break;
          case 'uploadfiles':
            if( field_item == 'upload_more_files') {
              const fileInput = $('input[name="upload_more_files"]');
              files = fileInput[0].files;
              if(files.length <= 0) formData.more_files['files'] = [];

              fileLists = []; // Clear previous file list
              filesPreview = [];
              $('#file-list .file-preview').each(function(){
                  fname = $(this).attr('id');
                  filesPreview.push(fname);
              });

              Array.from(files).forEach((file, index) => {
                  const reader = new FileReader();
                  let fileName = file.name.toLowerCase().replaceAll(' ','_');
                  if( filesPreview.includes(fileName)) {
                      reader.onload = function (e) {
                          const base64 = e.target.result;       
                          fileBase64 = createFile(
                            fileName,
                            e.target.result,
                            file.size,
                            file.type,
                            "files"
                          );

                          fileLists.push(fileBase64);
                      };

                      reader.onerror = function () {
                        console.log('Error reading file: ' + file.name);
                      };

                      // Convert file to base64
                      reader.readAsDataURL(file);
                  }
              });

              setTimeout(function(){
                formData.more_files['files'] = fileLists;
              }, 500);              
            }
          break;
        }  
      });
    });

    //process home address / work address
    formData.contact['home_address_type_id'] = $('#home_address_type_id').val();
    if( $('#work_address_type_id').length > 0)
    formData.contact['work_address_type_id'] = $('#work_address_type_id').val();
    $('#v-pills-tabContent .mc-accordion').each(function(){ 
      home_work = $(this).hasClass('home-address') ? 'home-address' : 'work-address';    
      $('.accordion-items .accordion-item', this).each(function(i){
        addess = {};     
        fields = $('select, input', this);
        fields.each(function(){
          field = $(this);      
          type = field.attr('type');
          field_item = (id == 'guarantor') ? field.attr('name').replace('guarantor_info.','') : field.attr('name');
          item = checkFieldValue(tabs, field, field_item, type);    
          addess[item.key] = item.value;
        });
        if( home_work == 'home-address')
          formData.home_address.push(addess);
        else
          formData.work_address.push(addess);
      });
    });
    //console.log(tabs);
    //console.log(formData);
    if(tabs.length <= 0) {
      return formData;
    } else {
      Swal.fire({
        title: "Error!",
        html: "Please complete all the fields in the sections listed below.<br />" + tabs.join(", "),
        icon: "error"
      });
      return false;
    }    
  }

  function checkFieldValue(tabs, field, field_item, type) {
    temp = {};
    h5_tags = $('> h5', parent);
    h5_title = h5_tags.text();
    isRequired = field.attr('required');
    field_name = field.attr('name');
    field_value = field.val();
    field_value = field_value.trim();
    pane_parent = field.parents('.tab-pane');
    h5_title = $('> h5', pane_parent).text();

    if( isRequired === 'required' && field_value.length <= 0) {          
      field.addClass('error');
      if(!tabs.includes(h5_title)) tabs.push(h5_title);
    }
    
    //required
    if(field_name == 'date_of_birth') {
      let age20 = calculateAge(field_value);
      if( !age20) {
        showMessage(field, age20, "You must be 20 years old to apply.");
        if(!tabs.includes(h5_title)) tabs.push(h5_title);
      }
    }

    if(field_name == 'guarantor_info.date_of_birth' && field_value.length > 0) {
      let age20 = calculateAge(field_value);
      if( !age20) {
        showMessage(field, age20, "You must be 20 years old to apply.");
        if(!tabs.includes(h5_title)) tabs.push(h5_title);
      }
    }

    if(field_name == 'identification_expiry' && field_value.length > 0) {
      let expiryDate = checkExpiryDate(field_value, 2);      
      if( !expiryDate) {
        showMessage(field, expiryDate, "The expiration date must be greater than 2 months from the current date.");
        if(!tabs.includes(h5_title)) tabs.push(h5_title);
      }
    }    

    //income
    if(field.hasClass('income') && (field_value - 0) < 0) {
      condition = (field_value - 0) < 0;
      showMessage(field, condition, "The minimum amount is 0.");
      if(!condition && !tabs.includes(h5_title)) tabs.push(h5_title);
    }
    //loan amount
    if( field_name == 'loan_amount_requested') {
      condition = (field_value - 0) > 0;
      showMessage(field, condition, "Loan Amount Required must be greater than 0");
      if(!condition && !tabs.includes(h5_title)) tabs.push(h5_title);
    }

    switch(type) {
      case 'checkbox':
        //let income  = ["payslip", "cpf", "noa", "others"];
        temp['key'] = field_item;
        if(field.is(":checked") && (field_name == 'income_document' 
          || field_name == 'guarantor_info.income_document' || field_name == 'supply_doc_offile')) {         
          temp['value'] = field_value;
        } else temp['value'] = '__return_true';
      break;
      case 'radio':       
        temp['key'] = field_item;
        if(field.is(":checked")) {
          temp['value'] = field_value;
        } else temp['value'] = "__return_true";//not select
      break;
      case 'email':
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if( !regex.test(field_value) && field_value.length > 0) {       
          if( !tabs.includes(h5_title) ) tabs.push(h5_title);
        }
        temp['key'] = field_item;
        temp['value'] = field_value;
      break;
      default:
        files = ['filePhotoValue', 'income_document_files', 'loan_interest',
          'document_files' , 'guarantor_info.income_document_files', 'cpf', 'loan_rate'];
        if(files.includes(field_name) && field_value.length > 0 ) {
          field_value = JSON.parse(field_value);
        }
        temp['key'] = field_item;
        temp['value'] = field_value;
      break;
    }

    return temp;
  }

  function fileShowRequired(obj, condition) {
    obj.css('position', 'relative');
    hasError = $('small.error', obj);
    if( hasError ) hasError.remove();
    if( condition )
    obj.append('<small class="text-danger error" style="position: absolute;bottom: 0;left: 0;top: 100%;padding: 5px 0;color: #ff0000;">File required.</small>');
  }

  function calculateInstalment() {   

    //1.	Equal Monthly Installment (EMI) = Amount * (Interest) * (1 + Interest)^(Loan term) / (((1 + Interest)^(Loan term))) - 1)
    unit = [ 'daily', 'weekly', 'biweekly', 'monthly', 'annual'];

    loan_type_id = $('#loan_type_id').val();
    loan_rate = $('input[name="loan_rate"]').val();   

    loan_rate = (loan_rate) ? JSON.parse(loan_rate) : [];
    loan_interest = loan_rate.find((x) =>  parseInt(x.type_id) ===  parseInt(loan_type_id));

    amount = $('#loan_amount_requested').val();

    term_unit = $('select[name="term_unit"]').val();
    type = unit[parseInt(term_unit )];
    insterest = loan_interest[type];

    loan_term = $('input[name="loan_terms"]').val();
    loan_term = parseInt(loan_term);
    insterest = (loan_term > 1) ? loan_interest['more_month'] : insterest;
    
    if( !loan_type_id || !loan_term || !isNumber(loan_term) || loan_term <= 0  || !amount) return;

    let instalment = 0;
     //clear
    $('input[name="installment"]').val('');
    $.ajax({
      type: "POST",
      url: mc_ajax.ajax_url,
      dataType: "json",
      data: {
        action: 'calc_repayment',
        interest_percent: insterest,
        loan_amount: amount,
        term_unit: term_unit,
        loan_term: loan_term,
        loan_type_id: loan_type_id
      },
      success: function(response) {
        if( response.data ) {
          $('input[name="installment"]').val(response.data);
          instalment = response.data;
          //if a loan with monthly repayment that is 60% of the salary
          const monthly_income = $('#step_employment .monthly_income').val();
          const percent_allow = (monthly_income > 0 ) ? (monthly_income*60)/100 : 0;
          if( instalment >= percent_allow) {
            Swal.fire({
              title: 'Loan eligibility check',
              html: 'Your instalment amount exceeds 60% of your monthly income. Please adjust the loan amount or terms to meet eligibility requirements.',
              icon: 'error'
            });

            $('#btnContinueStep4').attr('disabled', 'disabled');
          } else {
            $('#btnContinueStep4').removeAttr('disabled');
          }
        }
      },
      error: function() {
      }
    });

  }

  function validateNric(button, nricNo) {

    chckSum = nricNo.match(/^[STFGM]\d{7}[A-Z]$/);
    if(!chckSum) {
      Swal.fire({
        title: 'Singapore NRIC/FIN Validation',
        text: "The NRIC / FIN number must be a 7-digit number, please try with [STFGM]xxxxxxx[A-Z] format.",
        icon: 'error',
        allowOutsideClick: false
      });    
      button.text('Login').removeAttr('disabled');
    }

    return chckSum;
  }

  function tabSectionMobile() {
    w  = $(window).width();
    if( w > 1024) return;
    
    $('.mb-nav-tabs .close-open').click(function(){
      $('.mb-nav-tabs').toggleClass('open');
    });

    $('.nav-pills button.nav-link').click(function(){
      //$('.mb-nav-tabs .close-open').trigger('click');
      $('.mb-nav-tabs').removeClass('open');
      let id = $(this).attr('data-bs-target');
      $('html, body').animate({
        scrollTop: $(id).offset().top - 150
      }, 1000);
    });
  }

  function checkDateToSendMLCB() {

    // Define the time strings in Singapore time
    const startTime = "08:45"; // 9:45 PM in 24-hour format
    const endTime = "21:45"; // 8:45 AM in 24-hour format

    // Get the current date
    const singaporeDate = new Date().toLocaleDateString("en-CA", { timeZone: "Asia/Singapore"});
    const singaporeTime = new Date().toLocaleTimeString("en-SG", { timeZone: "Asia/Singapore", hour12: false });

    // Create Date objects for each time
    const currentDateSin = new Date(`${singaporeDate}T${singaporeTime}`);
    const startDate = new Date(`${singaporeDate}T${startTime}`); // Singapore is UTC+8
    const endDate = new Date(`${singaporeDate}T${endTime}`); // Singapore is UTC+8

    const startTimeSin = startDate.getTime();
    const currentTimeSin = currentDateSin.getTime();
    const endTimeSin = endDate.getTime();

    //const allowCheckMLCB = ( currentTimeSin < startTimeSin || currentTimeSin > endTimeSin);
    //8:45 AM  -> 21:45 PM - OK
    const allowCheckMLCB = ( startTimeSin <= currentTimeSin && currentTimeSin <= endTimeSin);
    return allowCheckMLCB;
    //document.getElementById('demo').innerHTML = allowCheckMLCB;
  }

  //upload multiple files
  function stepContinueStep8() {
    $('#btnContinueStep8').click(function(){
      update_next_step(
        "uploadfiles",
        "complete",
        "btoa(jsonString)",
        "completion"
      );
    });
  }
})(jQuery);
