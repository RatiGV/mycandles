/********************** რედაქტირების გვერდების javascript **********************/
/********************** რედაქტირების გვერდების javascript **********************/
/********************** რედაქტირების გვერდების javascript **********************/
/********************** რედაქტირების გვერდების javascript **********************/
/********************** რედაქტირების გვერდების javascript **********************/

/*************************** Ajaxs ოპერაციები **********************************/

// მიმაგრებული ფაილის წაშლა
$('.remove-file').click(function(e) {
    
    e.preventDefault();
    let _this = $(this);
    let id = $(this).data('id');
    let table = $(this).data('table');
    let imgOrNo = $(this).parent().prev().find('.img-or-no');
    let column = $(this).parent().prev().find('input[type=file]').attr('name');
    
    bootbox.confirm({
        message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
        buttons: {
            confirm: {
                label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
            },
            cancel: {
                label: no,
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result) 
            {
                $.ajax({
                    url: "/admin/remove_file",
                    type: 'post',
                    dataType: 'json',
                    data: {id: id, table: table, column: column}
                }).done(function (data) {
                    
                    new PNotify({
                        text: data.text,
                        type: data.type,
                        styling: 'bootstrap3'
                    });
                    
                    if (data.status == 1) 
                    {
                        imgOrNo.html("<div class='alert alert-warning' style='margin-top: 40px;'>"+ notUploaded +"</div>");  
                        _this.fadeOut();
                    } 
                    
                });
            }
        }
    });
    
});    

// ფოტოს წაშლა გალერიიდან 
$('.remove-image').click(function(e) {
    
    e.preventDefault();
    let _this = $(this);
    let imageID = $(this).data('id');
    let galleryTable = $(this).data('gallery-table');
    
    bootbox.confirm({
        message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
        buttons: {
            confirm: {
                label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
            },
            cancel: {
                label: no,
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result) 
            {
                $.ajax({
                    url: "/admin/remove_image_from_gallery",
                    type: 'post',
                    dataType: 'json',
                    data: {image_id: imageID, gallery_table: galleryTable}
                }).done(function (data) {
                    
                    new PNotify({
                        text: data.text,
                        type: data.type,
                        styling: 'bootstrap3'
                    });
                    
                    if (data.status == 1) 
                    {
                        $(_this).parents('.col-md-4')[0].remove();
                    } 
                    
                });
            }
        }
    });

});  

// ვიდეოს წაშლა გალერიიდან 
$('.remove-video').click(function(e) {
    
    e.preventDefault();
    let _this = $(this);
    let videoID = $(this).data('id');
    let galleryTable = $(this).data('gallery-table');
    
    bootbox.confirm({
        message: areYouSure, // ცვლადი აღწერილია layouts/admin.blade.php-ში
        buttons: {
            confirm: {
                label: yes, // ცვლადი აღწერილია layouts/admin.blade.php-ში
                className: 'btn-success' // ცვლადი აღწერილია layouts/admin.blade.php-ში
            },
            cancel: {
                label: no,
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result) 
            {
                $.ajax({
                    url: "/admin/remove_video_from_gallery",
                    type: 'post',
                    dataType: 'json',
                    data: {video_id: videoID, gallery_table: galleryTable}
                }).done(function (data) {
                    
                    new PNotify({
                        text: data.text,
                        type: data.type,
                        styling: 'bootstrap3'
                    });
                    
                    if (data.status == 1) 
                    {
                        $(_this).parents('.col-md-4')[0].remove();
                    } 
                    
                });
            }
        }
    });

});    


/*************************** /Ajaxs ოპერაციები *********************************/

/********************************* სხვადასხვა **********************************/

// ენების გადამრთველების მართვა რედქტირების გვერდზე
$('.lang-switcher').on('click',function(){
    $('#lat-edited-lang-inp').val($(this).data('lang'));
});

$('.lang-switcher').each(function(){
    if($(this).data('lang') == activeLang)
    {
        $(this).parent().addClass('active');
    }
});

$('.tab-pane').each(function(){
    if($(this).attr('id') == activeLang)
    {
        $(this).addClass('active');
    }
});
// ენების გადამრთველების მართვა რედქტირების გვერდზე



// ფოტო გალერეების მართვა 
const fileInput = document.getElementById('fileUp');
const myUl = document.getElementById('upload__file-ls');

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if(bytes === 0) { return '0 Byte'; }
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

if(fileInput) {
    fileInput.addEventListener('change', function () {
        myUl.innerHTML = "";
        for(let i = 0; i < this.files.length; i++) {
            let urlImg = URL.createObjectURL(this.files[i]);
            const newLi = document.createElement('li');
            const temp = `<li class='upload__file-it'>
                            <img src='${ urlImg }' alt='' class='upload__file-img'>
                            <div class='upload__file-desc'>
                                <span class='upload__file-name'>${ this.files[i].name }</span>
                                <span class='upload__file-size'>${ bytesToSize(this.files[i].size) }</span>
                                <span class='upload__file-remove'></span>
                            </div>
                         </li>`;
            newLi.innerHTML = temp;
            myUl.appendChild(newLi);
        }
    }, false);    
}


$(document).on("click",".upload__file-remove",function() {
    $(this).parent().closest('.upload__file-it').remove();
});    
// ფოტო გალერეების მართვა 



// ვიდეო გალერეების მართვა 
$(document).on('click', '.hide-new-video', function(){

    $(this).parent().parent().fadeOut().remove();
    return false;

});

$("#add-video-link").on('click',function(){

    let newVideo = `<div class="col-md-4">
                        <div class="thumbnail video-thumbnail">
                            <div class="iframe-div">
                                <i class="fab fa-youtube"></i>
                            </div>
                            <input type="text" name="videos[]" class="form-control" placeholder="Youtube-ს ბმული">
                            <a href="" class="hide-new-video">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>`;

    $("#new-videos").append(newVideo);

    return false;

});
// ვიდეო გალერეების მართვა 




// დავრჩეთ იგივე გვერდზე ან გადავიდეთ მთავარზე რედაქტირების შემდეგ
$('.save-btn').on('click', function(){
    $('#stay-input').val($(this).data('stay'));
    $('#news-form').submit();
});

/********************************* /სხვადასხვა *********************************/