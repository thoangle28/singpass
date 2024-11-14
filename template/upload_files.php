<div class="accordion mb-3" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header m-0" id="headingOne">
      <button class="accordion-button w-100 border-0" style="text-align: left; text-wrap-mode: wrap" title="Click here to show/hide"
      type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
        + The following documents may be required:
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body" id="upload_info">        
        <p>Please ensure you upload all necessary supporting documents to complete your loan application.</p>
        <div class="row">
            <div class="col-12 mb-3 col-md-6">
                <p><strong>Document Checklist - Personnal</strong></p>
                <ul>
                    <li>NRIC (front/back or digital IC)</li>
                    <li>Income Proof (Latest 3 months payslip or CPF Statement / Latest NOA)</li>
                    <li>Proof of Residence (Rental Agreement or HDB Flat Details or Property Summary from IRAS)</li>
                </ul>
            </div>
            <div class="col-12 mb-3 col-md-6">
                <p><strong>Document Checklist - Business</strong></p>
                <ul>
                    <li>ARCA</li>
                    <li>3 months company bank statement</li>
                    <li>Tenancy Agreement</li>
                    <li>Proof of Business Address (Telco Bill etc)</li>
                    <li>M&A</li>
                    <li>Government License (if any)</li>
                    <li>NRIC of Business Owner (front/back or digital IC)</li>
                </ul>
            </div>
        </div>
        <p>Uploading all relevant documents helps speed up the review and approval process. Thank you for your cooperation!</p>
      </div>
    </div>
  </div>
</div>

<div id="drag-drop-area" class="p-3 p-md-5">
    <div class="row">
        <div class="col-12 col-md-7 text-center">
            <img style="width: 64px;" src="<?php print PLUGIN_DIR_URL . "assets/images/1562155-512.png"; ?>" />
            <p>Drag & drop your files here</p>
            <p>OR</p>
            <label class="mb-3 btn btn-primary" type="button" for="file-input">Click to upload</label>
            <p ><small>Only PDF or image files (JPEG, PNG,...) are allowed.</small></p>
            <input type="file" id="file-input" multiple class="d-none" name="upload_more_files">
        </div>
        <div class="col-12 col-md-5 text-left">
            <h6>Files list </h6>
            <div class="mt-4" id="file-list"></div>
        </div>
    </div>
</div>

<div class="row mt-3" id="continue-upload">
  <div class="col-12 col-md-12 col-lg-6">
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="d-flex-end gap-3 mb-form-submit">
      <button class="btn btn-primary px-4" id="btnContinueStep8">Continue</button>
    </div>
  </div>
</div>
<style>
    #step_uploadfiles ul li,
    #upload_info ul li {
        margin: 5px 0;
        font-size: 14px;
    }
    .accordion-item button,
    #upload_info p {
        font-size: 14px !important;
        line-height: 18px;
    }
    #drag-drop-area {
        border: 1px dashed #ccc; 
        background: var(--light_blue);
        border-radius: 5px;
    }
    label.btn-primary {
        display: flex;
        width: 150px;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    label.btn-primary,
    #step_uploadfiles button.btn {
        background-color: var(--active_blue) !important;
        color: #fff;        
        border: 0 !important; 
        cursor: pointer;
        transition: all .5s;
    }
    #step_uploadfiles button.btn:hover {
        opacity: 0.8;
    }
    #step_uploadfiles button.btn label {
        cursor: pointer;
    }
    #step_uploadfiles .file-preview {
        align-items: flex-start;
    }

    #file-list {
        height: 180px;
        overflow: auto;
    }
    #file-list .file-preview .delete {
        width: 32px;
        height: 18px;
        background-size: 16px;
        background-position: left center;
        margin-top: 2px;
    }
    @media (max-width: 767px) {
        #file-list {
            height: 100px;
        }
        #step_uploadfiles .file-preview {
            align-items: flex-start;
        }
        #continue-upload .mb-form-submit {
            justify-content: right;
        }
        #file-list .file-preview .delete {
            width: 32px;
        }
    }
</style>
<script>
(function($){
    $(document).ready(function () {
        const dragDropArea = $('#drag-drop-area');
        const fileInput = $('#file-input');
        const fileList = $('#file-list');
        // Open file selector on click
        //dragDropArea.on('click', () => fileInput.click());

        // Handle file selection
        fileInput.on('change', function () {
            processFiles(this.files);
        });

        // Handle drag-and-drop events
        dragDropArea.on('dragover', function (e) {
            e.preventDefault();
            $(this).css('border-color', '#1B84FF');
        });

        dragDropArea.on('dragleave', function () {
            $(this).css('border-color', '#ccc');
        });

        dragDropArea.on('drop', function (e) {
            e.preventDefault();
            $(this).css('border-color', '#ccc');
            const files = e.originalEvent.dataTransfer.files;
            processFiles(files);
        });

        // Process files and convert to base64
        function processFiles(files) {
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
            let valid = true;
            // Check each selected file
            Array.from(files).forEach(file => {
                if (!validTypes.includes(file.type)) {
                    valid = false;
                }
            });
            
            if (!valid) {
                Swal.fire({
                    title: "Upload files",
                    html: "Please choose a PDF or image file (JPG, PNG) to proceed.",
                    icon: "error",
                });
                fileInput.val("");
                return;
            };

            fileList.empty(); // Clear previous file list
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                let fileName = file.name.toLowerCase().replaceAll(' ','_');

                reader.onload = function (e) {
                    const base64 = e.target.result;
                    const size = formatFileSize(file.size);
                    // Display file information
                    fileList.append(`<div id="${fileName}" class="file-preview d-flex gap-2 mb-3"><span data-fname="${fileName}" data-item="${index}" title="Click to remove" class="delete"></span> ` 
                                        + `<span style="font-size: 14px; overflow: hidden; text-wrap-mode: wrap;">${file.name}(${size})</span></div>`);
                };

                reader.onerror = function () {
                    alert('Error reading file: ' + file.name);
                };

                // Convert file to base64
                reader.readAsDataURL(file);
            });
            setTimeout(function(){
                removeFiles();
            }, 1000)
            
        }
        
        // Format file size to KB or MB
        function formatFileSize(bytes) {
            if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
            } else {
            return bytes + ' bytes';
            }
        }

        function removeFiles() {
           
            $('#file-list .delete').click(function(){
                parent = $(this).parent();
                console.log(parent);
                parent.remove();
                if( $('#file-list .delete').length <= 0 ) fileInput.val('');
            });
        }
    });
})(jQuery);
</script>