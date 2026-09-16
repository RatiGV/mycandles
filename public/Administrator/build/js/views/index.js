/********************** ჩამონათვალის გვერდების javascript **********************/
/********************** ჩამონათვალის გვერდების javascript **********************/
/********************** ჩამონათვალის გვერდების javascript **********************/
/********************** ჩამონათვალის გვერდების javascript **********************/
/********************** ჩამონათვალის გვერდების javascript **********************/

/*************************** Ajaxs ოპერაციები **********************************/

// integer ტიპის ისეთი ველების განახლება, რომელთა შესაძლო მნიშვნელობებიცაა 0 და 1
$('.change').change(function (e) {

    let id = $(this).data('id');
    let table = $(this).data('table');
    let column = $(this).data('column');

    $.ajax({
        url: "/admin/status",
        type: 'post',
        dataType: 'json',
        data: {id: id, table: table, column: column}
    }).done(function (data){
        
        new PNotify({
            text: data.text,
            type: data.type,
            styling: 'bootstrap3'
        });
        
    });
});

// წაშლა
$('.delete').click(function (event) {
    
    event.preventDefault();
    
    let _this = $(this);
    let id = $(this).data('id');
    let table = $(this).data('table');
    let checkChildsHere = $(this).data('check-childs-here');
    
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
                    url: "/admin/remove",
                    type: 'post',
                    dataType: 'json',
                    data: {id: id, table: table, check_childs_here: checkChildsHere}
                }).done(function (data) {

                    new PNotify({
                        text: data.text,
                        type: data.type,
                        styling: 'bootstrap3'
                    });
                    
                    if (data.status == 1) 
                    {
                        _this.parents('tr').hide().remove();
                    } 
                    
                });
            }
        }
    });
   
});

// თანმიმდევრობის შეცვლა ჩამონათვალის გვერდზე
function changeordering(instance)
{
    let arr = [];
    let multi = $('.sort');

    for (var i = 0; i < multi.length; i++)
    {
        arr.push([$('#sort' + i).attr('data-id'), $('#sort' + i).attr('data-ordering')]);
    }

    arr = JSON.stringify(arr);

    $.ajax({
        url: "/admin/ordering",
        type: "POST",
        data: {ordering: arr, table: instance}
    })
    .done(function (data) {

        new PNotify({
            text: data.text,
            type: data.type,
            styling: 'bootstrap3'
        });

    });
}

/*************************** /Ajaxs ოპერაციები *********************************/

/********************************* სხვადასხვა **********************************/
   
$(document).ready(function () {

    // ელემენტთა ინდექსების გადანაცვლებები თანმიმდევრობის შეცვლისას
    $k = 0;
    $('.admin_container tbody').sortable({
        start: function (event, ui) {

            var start_pos = ui.item.index();
            ui.item.data('start_pos', start_pos);
        },
        update: function (event, ui) {
            var index = ui.item.index();
            var start_pos = ui.item.data('start_pos');

            //update the html of the moved item to the current index
            $('.admin_container tbody tr:nth-child(' + (index + 1) + ') .sort').html(index + $k + 1).attr('data-ordering', index + $k + 1);

            if (start_pos < index)
            {
                //update the items before the re-ordered item
                for (var i = index; i > 0; i--)
                {
                    $('.admin_container tbody tr:nth-child(' + i + ') .sort').html(i + $k).attr('data-ordering', i + $k);

                }
            } else {
                //update the items after the re-ordered item
                for (var i = index + 2; i <= $(".admin_container tbody tr").length; i++)
                {
                    $('.admin_container tbody tr:nth-child(' + i + ') .sort').html(i + $k).attr('data-ordering', i + $k);
                }
            }
            changeordering($('.admin_container').data('instance'));
        }
    });
    
});

// DataTable ცხრილის ინიციალიზაცია
$(document).ready(function() {
    
    $('#table').DataTable();

    // ყველას მონიშნვა checkbox-ები
    $('#exampleCheck1').on('click', function(){
        $('input:checkbox').prop('checked', this.checked);
    });
    
    $('.multi-check').on('change', function(){
            
        if($('.chk:checked').length > 0)
        {
            $('.multi-action-btn').fadeIn();
        }
        else
        {
            $('.multi-action-btn').fadeOut();
        }
    });

    // მოთხოვნის გაგზავნა მონიშნული ჩანაწერებისათვის
    $('.multi-action-btn').on('click', function(e){

        e.preventDefault();
        let action = $(this).data('action');
        let checkChildsHere = $(this).data('check-childs-here');

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
                    $('#action').val(action);
                    $('#multi-form').submit();
                }
            }
        });

    });

    // ფილტრების ველების სიგანე
    let formGroupsCount = $('#filter-form .form-group').length;
    let widthInPercents = 100 / formGroupsCount;
    $('#filter-form .form-group').css('width', widthInPercents - 0.5 + '%');

});

/********************************* /სხვადასხვა *********************************/