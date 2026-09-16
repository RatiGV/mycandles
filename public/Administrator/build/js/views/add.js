/********************** დამატების გვერდების javascript **********************/
/********************** დამატების გვერდების javascript **********************/
/********************** დამატების გვერდების javascript **********************/
/********************** დამატების გვერდების javascript **********************/
/********************** დამატების გვერდების javascript **********************/

/*************************** Ajaxs ოპერაციები **********************************/



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

/********************************* /სხვადასხვა *********************************/